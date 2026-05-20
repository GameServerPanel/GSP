#!/bin/bash
# Installs Apache for Windows on Cygwin
/etc/rc.d/init.d/httpd install
cygrunsrv -S httpd
/etc/rc.d/init.d/httpd reload
