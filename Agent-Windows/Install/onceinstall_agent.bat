@echo off
net session >nul 2>&1
IF %ERRORLEVEL% neq 0 (
	echo Failure: Current permissions inadequate.
	echo[
	echo Run this script by using "Run as administrator" in the context menu.
	pause >nul
	exit
)
REM Remove the trailing \ in the path or else Cygwin will flip when it's enclosed in double quotes (http://stackoverflow.com/questions/3160058/how-to-get-the-path-of-a-batch-script-without-the-trailing-backslash-in-a-single && https://cygwin.com/ml/cygwin/2016-11/msg00178.html)
set WD=%~dp0
pushd %WD%
set WD=%WD:~0,-1%
REM Set the needed enviroment variables to run Cygwin executables without writing the full path
set CYGWIN=server ntsec
REM PATH CANNOT BE DOUBLE QUOTED (http://serverfault.com/questions/349179/path-variable-and-quotation-marks-windows)
set path=%WD%\bin;%WD%\usr\sbin;%path%
set SHELL=/bin/bash
REM Advice 
echo DO NOT CLOSE THIS WINDOW YET.
echo The setup process will continue once cygwin installation ends.
REM Download latest Cygwin
tools\wget.exe -N "https://cygwin.com/setup-x86_64.exe" -O "setup-x86_64.exe" --no-check-certificate
REM start the setup for cygwin with the requiered repositories, paths and packages
REM OLD WAY:
REM setup-x86_64.exe --local-install --quiet-mode --root %WD% --local-package-dir %WD%cygTemp --packages "screen,perl,perl-HTTP-Daemon,perl-Path-Class,perl-XML-Parser,perl-Archive-Zip,perl-XML-Simple,wget,unzip,rsync,curl,bzip2,zip,cygrunsrv,dos2unix,mutt,ssmtp,nano,git,subversion" > Cygwin64_Agent_Setup.log
IF EXIST "setup-x86_64.exe" setup-x86_64.exe --site "http://cygwin.mirror.constant.com/" --quiet-mode --root "%WD%" --local-package-dir "%WD%\cygTemp" --packages "screen,perl,perl-HTTP-Daemon,perl_vendor,perl-Path-Class,perl-XML-Parser,perl-Archive-Zip,perl-XML-Simple,wget,unzip,gawk,rsync,curl,bzip2,zip,cygrunsrv,dos2unix,mutt,ssmtp,nano,git,subversion,perl-Archive-Extract" > Cygwin64_Agent_Setup.log
IF NOT EXIST "setup-x86_64.exe" setup-x86_64_local.exe --site "http://cygwin.mirror.constant.com/" --quiet-mode --root "%WD%" --local-package-dir "%WD%\cygTemp" --packages "screen,perl,perl-HTTP-Daemon,perl_vendor,perl-Path-Class,perl-XML-Parser,perl-Archive-Zip,perl-XML-Simple,wget,unzip,gawk,rsync,curl,bzip2,zip,cygrunsrv,dos2unix,mutt,ssmtp,nano,git,subversion,perl-Archive-Extract" > Cygwin64_Agent_Setup.log
IF EXIST "setup-x86_64.exe" DEL setup-x86_64_local.exe
cls
REM Creating administrator account
:gameserver_exists
cls
NET USER | FINDSTR gameserver >nul
IF %ERRORLEVEL% neq 0 (
	echo In order to run the agent on boot,
	echo we need an administrator account named 'gameserver'.
	echo Please, create a new administrator account named 'gameserver' 
	echo from the control panel of Windows and press any key to continue.
	pause >nul
	goto :gameserver_exists
)
cls
color C
echo Please, make sure the user 'gameserver' is an administrator account
echo and press any key to continue.
pause >nul
cls
color 7
FOR /f "tokens=2,3,4 delims=[.]" %%a IN ('ver') DO SET WVer=%%a
FOR /f "tokens=2,3 delims= " %%a IN ('echo %WVer%') DO SET Ver=%%a
set alpha=MNOPQRSTUVW
set DRIVE=M
:set_free_drive
IF EXIST %DRIVE%: (
	call set beta=%%alpha:*%DRIVE%=%%
	set DRIVE=%beta:~,1%
	goto :set_free_drive
)
:gameserver_pass_ok
set /p PASS=Please, enter the password for user 'gameserver': 
IF %VER% LSS 6 (
	grant del SeDenyNetworkLogonRight %USERDOMAIN%\gameserver
	net use %DRIVE%: \\%USERDOMAIN%\c$ %PASS% /user:gameserver >nul
) ELSE (
	schtasks /Create /RU "gameserver" /SC ONSTART /TN "testtask" /TR "calc.exe" /F /RL highest /RP %PASS% >nul
)
IF %ERRORLEVEL% NEQ 0 (
	goto :gameserver_pass_ok
) ELSE (
	IF %VER% LSS 6 (
		net use %DRIVE%: /DELETE >nul
		grant add SeDenyNetworkLogonRight %USERDOMAIN%\gameserver
	) ELSE (
		schtasks /Delete /TN "testtask" /f >nul
	)
)
cls
REM Old way from SVN
REM tools\wget.exe -N "http://master.dl.sourceforge.net/project/ogpextras/Installer-Snapshot/latest_win_agent_files.zip" -O "agent_files.zip"
tools\wget.exe -N "https://github.com/OpenGamePanel/OGP-Agent-Windows/archive/master.zip" -O "agent_files.zip" --no-check-certificate
unzip -q agent_files_old.zip
unzip -q -o agent_files.zip
IF NOT EXIST "OGP-Agent-Windows-master" unzip -q agent_files_local_copy.zip
cd "OGP-Agent-Windows-master"
IF EXIST OGP/COPYING xcopy /Y /E * ..\
IF EXIST OGP/COPYING cd ..
rm -rf "OGP-Agent-Windows-master"
rm -f agent_files.zip
rm -f agent_files_old.zip
rm -f agent_files_local_copy.zip
chmod +x /OGP/agent_conf.sh
chmod +x /bin/ogp_agent
REM Run OGP Agent configuration script
bash /OGP/agent_conf.sh -p %PASS%
REM adding OGP Agent to the system startup
tools\fart.exe "%WD%\service_settings.xml" "{COMMAND}" "%WD%\agent_start.bat"
tools\fart.exe "%WD%\service_settings.xml" "{COMMAND_WORK_DIR}" "%WD%"
schtasks /create /tn "OGP agent start on boot" /XML "%WD%\service_settings.xml" /ru "gameserver" /rp "%PASS%"
REM Rebase files
call "%WD%\rebase_post_ins.bat"
echo.
REM Start OGP Agent
schtasks /Run /tn "OGP agent start on boot"
REM Grant logon as a service for FTP / other cyg_win services... needed for FileZilla for sure in x64 installer... not sure about here, but why not put it in.
tools\ntrights.exe +r SeServiceLogonRight -u gameserver -m \\%COMPUTERNAME%
exit 0
