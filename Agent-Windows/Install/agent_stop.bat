@echo off
@title Stop OGP Agent 
net session >nul 2>&1
if NOT %errorLevel% == 0 (
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
if exist %WD%var\run\pure-ftpd.pid set /p PID1=<%WD%var\run\pure-ftpd.pid
if exist %WD%OGP\ogp_agent.pid set /p PID2=<%WD%OGP\ogp_agent.pid
if exist %WD%OGP\ogp_agent_run.pid set /p PID3=<%WD%OGP\ogp_agent_run.pid
IF NOT [%PID1%] == [] kill -15 %PID1%
IF NOT [%PID2%] == [] kill -15 %PID2%
IF NOT [%PID3%] == [] kill -15 %PID3%
