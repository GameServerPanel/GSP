@echo off
echo.
echo Stopping OGP Agent if exists...
SET mypath=%~dp0
IF EXIST "%mypath%\agent_stop.bat" call "%mypath%\agent_stop.bat"
echo.
echo Stopping CygWin Services...
echo. 
net stop mysqld
net stop cygserver
net stop httpd
net stop cron
echo.
echo Running CygWin rebaseall command to prevent errors...
echo .
C:
cd "%mypath%\bin"
ash.exe /bin/rebaseall
echo.

