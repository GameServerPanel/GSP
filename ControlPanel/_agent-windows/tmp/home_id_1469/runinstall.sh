#!/bin/bash
cd '/OGP/tmp/home_id_1469'
./preinstall.sh
/OGP/steamcmd/steamcmd.exe +runscript 000001469_install.txt +exit
wait ${!}
cd '/OGP/tmp/home_id_1469'
./postinstall.sh
