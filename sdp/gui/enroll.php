<div id="body"></div>
<script>
	var error = "<?php echo $data[2]; ?>";
	var stage = <?php echo $data[1]; ?>;
	for(var i=1; i<=5; i++) {
		if(i == stage && error == "") {
			$("body").innerHTML += "<p style='color: yellow'>Enrollment stage: " + i + "</p>";
		} else if(i == stage) {
			$("body").innerHTML += "<p style='color: red'>Enrollment stage: " + i + " (" + error + ")</p>";
		} else if(i < stage) {
			$("body").innerHTML += "<p style='color: green'>Enrollment stage: " + i + "</p>";
		} else if(i > stage) {
			$("body").innerHTML += "<p>Enrollment stage: " + i + "</p>";
		}
	}
</script>
