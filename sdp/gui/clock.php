<?php
	exec("pgrep readfingers", $pids);
	if(empty($pids)) {
		shell_exec(dirname(__FILE__) . '/../bin/readfingers > /dev/null 2>/dev/null &');
	}
?>
<script>
	setInterval(function() {
		var d = new Date();
		$("hour").innerText = d.getHours().toString().padStart(2, '0');
		$("minute").innerText = d.getMinutes().toString().padStart(2, '0');
		$("seconds").innerText = d.getSeconds().toString().padStart(2, '0');
	}, 1000);
</script>
<center>
	<b id="hour" style="font-size: 50px">--</b>:
	<b id="minute" style="font-size: 50px">--</b>
	<b id="seconds" style="font-size: 30px">--</b>
	<p>Tap your finger on the fingerprint scanner</p>
</center>
