#!/bin/bash

#		--dry-run 


sshpass -p S4wihr6q8rzc! \
rsync -avP --delete -e "ssh -p 12322" \
--link-dest /sdb1/backup/gameserver/00-latest-kcwin.D.drive \
cyg_server@kcwin.iaregamer.com:/cygdrive/d/OGP64/home/gameserver/1437 \
/sdb1/backup/gameserver/07-Sunday-kcwin.D.drive/1437

