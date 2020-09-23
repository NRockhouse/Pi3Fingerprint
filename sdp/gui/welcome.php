<?php
        if(strpos($data[1], 'admin') !== false) {
                file_put_contents("data.txt","admin.php");
                echo '<meta http-equiv="refresh" content="0;url=http://localhost:8000" />';
        } else {
		file_put_contents("data.txt","clock.php");
?>
<script src="sha1hmac.js"></script>
<script>
        var fpid = "<?php echo $data[1]; ?>";
        var xhr = new XMLHttpRequest();

	var d = new Date();
	var hash = Crypto.sha1_hmac(fpid + d.getFullYear() + (d.getMonth()+1) + d.getDate() + d.getHours() + d.getMinutes().toString().padStart(2,'0'), "rsc_fpbiometrics_pw216");

        xhr.open("POST", "http://YOUR_WEBSERVER_ADDRESS_HERE/YOUR_FILE_HERE.php", false);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("fpid=" + fpid + "&hash=" + hash);
	if(xhr.status != 200) {
		document.write("<center><h1>Error</h1><p>Couldn't contact staff management system database. Error code: " + xhr.status);
		setTimeout(function(){ window.location.reload();  }, 3000);
	} else {
		document.write(xhr.responseText);
	}
</script>
<?php
        }
?>
