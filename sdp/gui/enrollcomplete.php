<?php
	file_put_contents("data.txt","admin.php");
	if($data[1] == 'admin') {
		echo '<h1>Administrator fingerprint replaced successfully!</h1><meta http-equiv="refresh" content="3;url=http://localhost:8000" />';
	} else {
?>
<center>
<h1>Fingerprint enrolled successfully</h1>
<p>Fingerprint ID: <?php echo $data[1]; ?></p>
<br><br>
<button style="width:80%;background-color:blue;height:100px" onclick="window.location.replace('/')"><font size="50">Done</font></button>
<?php
	}
?>
