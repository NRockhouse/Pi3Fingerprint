<?php
        file_put_contents("data.txt","clock.php");
	if(strpos($data[1], 'FATAL ERROR') !== false) {
		echo '<meta http-equiv="refresh" content="6;url=http://localhost:8000" />';
	} else {
		echo '<meta http-equiv="refresh" content="3;url=http://localhost:8000" />';
	}
?>
<center>
<h1>Unrecognized fingerprint</h1>
<p><?php echo $data[1]; ?></p>
</center>
