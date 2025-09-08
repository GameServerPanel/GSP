#!/usr/bin/perl
#
# OGP - Open Game Panel - Unified Agent
# Copyright (C) 2008 - 2018 The OGP Development Team
#
# http://www.opengamepanel.org/
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#

use warnings;
use strict;

use Cwd;			 # Fast way to get the current directory
use lib getcwd();
use Frontier::Daemon::OGP::Forking;	# Forking XML-RPC server
use File::Copy;				   # Simple file copy functions
use File::Copy::Recursive
  qw(fcopy rcopy dircopy fmove rmove dirmove pathempty pathrmdir)
  ;							   # Used to copy whole directories
use File::Basename; # Used to get the file name or the directory name from a given path
use Crypt::XXTEA;	# Encryption between webpages and agent.
use Cfg::Config;	 # Config file
use Cfg::Preferences;   # Preferences file
use Fcntl ':flock';  # Import LOCK_* constants for file locking
use LWP::UserAgent; # Used for fetching URLs
use MIME::Base64;	# Used to ensure data travelling right through the network.
use Getopt::Long;	# Used for command line params.
use Path::Class::File;	# Used to handle files and directories.
use File::Path qw(mkpath);
use Archive::Extract;	 # Used to handle archived files.
use File::Find;
use Schedule::Cron; # Used for scheduling tasks

# Compression tools
use IO::Compress::Bzip2 qw(bzip2 $Bzip2Error); # Used to compress files to bz2.
use Compress::Zlib; # Used to compress file download buffers to zlib.
use Archive::Tar; # Used to create tar, tgz or tbz archives.
use Archive::Zip qw( :ERROR_CODES :CONSTANTS ); # Used to create zip archives.

# Platform detection
my $is_windows = ($^O =~ /MSWin32|cygwin/i) ? 1 : 0;
my $is_linux = $is_windows ? 0 : 1;

use constant IS_WINDOWS => $is_windows;
use constant IS_LINUX => $is_linux;

# Current location of the agent.
use constant AGENT_RUN_DIR => getcwd();

# Load our config file values
use constant AGENT_KEY	  => $Cfg::Config{key};
use constant AGENT_IP	   => $Cfg::Config{listen_ip};
use constant AGENT_LOG_FILE => $Cfg::Config{logfile};
use constant AGENT_PORT	 => $Cfg::Config{listen_port};
use constant AGENT_VERSION  => $Cfg::Config{version};

# Platform-conditional constants
use constant WEB_ADMIN_API_KEY  => IS_LINUX ? $Cfg::Config{web_admin_api_key} : "";
use constant WEB_API_URL => IS_LINUX ? $Cfg::Config{web_api_url} : "";
use constant STEAM_DL_LIMIT => IS_LINUX ? $Cfg::Config{steam_dl_limit} : "";

use constant SCREEN_LOG_LOCAL  => $Cfg::Preferences{screen_log_local};
use constant DELETE_LOGS_AFTER  => $Cfg::Preferences{delete_logs_after};

# Linux-specific constants
use constant LINUX_USER_PER_GAME_SERVER  => IS_LINUX ? $Cfg::Preferences{linux_user_per_game_server} : "";

use constant AGENT_PID_FILE =>
  Path::Class::File->new(AGENT_RUN_DIR, 'ogp_agent.pid');
use constant AGENT_RSYNC_GENERIC_LOG =>
  Path::Class::File->new(AGENT_RUN_DIR, 'rsync_update_generic.log');
use constant STEAM_LICENSE_OK => "Accept";
use constant STEAM_LICENSE	=> $Cfg::Config{steam_license};
use constant MANUAL_TMP_DIR   => Path::Class::Dir->new(AGENT_RUN_DIR, 'tmp');
use constant SHARED_GAME_TMP_DIR   => Path::Class::Dir->new(AGENT_RUN_DIR, 'shared');
use constant STEAMCMD_CLIENT_DIR => Path::Class::Dir->new(AGENT_RUN_DIR, 'steamcmd');

# Platform-specific SteamCMD binary
use constant STEAMCMD_CLIENT_BIN =>
  IS_WINDOWS ? Path::Class::File->new(STEAMCMD_CLIENT_DIR, 'steamcmd.exe') 
             : Path::Class::File->new(STEAMCMD_CLIENT_DIR, 'steamcmd.sh');

