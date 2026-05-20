# The agent runs this file if it exists before running the game itself.
# We use this to run a second application such as a bot.  The _startServer.bat file will
# run this and then kill it when the game starts.  We used to use the _prestart.bat file file to run
# second apps but now that file can be reserved for other uses like log cleanup etc.

@echo off
cd bec
del /q "..\_alsoRun.pid" 2>nul
start "BEC" bec.exe --dsc --dec -f config.cfg
cd ..
timeout /t 3 /nobreak >nul
for /f "tokens=2 delims==" %%P in ('wmic process where "ExecutablePath='%cd:\=\\%\\bec.exe'" get ProcessId /value ^| find "="') do >"..\_alsoRun.pid" echo %%P



# Add this to the games XML post-install to create automatically

printf '%s\r\n' \
'@echo off' \
'cd bec \
'del /q "..\_alsoRun.pid" 2>nul' \
'start "BEC" bec.exe --dsc --dec -f config.cfg' \
' cd .. ' \
'timeout /t 3 /nobreak >nul' \
'for /f "tokens=2 delims==" %%P in ('"'"'wmic process where "ExecutablePath='"'"'%cd:\=\\%\\bec.exe'"'"'" get ProcessId /value ^| find "="'"'"') do >"..\_alsoRun.pid" echo %%P' \
> _alsoRun.bat
