<?php
	include_once("db.php");
	include_once("sso.php");

	requireLogin();

	/// call me
	function show_event_selection_page() {
		$events = get_events();
?><!DOCTYPE html>
<html>
<head>
	<title>Braven Interview Tool: Choose an Event</title>
</head>
<body>
	<h1>Choose an Event</h1>

<?php
		foreach($events as $event) {
			echo "<a href=\"".htmlentities($_SERVER["PHP_SELF"])."?event_id={$event["id"]}\">";
			echo "<span>".htmlentities($event["name"])."</span>";
			echo "<br />";
		}
?>
	<br />
	<a href="event-preparation.php">Prepare a New Event</a>
</body>
</html><?php
	}
