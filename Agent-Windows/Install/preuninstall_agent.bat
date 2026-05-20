@echo off
set WD=%~dp0
pushd %WD%
net session >nul 2>&1
if NOT %errorLevel% == 0 (
	echo Failure: Current permissions inadequate.
	echo[
	echo Run this script by using "Run as administrator" in the context menu.
	pause >nul
	exit
)
net stop ogp_agent
sc delete ogp_agent
