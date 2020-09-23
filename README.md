# NRockhouse/Pi3Fingerprint
Raspberry Pi 3 Time Attendance System with DigitalPersona U.are.U 4500 Scanner using libfprint

# Demo
Youtube video: https://www.youtube.com/watch?v=m9w4m63oj4U

Tested with the following hardware:
- Raspberry Pi 3 Model B+
- DigitalPersona U.are.U® 4500 Fingerprint Reader
- XPT2046 480X320 3.5inch RPi Display

# Installation on Raspberry Pi
1. Unzip the sdp directory into `/usr/share`

2. Run the following command:
	`sudo apt-get -y install libfprint0 php-cli xdotool`

3. Set executable and SUID bit (the binaries requires root to interact with the fingerprint scanner).
    - `cd /usr/share/sdp/bin`
    - `chmod u+s enrollfinger readfingers replaceadmin`

4. Run the application `/usr/share/sdp/bin/replaceadmin` to register an admin fingerprint first.

5. In `/usr/share/sdp/gui/welcome.php`, please modify the keyword in line 16, `"YOUR_WEBSERVER_ADDRESS_HERE/YOUR_FILE_HERE"` to your web server address.

6. (Optional) To make sure the fingerprint program executes on startup, create a file at `/home/pi/.config/autostart/fpscanner.desktop` with the following contents:
```ini
[Desktop Entry]
Type=Application
Name=Fingerprint Scanner
Exec=/usr/share/sdp/startup.sh
```
7. Restart your Pi so that the program runs on startup. Otherwise, you can just execute `/usr/share/sdp/startup.sh` to start the program.

# Installation on authentication web server
Unfortunately, I cannot provide the full source code for the web server as it is a different part of the project. I have however provided the API page I've used, which shows how to parse the data from the fingerprint scanner and insert it into a MySQL server. You can find the file named `addattendance.php` placed separately. You would need to tweak the file on your own, such as handling the authentication into the MySQL server. The following are the data that is being sent to the API as per mentioned in step 5 just now.

The data is being sent via POST, here are the parameters:
- fpid: The fingerprint ID
- hash: A hash to make sure the data being sent cannot be intercepted and altered. The PHP condition to check this is as follows. This data can be omitted as it is just an additional security measure. (Note that the time zone on the Pi and the web server has to be the same)
```php
if(
    $_POST['hash'] !== hash_hmac('sha1', $_POST['fpid'].(new DateTime("now",$tz))->format('YnjGi'), 'rsc_fpbiometrics_pw216') &&
    $_POST['hash'] !== hash_hmac('sha1', $_POST['fpid'].(new DateTime("-1 min",$tz))->format('YnjGi'), 'rsc_fpbiometrics_pw216')
) {
    header('HTTP/1.1 403 Forbidden');
}
```

# Security Concerns / Disclaimer
The binary programs used to interact with libfprint are NOT safe from exploits such as buffer overflow. The programs have not been checked for security vulnerabilities and are not considered safe for production use. I am not liable for any harm introduced by the usage of this program, or using any libraries required to run this program.

# General Concepts
The C programs in the `./bin` directory contains both the source code and binary to interact with libfprint. The source code is not necessary to run the program and it can be deleted. To make modifications to the binaries, simply edit the C files and run `./compile filename` (e.g. `./compile readfingers`)  to compile it into binary form.

The GUI is done via showing a full screen Chromium window. The HTML files used to power the GUI can be found in `./gui` directory.

For Chromium to know which file to display, the data is being read from `./gui/data.txt`. After that, xdotool (hardcoded in my C binaries) will artificially send the "F5" shortcut key so that Chromium refreshes and show another page.

# Questions?
I'm sorry that it is very complicated, it is not optimised for public use yet.

Please don't hesitate to contact me if you have any questions. It is preferable to contact me on [Twitter @NRockhouse](https://twitter.com/NRockhouse) or Discord NRockhouse#4157, but you can open an issue in this repository as well.