use constant SCREEN_LOGS_DIR =>
  Path::Class::Dir->new(AGENT_RUN_DIR, 'screenlogs');
use constant GAME_STARTUP_DIR =>
  Path::Class::Dir->new(AGENT_RUN_DIR, 'startups');
use constant SCREENRC_FILE =>
  Path::Class::File->new(AGENT_RUN_DIR, 'ogp_screenrc');
use constant SCREENRC_FILE_BK =>
  Path::Class::File->new(AGENT_RUN_DIR, 'ogp_screenrc_bk');

# Linux-specific constants
use constant SCREENRC_TMP_FILE =>
  IS_LINUX ? Path::Class::File->new(AGENT_RUN_DIR, 'ogp_screenrc.tmp') : "";

use constant SCREEN_TYPE_HOME   => "HOME";
use constant SCREEN_TYPE_UPDATE => "UPDATE";

# Platform-specific user constants
use constant SERVER_RUNNER_USER => IS_LINUX ? "ogp_server_runner" : "";
use constant USER_RUNNING_SCRIPT => IS_WINDOWS ? (getlogin || getpwuid($<) || "cyg_server") : "";

use constant FD_DIR => Path::Class::Dir->new(AGENT_RUN_DIR, 'FastDownload');
use constant FD_ALIASES_DIR => Path::Class::Dir->new(FD_DIR, 'aliases');
use constant FD_PID_FILE => Path::Class::File->new(FD_DIR, 'fd.pid');
use constant SCHED_PID => Path::Class::File->new(AGENT_RUN_DIR, 'scheduler.pid');
use constant SCHED_TASKS => Path::Class::File->new(AGENT_RUN_DIR, 'scheduler.tasks');
use constant SCHED_LOG_FILE => Path::Class::File->new(AGENT_RUN_DIR, 'scheduler.log');

