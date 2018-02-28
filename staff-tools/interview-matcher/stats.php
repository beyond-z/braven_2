<?php
	include_once("db.php");

	$event_id = (int) $_REQUEST["event_id"];

	if($event_id == 0) {
		// need to choose an event
		include_once("event_selection.php");
		show_event_selection_page();
		exit;
	}

	$match_history = load_match_history($event_id);


