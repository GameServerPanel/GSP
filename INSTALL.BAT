@echo off


REM ===== Admin check =====
net session >nul 2>&1
IF %ERRORLEVEL% neq 0 (
  echo Failure: Current permissions inadequate.
  echo.
  echo Run this script by using "Run as administrator" in the context menu.
  pause >nul
  exit /b 1
)

REM Ensure we run from script folder
set WD=%~dp0
pushd %WD%
set WD=%WD:~0,-1%

REM Info when elevated
echo Administrator privileges detected.
echo This will create/update the 'gameserver' user and install Cygwin
echo to provide a Linux-like environment for running the Game Panel on Windows.
echo.

REM ===== Cygwin prep =====
set CYGWIN=server ntsec
set path=%WD%\bin;%WD%\usr\sbin;%path%
set SHELL=/bin/bash
echo DO NOT CLOSE THIS WINDOW YET.
echo The setup process will continue once Cygwin installation ends.
echo ...
echo Download and install Cygwin
echo ...

REM --- Download quietly (silent except errors) ---
echo [INFO] Downloading Cygwin setup...
curl -L --fail -sS "https://cygwin.com/setup-x86_64.exe" -o "setup-x86_64.exe" || (
  echo [ERROR] Download failed.
  pause
  exit /b 1
)

REM --- Run installer and WAIT for it to finish; log output to file ---
echo [INFO] Installing Cygwin (please wait)...
start "" /wait "%CD%\setup-x86_64.exe" ^
  --site "http://cygwin.mirror.constant.com/" ^
  --quiet-mode ^
  --root "%WD%" ^
  --local-package-dir "%WD%\cygTemp" ^
  --packages "screen,perl,perl-HTTP-Daemon,perl_vendor,perl-Path-Class,perl-XML-Parser,perl-Archive-Zip,perl-XML-Simple,wget,unzip,gawk,rsync,curl,bzip2,zip,cygrunsrv,dos2unix,mutt,ssmtp,nano,git,subversion,perl-Archive-Extract" ^
  > "Cygwin64_Agent_Setup.log" 2>&1

set "RC=%ERRORLEVEL%"
echo [INFO] Cygwin installer exit code: %RC%
if not "%RC%"=="0" (
  echo [WARN] Installer returned non-zero. See Cygwin64_Agent_Setup.log for details.
)

REM Tip: avoid clearing screen so you can read the output
REM cls


REM ===== Create/update 'gameserver' and make admin (PowerShell handles special chars) =====
set /p PASS=Enter password for 'gameserver' (visible): 
set "GSU=gameserver"

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$name=$env:GSU; $p=$env:PASS; $sec=ConvertTo-SecureString $p -AsPlainText -Force; " ^
  "if (Get-LocalUser -Name $name -ErrorAction SilentlyContinue) " ^
  "{ Set-LocalUser -Name $name -Password $sec } " ^
  "else { New-LocalUser -Name $name -Password $sec -FullName $name | Out-Null } ; " ^
  "Add-LocalGroupMember -Group 'Administrators' -Member $name -ErrorAction SilentlyContinue"

if errorlevel 1 (
  echo [ERROR] Failed to create/update 'gameserver' or add to Administrators.
  pause
  exit /b 1
)

cls

REM ===== Fetch agent files (curl instead of wget) =====
REM (Old SVN example left as comment)
REM curl -L "http://master.dl.sourceforge.net/project/ogpextras/Installer-Snapshot/latest_win_agent_files.zip" -o "agent_files.zip"
curl -L "https://github.com/OpenGamePanel/OGP-Agent-Windows/archive/master.zip" -o "agent_files.zip"

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

REM ===== Configure agent (pass password safely) =====
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "Start-Process -FilePath bash -NoNewWindow -Wait -ArgumentList @('/OGP/agent_conf.sh','-p',$env:PASS)"

REM ===== Create scheduled task (password via PowerShell to avoid metachar issues) =====
tools\fart.exe "%WD%\service_settings.xml" "{COMMAND}" "%WD%\agent_start.bat"
tools\fart.exe "%WD%\service_settings.xml" "{COMMAND_WORK_DIR}" "%WD%"

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$xml = Join-Path $env:WD 'service_settings.xml'; " ^
  "Start-Process -FilePath schtasks -NoNewWindow -Wait -ArgumentList @('/create','/tn','OGP agent start on boot','/XML',$xml,'/ru','gameserver','/rp',$env:PASS)"

REM ===== Rebase & start agent =====
call "%WD%\rebase_post_ins.bat"
echo.
schtasks /Run /tn "OGP agent start on boot"

REM ===== Grant logon as a service =====
tools\ntrights.exe +r SeServiceLogonRight -u gameserver -m \\%COMPUTERNAME%

echo.
echo [SUCCESS] Installation completed.
exit /b 0
