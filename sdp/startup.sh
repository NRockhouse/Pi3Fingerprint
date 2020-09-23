cd /usr/share/sdp
cd gui
echo -n "clock.php" > data.txt
php -S localhost:8000 >/dev/null 2>/dev/null &
DISPLAY=:0 chromium-browser --kiosk --incognito --disable-infobars http://localhost:8000/ &
