<?php 
	include("db.php");

	print_r($_POST['matches']);
	save_matches(1, $_POST['matches']);
?>
<h2><a href="./">Match again</a></h2>
