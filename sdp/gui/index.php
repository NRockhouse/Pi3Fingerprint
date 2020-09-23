<style>
	* {
		cursor: none;
		font-family: verdana;
		color: white
	}

	body {
		background-color: black;
	}
</style>
<script>
	function $(id) {
                return document.getElementById(id);
        }
</script>
<?php
	chdir(dirname(__FILE__));
	$data = explode('|',trim(file_get_contents('data.txt')));
	require_once($data[0]);
?>
