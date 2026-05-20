@echo off
@title OGP Agent
FOR /f "tokens=2,3,4 delims=[.]" %%a IN ('ver') DO SET WVer=%%a
FOR /f "tokens=2,3 delims= " %%a IN ('echo %WVer%') DO SET Ver=%%a
whoami /groups | find "S-1-16-12288" >nul 2>&1
if NOT %errorLevel% == 0 if %VER% GEQ 6 (
	echo Failure: Current permissions inadequate.
	echo[
	echo Run this script by using "Run as administrator" in the context menu.
	pause >nul
	exit
)
set WD=%~dp0
pushd %WD%
set path=%WD%bin;%WD%usr\sbin;%path%
set CYGWIN=server ntsec
set SHELL=/bin/bash
set runAgentNormally=no

REM Stop any running agent
if exist %WD%var\run\pure-ftpd.pid set /p PID1=<%WD%var\run\pure-ftpd.pid
if exist %WD%OGP\ogp_agent.pid set /p PID2=<%WD%OGP\ogp_agent.pid
if exist %WD%OGP\ogp_agent_run.pid set /p PID3=<%WD%OGP\ogp_agent_run.pid
IF NOT [%PID1%] == [] kill -15 %PID1%
IF NOT [%PID2%] == [] kill -15 %PID2%
IF NOT [%PID3%] == [] kill -15 %PID3%

REM Check for gameserver user and if it exists and the user running this script matches, run it the normal way, else prompt for elevation
if "%username%" == "" set runAgentNormally=yes
if "%username%" == "gameserver" set runAgentNormally=yes

net user gameserver
if %ERRORLEVEL% EQU 0 (
	if %runAgentNormally% == yes (
		bash ogp_agent -pidfile /OGP/ogp_agent_run.pid
	) else (
		cygstart mintty /c "runas /profile /user:gameserver \"%WD%\bin\bash.exe %WD%\bin\ogp_agent -pidfile /OGP/ogp_agent_run.pid\""
	)
) else (
	bash ogp_agent -pidfile /OGP/ogp_agent_run.pid
)
