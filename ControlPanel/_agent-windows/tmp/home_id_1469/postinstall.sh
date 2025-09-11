cd '/home/gameserver/1469'

touch epochmod_win32.xml.txt
wget http://files.iaregamer.com/installers/arma2/arma2Addons.tar .
tar -xvf arma2Addons.tar
wget http://files.iaregamer.com/installers/bec/bec.tar .
tar -xvf bec.tar
wget http://files.iaregamer.com/installers/epoch_mod/epoch_mod.tar .
tar -xvf epoch_mod.tar
chmod +x setup_db.sh
./setup_db.sh
echo "Cleaning up files"
rm *.sql
rm *.sh
rm *.tar
	
takeown /U cyg_server /f "/home/gameserver/1469" /r >/dev/null 2>&1
chmod 775 -R "/home/gameserver/1469" >/dev/null 2>&1
takeown /U cyg_server /f "C:\OGP64\home\gameserver\1469" /r >/dev/null 2>&1
chmod 775 -R "C:\OGP64\home\gameserver\1469" >/dev/null 2>&1
icacls "C:\OGP64\home\gameserver\1469" /grant cyg_server:\(OI\)\(CI\)F /T >/dev/null 2>&1
icacls "C:\OGP64\home\gameserver\1469" /grant administrators:F /T >/dev/null 2>&1
cd '/OGP/tmp/home_id_1469'
rm -f preinstall.sh
rm -f postinstall.sh
rm -f runinstall.sh
