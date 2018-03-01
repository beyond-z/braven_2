<?php 
	include_once("db.php");

	save_matches($_POST["event_id"], $_POST['matches']);
?>
<h2><a href="matcher.php?event_id=<?php echo htmlentities($_POST['event_id']); ?>">Match again</a></h2>
