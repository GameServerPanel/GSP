@echo off
TITLE DayZ Server
COLOR 0A
:: Variables::
::DZSALModServer.exe path
set DAYZ-SA_SERVER_LOCATION="C:\SERVERFILEPATH\server"
::Bec.exe path
set BEC_LOCATION="C:\SERVERFILEPATH\server\Battleye\Bec"
::
::ModCheck ; Enter location of Mod List and where your steam workshop files download to (set for default)
set MOD_LIST=(C:\FILEPATH\Modlist.txt)
set STEAM_WORKSHOP=C:\FILEPATH\steamcmd\steamapps\workshop\content\221100
set STEAMCMD_LOCATION=C:\FILEPATH\steamcmd
set STEAM_USER=USERNAME
set STEAMCMD_DEL=10
::::::::::::::

echo Agusanz
goto checksv
pause

:checksv
tasklist /FI "IMAGENAME eq DZSALModserver.exe" 2>NUL | find /I /N "DZSALModserver.exe">NUL
if "%ERRORLEVEL%"=="0" goto checkbec
cls
echo Server is not running, taking care of it..
goto killsv

:checkbec
tasklist /FI "IMAGENAME eq Bec.exe" 2>NUL | find /I /N "Bec.exe">NUL
if "%ERRORLEVEL%"=="0" goto loopsv
cls
echo Bec is not running, taking care of it..
goto startbec

:loopsv
FOR /L %%s IN (30,-1,0) DO (
	cls
	echo Server is running. Checking again in %%s seconds.. 
	timeout 1 >nul
)
goto checksv

:killsv
taskkill /f /im Bec.exe
taskkill /f /im DZSALModserver.exe
goto checkmods

:startsv
cls
echo Starting DayZ SA Server.
timeout 1 >nul
cls
echo Starting DayZ SA Server..
timeout 1 >nul
cls
echo Starting DayZ SA Server...
cd "%DAYZ-SA_SERVER_LOCATION%"
start DZSALModserver.exe -config=serverDZ.cfg -port=2302 -dologs -adminlog -netlog -freezecheck -BEpath=C:\SERVERFILEPATH\server\battleye -profiles=C:\FILEPATH\ServerLogs\server "-mod=@yourmods;@gohere"
FOR /L %%s IN (30,-1,0) DO (
	cls
	echo Initializing server, wait %%s seconds to initialize Bec.. 
	timeout 1 >nul
)
goto startbec

:startbec
cls
echo Starting Bec.
timeout 1 >nul
cls
echo Starting Bec..
timeout 1 >nul
cls
echo Starting Bec...
timeout 1 >nul
cd "%BEC_LOCATION%"
start Bec.exe -f Config.cfg
goto checksv

:checkmods
cls
FOR /L %%s IN (90,-1,0) DO (
	cls
	echo Checking for mod updates in %%s seconds.. 
	timeout 1 >nul
)
echo Reading in configurations/variables set in this batch and MOD_LIST. Updating Steam Workbench mods...
@ timeout 1 >nul
cd %STEAMCMD_LOCATION%
for /f "tokens=1,2 delims=," %%g in %MOD_LIST% do steamcmd.exe +login %STEAM_USER% +workshop_download_item 221100 "%%g" +quit +cls
cls
echo Steam Workshop files up to date! Syncing Workbench source with server destination...
@ timeout 2 >nul
@ for /f "tokens=1,2 delims=," %%g in %MOD_LIST% do robocopy "%STEAM_WORKSHOP%\%%g" "%DAYZ-SA_SERVER_LOCATION%\%%h" *.* /mir
cls
echo Sync complete! If sync not completed correctly, verify configuration file.
@ timeout 3 >nul
cls
goto startsv