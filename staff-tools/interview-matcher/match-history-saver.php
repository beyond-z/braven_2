<?php 
	include_once("db.php");

	save_matches($_POST["event_id"], $_POST['matches']);
?>
<h2><a href="matcher.php?event_id=<?php echo htmlentities($_POST['event_id']); ?>">Match again</a></h2>

<a href="results.php?event_id=<?php echo htmlentities($_POST['event_id']); ?>">See Results Page</a>

<?php

	$event_id = (int) $_REQUEST["event_id"];
	$volunteers = load_volunteers_from_database($event_id);
	$fellows = load_fellows_from_database($event_id);
	$matches = $_POST['matches'];

	include_once("shared.php");
	bz_show_proposed_matches(false);
?>
