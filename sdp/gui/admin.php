<?php
	if(isset($_GET['action'])) {
		if($_GET['action'] === 'logout') {
			file_put_contents("data.txt","clock.php");
			header('Location: http://localhost:8000');
		} elseif($_GET['action'] === 'replace') {
			file_put_contents("data.txt","replaceadmin.php");
			header('Location: http://localhost:8000');
		} elseif($_GET['action'] === 'enroll') {
			file_put_contents("data.txt","enrollfinger.php");
			header('Location: http://localhost:8000');
		}
	}
?>
<body style="margin:0">
<button style="width:49%;height:50%;background-color:green" onclick="window.location.replace('/?action=enroll')"><h1>Enroll Fingerprint</h1></button>
<button style="width:49%;height:50%;background-color:purple" onclick="window.location.replace('/?action=replace')"><h1>Replace Admin Fingerprint</h1></button>
<button style="width:98%;height:50%;background-color:brown" onclick="window.location.replace('/?action=logout')"><h1>Logout</h1></button></a>
</body>
