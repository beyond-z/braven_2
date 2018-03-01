<?php 
	include_once("db.php");

	save_matches($_POST["event_id"], $_POST['matches']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Braven Mock Interview Matcher</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<h1>Success!</h1>
<ul>
	<li><a href="matcher.php?event_id=<?php echo htmlentities($_POST['event_id']); ?>">Start a new round</a></li>
	<li><a href="results.php?event_id=<?php echo htmlentities($_POST['event_id']); ?>">View results</a></li>
</ul>

<?php

	$event_id = (int) $_REQUEST["event_id"];
	$volunteers = load_volunteers_from_database($event_id);
	$fellows = load_fellows_from_database($event_id);
	$matches = $_POST['matches'];

	include_once("shared.php");
	bz_show_proposed_matches(false);
?>

</body>