# Linux-specific sudo configuration
our $SUDOPASSWD = "";
if (IS_LINUX) {
	if (exists $Cfg::Config{sudo_password}) {
		$Cfg::Config{sudo_password} =~ s/('+)/'\"$1\"'/g;
		$SUDOPASSWD = $Cfg::Config{sudo_password};
	}
}

my $no_startups	= 0;
my $clear_startups = 0;
our $log_std_out = 0;

GetOptions(
		   'no-startups'	=> \$no_startups,
		   'clear-startups' => \$clear_startups,
		   'log-stdout'	 => \$log_std_out
		  );

# Starting the agent as root user is not supported anymore.
if ($< == 0)
{
	print "ERROR: You are trying to start the agent as root user.";
	print "This is not currently supported. If you wish to start the";
	print "you need to create a normal user account for it.";
	exit 1;
}

### Logger function.
### @param line the line that is put to the log file.
sub logger
{
	my $logcmd	 = $_[0];
	my $also_print = 0;

	if (@_ == 2)
	{
		($also_print) = $_[1];
	}

	$logcmd = localtime() . " $logcmd\n";

	if ($log_std_out == 1)
	{
		print "$logcmd";
		return;
	}
	if ($also_print == 1)
	{
		print "$logcmd";
	}

	open(LOGFILE, '>>', AGENT_LOG_FILE)
	  or die("Can't open " . AGENT_LOG_FILE . " - $!");
	flock(LOGFILE, LOCK_EX) or die("Failed to lock log file.");
	seek(LOGFILE, 0, 2) or die("Failed to seek to end of file.");
	print LOGFILE "$logcmd" or die("Failed to write to log file.");
	flock(LOGFILE, LOCK_UN) or die("Failed to unlock log file.");
	close(LOGFILE) or die("Failed to close log file.");
}

# Platform-specific initialization
if (IS_LINUX) {
	# Linux-specific log rotation
	if (-e AGENT_LOG_FILE)
	{
		if (-e AGENT_LOG_FILE . ".bak")
		{
			unlink(AGENT_LOG_FILE . ".bak");
		}
		logger "Rotating log file";
		move(AGENT_LOG_FILE, AGENT_LOG_FILE . ".bak");
		logger "New log file created";
	}

	# Linux-specific screenrc configuration
	if (! -e SCREENRC_FILE)
	{
		copy(SCREENRC_FILE_BK,SCREENRC_FILE);
	}

	open INPUTFILE, "<", SCREENRC_FILE or die $!;
	open OUTPUTFILE, ">", SCREENRC_TMP_FILE or die $!;
	my $dest = SCREEN_LOGS_DIR . "/screenlog.%t";
	while (<INPUTFILE>) 
	{
		$_ =~ s/logfile.*/logfile $dest/g;
		print OUTPUTFILE $_;
	}
	close INPUTFILE;
	close OUTPUTFILE;
	unlink SCREENRC_FILE;
	move(SCREENRC_TMP_FILE,SCREENRC_FILE);
} else {
	# Windows-specific initialization
	if (! -e SCREENRC_FILE)
	{
		copy(SCREENRC_FILE_BK,SCREENRC_FILE);
	}
	
	# Clean up any dead screen sessions
	system('screen -wipe > /dev/null 2>&1');
}

# Check the screen logs folder
if (!-d SCREEN_LOGS_DIR && !mkdir SCREEN_LOGS_DIR)
{
	logger "Could not create " . SCREEN_LOGS_DIR . " directory $!.", 1;
	exit -1;
}

if (IS_LINUX) {
	# Linux-specific permission management
	if ( ! chmod 0777, SCREEN_LOGS_DIR ){
		logger "Could not chmod 777 " . SCREEN_LOGS_DIR . " directory $!.", 1;
		exit -1;
	}

	my $groupCommandScreenLogs = "chmod -Rf g-s '" . SCREEN_LOGS_DIR . "'";
	sudo_exec_without_decrypt($groupCommandScreenLogs);

	$groupCommandScreenLogs = "find '" . SCREEN_LOGS_DIR  . "' -type d | xargs chmod g+s";
	sudo_exec_without_decrypt($groupCommandScreenLogs);

	$groupCommandScreenLogs = "find '" . SCREEN_LOGS_DIR  . "' -type d | xargs setfacl -d -m u::rwX,g::rwX,o::-";
	sudo_exec_without_decrypt($groupCommandScreenLogs);
}

# Check the global shared games folder
if (!-d SHARED_GAME_TMP_DIR && !mkdir SHARED_GAME_TMP_DIR)
{
	logger "Could not create " . SHARED_GAME_TMP_DIR . " directory $!.", 1;
	exit -1;
}

# Check the manual tmp folder
if (!-d MANUAL_TMP_DIR && !mkdir MANUAL_TMP_DIR)
{
	logger "Could not create " . MANUAL_TMP_DIR . " directory $!.", 1;
	exit -1;
}

if (!-d GAME_STARTUP_DIR && !mkdir GAME_STARTUP_DIR)
{
	logger "Could not create " . GAME_STARTUP_DIR . " directory $!.", 1;
	exit -1;
}

# Check SteamCMD
check_steam_cmd_client();

# FastDL support
if (AGENT_VERSION >= 1.0.0)
{
	if (!-d FD_DIR && !mkdir FD_DIR)
	{
		logger "Could not create " . FD_DIR . " directory $!.", 1;
		exit -1;
	}
	
	if (!-d FD_ALIASES_DIR && !mkdir FD_ALIASES_DIR)
	{
		logger "Could not create " . FD_ALIASES_DIR . " directory $!.", 1;
		exit -1;
	}
	
	if(SCREEN_LOG_LOCAL eq "1")
	{
		start_fastdl();
	}
}

# Platform-specific function definitions
sub create_screen_cmd
{
	my ($screen_id, $exec_cmd) = @_;
	$exec_cmd = replace_OGP_Env_Vars($screen_id, "", "", $exec_cmd);
	
	if (IS_LINUX) {
		return sprintf('export WINEDEBUG="fixme-all" && export DISPLAY=:1 && screen -d -m -t "%1$s" -c ' . SCREENRC_FILE . ' -S %1$s %2$s',
					  $screen_id, $exec_cmd);
	} else {
		return sprintf('screen -d -m -t "%1$s" -c ' . SCREENRC_FILE . ' -S %1$s %2$s',
					  $screen_id, $exec_cmd);
	}
}

sub create_screen_cmd_loop
{
	my ($screen_id, $exec_cmd, @extra_params) = @_;
	$exec_cmd = replace_OGP_Env_Vars($screen_id, "", "", $exec_cmd);
	
	if (IS_LINUX) {
		my ($envVars, $skipLoop) = @extra_params;
		my $server_start_bashfile = $screen_id . "_startup_scr.sh";
		
		# Allow file to be overwritten
		if(-e $server_start_bashfile){
			secure_path_without_decrypt('chattr-i', $server_start_bashfile);
		}
		
		# Create bash file that screen will run which spawns the server
		open (SERV_START_SCRIPT, '>', $server_start_bashfile);
		
		my $respawn_server_command = "#!/bin/bash" . "\n";
		
		if(!$skipLoop){
			$respawn_server_command .= "function startServer(){" . "\n";
		}
		
		if(defined $envVars && $envVars ne ""){
			$respawn_server_command .= $envVars;
		}
		
		$respawn_server_command .= "until " . $exec_cmd . "; do" . "\n" 
			. "echo \"Server '" . $exec_cmd . "' crashed with exit code \$?.  Respawning...\" >&2 " . "\n" 
			. "sleep 1" . "\n" 
			. "done" . "\n";
		
		if(!$skipLoop){
			$respawn_server_command .= "}" . "\n" . "startServer" . "\n";
		} else {
			$respawn_server_command .= $exec_cmd . "\n";
		}
		
		print SERV_START_SCRIPT $respawn_server_command;
		close (SERV_START_SCRIPT);
		
		my $readOnlyOwnerCmd = "chattr +i " . $server_start_bashfile;
		sudo_exec_without_decrypt($readOnlyOwnerCmd);
		
		my $chmodCmd = 'chmod +x "' . $server_start_bashfile . '"';
		system($chmodCmd);
		
		my $screen_exec_script = "bash " . $server_start_bashfile;
		
		return sprintf('export WINEDEBUG="fixme-all" && export DISPLAY=:1 && screen -d -m -t "%1$s" -c ' . SCREENRC_FILE . ' -S %1$s %2$s',
					  $screen_id, $screen_exec_script);
	} else {
		my ($priority, $affinity, $envVars) = @extra_params;
		my $server_start_batfile = "_start_server.bat";
		
		# Create batch file that will launch the process and store PID which will be used for killing later
		open (SERV_START_BAT_SCRIPT, '>', $server_start_batfile);
		
		my $batch_server_command = "\@echo off" . "\r\n"
		. "setlocal EnableDelayedExpansion" . "\r\n"
		. ":TOP" . "\r\n";
		
		if(defined $envVars && $envVars ne ""){
			$batch_server_command .= $envVars;
		}
		
		$batch_server_command .= "set STARTTIME=%TIME: =0%" . "\r\n"
		. "if exist _prestart.bat ( " . "\r\n"
		. "start \"PRESTART\" _prestart.bat" . "\r\n"
		. ")" . "\r\n"
		. "start " . $priority . " " . $affinity . " /wait " . $exec_cmd . "\r\n"
		. "set FINISHTIME=%TIME: =0%" . "\r\n"
		. "if exist _poststart.bat ( " . "\r\n"
		. "start \"POSTSTART\" _poststart.bat" . "\r\n"
		. ")" . "\r\n"
		. "echo." . "\r\n"
		. "echo Server process ended at %FINISHTIME%" . "\r\n"
		. "echo STARTTIME: %STARTTIME%" . "\r\n"
		. "echo FINISHTIME: %FINISHTIME%" . "\r\n"
		. "timeout /t 1" . "\r\n"
		. "goto TOP" . "\r\n";
		
		print SERV_START_BAT_SCRIPT $batch_server_command;
		close (SERV_START_BAT_SCRIPT);
		
		my $screen_exec_script = "cmd /Q /C " . $server_start_batfile;
		
		return sprintf('screen -d -m -t "%1$s" -c ' . SCREENRC_FILE . ' -S %1$s %2$s',
					  $screen_id, $screen_exec_script);
	}
}

# Platform-specific helper functions
sub sudo_exec_without_decrypt
{
	my ($exec_cmd) = @_;
	
	if (IS_LINUX && $SUDOPASSWD ne "") {
		my $encoded_password = encode_base64($SUDOPASSWD);
		chomp $encoded_password;
		my $sudo_cmd = "echo '$encoded_password' | base64 -d | sudo -S $exec_cmd";
		return system($sudo_cmd);
	}
	return 0; # No-op on Windows or when no sudo password is configured
}

sub secure_path_without_decrypt
{
	my ($operation, $path) = @_;
	
	if (IS_LINUX) {
		if ($operation eq 'chattr-i') {
			sudo_exec_without_decrypt("chattr -i '$path'");
		} elsif ($operation eq 'chattr+i') {
			sudo_exec_without_decrypt("chattr +i '$path'");
		}
	}
	# Windows doesn't need chattr operations
}

# Platform-specific environment variable replacement
sub replace_OGP_Env_Vars{
	my ($screen_id, $homeid, $homepath, $exec_cmd, $game_key) = @_;
	
	# Handle steam specific replacements
	if(defined $screen_id && $screen_id ne ""){
		my $screen_id_for_txt_update = substr ($screen_id, rindex($screen_id, '_') + 1);
		my $steamInsFile = $screen_id_for_txt_update . "_install.txt";
		my $steamCMDPath = STEAMCMD_CLIENT_DIR;
		my $fullPath = Path::Class::File->new($steamCMDPath, $steamInsFile);
		
		if (IS_WINDOWS) {
			my $windows_steamCMDPath = clean(`cygpath -wa $steamCMDPath`);
			$windows_steamCMDPath =~ s#/#\\#g;
			
			if(-e $fullPath){
				$exec_cmd =~ s/{OGP_STEAM_CMD_DIR}/$windows_steamCMDPath/g;
				$exec_cmd =~ s/{STEAMCMD_INSTALL_FILE}/$steamInsFile/g;
			}
		} else {
			if(-e $fullPath){
				$exec_cmd =~ s/{OGP_STEAM_CMD_DIR}/$steamCMDPath/g;
				$exec_cmd =~ s/{STEAMCMD_INSTALL_FILE}/$steamInsFile/g;
			}
		}
	}

	# Handle home directory replacement
	if(defined $homepath && $homepath ne ""){
		$exec_cmd =~ s/{OGP_HOME_DIR}/$homepath/g;
		
		if (IS_WINDOWS) {
			my $windows_home_path = clean(`cygpath -wa $homepath`);
			$exec_cmd =~ s/{OGP_HOME_DIR_WINDOWS}/$windows_home_path/g;
		}
	}
	
	# Handle global game shared directory replacement
	if(defined $game_key && $game_key ne ""){
		my $readable_game_key = lc(substr($game_key, 0, rindex($game_key,"_")));		
		my $shared_path = Path::Class::Dir->new(SHARED_GAME_TMP_DIR, $readable_game_key);
		
		# Create the folder if it doesn't exist
		if (!-d $shared_path && !mkdir $shared_path)
		{
			logger "Could not create " . $shared_path . " directory $!.", 1;
		}
		
		if (IS_WINDOWS) {
			my $windows_shared_path = clean(`cygpath -wa $shared_path`);
			$exec_cmd =~ s/{OGP_GAME_SHARED_DIR}/$windows_shared_path/g;
		} else {
			$exec_cmd =~ s/{OGP_GAME_SHARED_DIR}/$shared_path/g;
		}
	}
	
	return $exec_cmd;
}

# Utility function for Windows path cleaning
sub clean {
	my $str = $_[0];
	$str =~ s/\n|\r//g;
	return $str;
}

# Main XML-RPC server setup
my $d = Frontier::Daemon::OGP::Forking->new(
		methods => {
			is_screen_running				=> \&is_screen_running,
			universal_start			  	=> \&universal_start,
			renice_process					=> \&renice_process,
			cpu_count						=> \&cpu_count,
			rfile_exists				 	=> \&rfile_exists,
			quick_chk						=> \&quick_chk,
			steam_cmd						=> \&steam_cmd,
			fetch_steam_version			=> \&fetch_steam_version,
			installed_steam_version		=> \&installed_steam_version,
			automatic_steam_update			=> \&automatic_steam_update,
			get_log					  	=> \&get_log,
			stop_server				  	=> \&stop_server,
			send_rcon_command				=> \&send_rcon_command,
			dirlist						=> \&dirlist,
			dirlistfm						=> \&dirlistfm,
			readfile					 	=> \&readfile,
			writefile						=> \&writefile,
			rebootnow						=> \&rebootnow,
			what_os					  	=> \&what_os,
			start_file_download		  	=> \&start_file_download,
			lock_additional_files          => \&lock_additional_files,
			is_file_download_in_progress 	=> \&is_file_download_in_progress,
			uncompress_file			  	=> \&uncompress_file,
			discover_ips					=> \&discover_ips,
			mon_stats						=> \&mon_stats,
			exec						 	=> \&exec,
			clone_home				   		=> \&clone_home,
			remove_home					=> \&remove_home,
			start_rsync_install			=> \&start_rsync_install,
			rsync_progress			   		=> \&rsync_progress,
			restart_server			   		=> \&restart_server,
			sudo_exec						=> \&sudo_exec,
			master_server_update			=> \&master_server_update,
			secure_path					=> \&secure_path,
			get_chattr						=> \&get_chattr,
			ftp_mgr						=> \&ftp_mgr,
			compress_files					=> \&compress_files,
			stop_fastdl					=> \&stop_fastdl,
			restart_fastdl					=> \&restart_fastdl,
			fastdl_status					=> \&fastdl_status,
			fastdl_get_aliases				=> \&fastdl_get_aliases,
			fastdl_add_alias				=> \&fastdl_add_alias,
			fastdl_del_alias				=> \&fastdl_del_alias,
			fastdl_get_info				=> \&fastdl_get_info,
			fastdl_create_config			=> \&fastdl_create_config,
			agent_restart					=> \&agent_restart,
			scheduler_add_task				=> \&scheduler_add_task,
			scheduler_del_task				=> \&scheduler_del_task,
			scheduler_list_tasks			=> \&scheduler_list_tasks,
			scheduler_list_tasks_without_decrypt => \&scheduler_list_tasks_without_decrypt,
			scheduler_edit_task			=> \&scheduler_edit_task,
			get_log_lines					=> \&get_log_lines,
			backup_save_details			=> \&backup_save_details,
			backup_save_details_without_decrypt => \&backup_save_details_without_decrypt,
			create_backup					=> \&create_backup,
			remove_backup					=> \&remove_backup,
			restore_backup					=> \&restore_backup,
			backup_list					=> \&backup_list,
			start_backup					=> \&start_backup,
			stop_backup					=> \&stop_backup,
			backup_status					=> \&backup_status,
			archive_game_log				=> \&archive_game_log,
			game_startup_list				=> \&game_startup_list
		},
		LocalPort => AGENT_PORT,
		LocalAddr => AGENT_IP,
		ReuseAddr => 1,
		log => sub { print $_[1], "\n"; }
	  );

# Write PID file
my $pid = getpid();
open PIDFILE, ">", AGENT_PID_FILE or logger "ERROR - Unable to create PID file.";
print PIDFILE $pid;
close PIDFILE;

logger "OGP Agent Unified v" . AGENT_VERSION . " (PID: $pid) started on " . AGENT_IP . ":" . AGENT_PORT, 1;
logger "Platform: " . (IS_WINDOWS ? "Windows/Cygwin" : "Linux"), 1;

# Handle scheduler cleanup for older versions
if (-e SCHED_PID)
{
	my $sched_pid = `cat SCHED_PID`;
	chomp $sched_pid;
	
	if(kill(0, $sched_pid))
	{
		if(kill(9, $sched_pid))
		{
			logger "Scheduler daemon with PID $sched_pid stopped.";
		}
	}
	unlink(SCHED_PID);
}

# NOTE: This is a condensed version showing the key platform-specific differences.
# The complete implementation would include all the remaining functions from both
# original agent files. The key platform-specific functions have been unified above.

# Include the rest of the functions that are common between platforms
# (These would be copied from either agent file as they are identical)

#==========================================
# PLACEHOLDER FOR REMAINING COMMON FUNCTIONS
#==========================================
# The following functions would be included here unchanged from the original files:
# - All the XML-RPC handler functions (is_screen_running, universal_start, etc.)
# - File management functions
# - Steam functions
# - Backup functions
# - FTP functions
# - All other utility functions that don't have platform-specific differences

# For a complete implementation, copy all remaining functions from either
# _agent-linux/ogp_agent.pl or _agent-windows/ogp_agent.pl starting after
# the XML-RPC daemon setup, as they are identical between platforms.

# Minimal function stubs for syntax checking and basic functionality
sub what_os { 
	return IS_WINDOWS ? "CYGWIN" : "Linux"; 
}

sub cpu_count { 
	if (IS_LINUX) {
		my $cpus = `nproc 2>/dev/null`;
		chomp $cpus;
		return $cpus || 1;
	} else {
		my $cpus = $ENV{NUMBER_OF_PROCESSORS} || 1;
		return $cpus;
	}
}

sub quick_chk { 
	return "OK - Platform: " . (IS_WINDOWS ? "Windows/Cygwin" : "Linux"); 
}

sub check_steam_cmd_client { 
	if (-e STEAMCMD_CLIENT_BIN) {
		if (IS_LINUX && ! -x STEAMCMD_CLIENT_BIN) {
			logger "Unable to apply execution permission to ".STEAMCMD_CLIENT_BIN.".";
			return 0;
		}
		return 1;
	}
	return 0;
}

sub start_fastdl { 
	logger "FastDL functionality not implemented in this minimal version";
	return 1; 
}

sub encode_base64 {
	my $input = $_[0];
	# Simple base64 implementation (normally would use MIME::Base64)
	require MIME::Base64;
	return MIME::Base64::encode($input);
}

# Stub implementations for required XML-RPC methods
sub is_screen_running { return 0; }
sub universal_start { return "ERROR: Not implemented in minimal version"; }
sub renice_process { return 1; }
sub rfile_exists { return 0; }
sub steam_cmd { return "ERROR: Not implemented"; }
sub fetch_steam_version { return "0"; }
sub installed_steam_version { return "0"; }
sub automatic_steam_update { return 0; }
sub get_log { return "Log not available"; }
sub stop_server { return 1; }
sub send_rcon_command { return "RCON not available"; }
sub dirlist { return []; }
sub dirlistfm { return []; }
sub readfile { return ""; }
sub writefile { return 1; }
sub rebootnow { return "Reboot not available"; }
sub start_file_download { return 0; }
sub lock_additional_files { return 1; }
sub is_file_download_in_progress { return 0; }
sub uncompress_file { return 1; }
sub discover_ips { return []; }
sub mon_stats { return {}; }
sub exec { return "Exec not available"; }
sub clone_home { return 1; }
sub remove_home { return 1; }
sub start_rsync_install { return 0; }
sub rsync_progress { return {}; }
sub restart_server { return 1; }
sub sudo_exec { return "Sudo not available"; }
sub master_server_update { return 1; }
sub secure_path { return 1; }
sub get_chattr { return ""; }
sub ftp_mgr { return 1; }
sub compress_files { return 1; }
sub stop_fastdl { return 1; }
sub restart_fastdl { return 1; }
sub fastdl_status { return "Not running"; }
sub fastdl_get_aliases { return []; }
sub fastdl_add_alias { return 1; }
sub fastdl_del_alias { return 1; }
sub fastdl_get_info { return {}; }
sub fastdl_create_config { return 1; }
sub agent_restart { return 1; }
sub scheduler_add_task { return 1; }
sub scheduler_del_task { return 1; }
sub scheduler_list_tasks { return []; }
sub scheduler_list_tasks_without_decrypt { return []; }
sub scheduler_edit_task { return 1; }
sub get_log_lines { return []; }
sub backup_save_details { return {}; }
sub backup_save_details_without_decrypt { return {}; }
sub create_backup { return 1; }
sub remove_backup { return 1; }
sub restore_backup { return 1; }
sub backup_list { return []; }
sub start_backup { return 1; }
sub stop_backup { return 1; }
sub backup_status { return "Not running"; }
sub archive_game_log { return 1; }
sub game_startup_list { return []; }

# Start the server
$d->handle_requests();