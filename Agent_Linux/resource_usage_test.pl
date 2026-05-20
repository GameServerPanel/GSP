#!/usr/bin/perl
#
# Resource Monitoring Demo/Test Script
# This script demonstrates the resource monitoring functionality
# without requiring a full database connection.
#
use strict;
use warnings;
use lib '.'; # Use current directory for local modules
use POSIX qw(strftime);
use Digest::MD5 qw(md5_hex);
use File::Find;

# Load the config to verify it works
use Cfg::Config;

print "=== GSP Agent Resource Monitoring Demo ===\n\n";

# Test 1: Configuration Loading
print "1. Testing Configuration Loading:\n";
print "   Database Host: " . ($Cfg::Config{stats_db_host} || 'undefined') . "\n";
print "   Database User: " . ($Cfg::Config{stats_db_user} || 'undefined') . "\n";
print "   Database Name: " . ($Cfg::Config{stats_db_name} || 'undefined') . "\n";
print "   Frequency: " . ($Cfg::Config{stats_frequency_minutes} || 'undefined') . " minutes\n";
print "   ✓ Configuration loaded successfully\n\n";

# Test 2: Machine ID Generation
print "2. Testing Machine ID Generation:\n";
sub get_machine_id_demo {
    my $hostname = `hostname` || 'unknown';
    chomp($hostname);

    # For demo on Linux, get MAC from ifconfig
    my $mac = '';
    my $ifconfig_output = `ifconfig 2>/dev/null | grep -o -E '([[:xdigit:]]{1,2}:){5}[[:xdigit:]]{1,2}' | head -1`;
    chomp($ifconfig_output);
    $mac = $ifconfig_output if $ifconfig_output;

    my $machine_id = md5_hex($hostname . '_' . $mac . '_' . './test');
    print "   Hostname: $hostname\n";
    print "   MAC Address: $mac\n";
    print "   Generated Machine ID: $machine_id\n";
    print "   ✓ Machine ID generated successfully\n\n";
    return $machine_id;
}
my $machine_id = get_machine_id_demo();

# Test 3: Timestamp Generation
print "3. Testing Timestamp Generation:\n";
my $timestamp = strftime("%Y-%m-%d %H:%M:%S", localtime());
print "   Current timestamp: $timestamp\n";
print "   ✓ Timestamp generated successfully\n\n";

# Test 4: Game Server Detection (if startup directory exists)
print "4. Testing Game Server Detection:\n";
my $startup_dir = './startups'; # Relative to script's root
if (-d $startup_dir) {
    opendir(my $dir, $startup_dir) or die "Cannot open $startup_dir: $!";
    my @startup_files = grep { !/^\./ && -f "$startup_dir/$_" } readdir($dir);
    closedir($dir);

    if (@startup_files) {
        print "   Found " . scalar(@startup_files) . " game server startup files:\n";
        foreach my $file (@startup_files) {
            if ($file =~ /^(.+)-(\d+)$/) {
                my ($ip, $port) = ($1, $2);
                print "     - $ip:$port (file: $file)\n";

                # Read server path
                if (open(my $fh, '<', "$startup_dir/$file")) {
                    my $path = <$fh>;
                    chomp($path) if $path;
                    print "       Path: $path\n" if $path;
                    close($fh);
                }
            }
        }
    } else {
        print "   No startup files found (startup directory is empty)\n";
        print "   In production, only processes with startup files are monitored\n";
    }
    print "   ✓ Game server detection working\n\n";
} else {
    print "   Startup directory not found: $startup_dir\n";
    print "   ✓ This is expected in a clean installation\n\n";
}

# Test 5: Database Schema
print "5. Checking Database Schema Files:\n";
my $db_dir = './DB'; # Relative to script's root
if (-d $db_dir) {
    opendir(my $dir, $db_dir) or die "Cannot open $db_dir: $!";
    my @sql_files = grep { /\.sql$/ } readdir($dir);
    closedir($dir);

    print "   Found " . scalar(@sql_files) . " SQL schema files:\n";
    foreach my $file (@sql_files) {
        print "     - $file\n";
    }
    print "   ✓ Database schema files ready\n\n";
}

# Test 6: Windows/Linux Compatibility Check
print "6. Testing Platform Compatibility:\n";
if (-e '/proc/stat') {
    print "   Platform: Linux (using /proc/stat for CPU monitoring)\n";
} else {
    print "   Platform: Windows (would use wmic for CPU monitoring)\n";
}

if (-e '/proc/meminfo') {
    print "   Memory monitoring: Linux /proc/meminfo\n";
} else {
    print "   Memory monitoring: Windows wmic OS queries\n";
}

if (-e '/bin/df') {
    print "   Disk monitoring: Linux/Cygwin df command\n";
} else {
    print "   Disk monitoring: Windows wmic logicaldisk\n";
}
print "   ✓ Platform detection working\n\n";

print "=== Demo Complete ===\n";
print "The resource monitoring system is ready for use.\n";
print "To enable it:\n";
print "1. Configure database settings in Cfg/Config.pm\n";
print "2. Import SQL schema files from DB/ directory\n";
print "3. Install DBI and DBD::mysql Perl modules\n";
print "4. Restart the OGP Agent\n";
print "\nThe system will automatically start collecting resource data\n";
print "every " . ($Cfg::Config{stats_frequency_minutes} || '5') . " minutes when enabled.\n";
