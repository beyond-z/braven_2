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
	$round_count = count($match_history);

	$fellows = load_fellows_from_database($event_id);
	$volunteers = load_volunteers_from_database($event_id);

	// reset stats
	foreach($volunteers as &$volunteer) {
		$volunteer["match_count"] = 0;
		$volunteer["round_matches"] = array_fill(1, $round_count, 0);
	}
	unset($volunteer);
	foreach($fellows as &$fellow) {
		$fellow["match_count"] = 0;
		$fellow["round_matches"] = array_fill(1, $round_count, 0);
	}
	unset($fellow);

	// load from round history
	$round_number = 0;
	foreach($match_history as $round_id => $round_matches) {
		$round_number++;

		foreach($round_matches as $round_match)
		foreach($round_match as $volunteer_id => $fellow_id) {
			$volunteers[$volunteer_id]["match_count"]++;
			$volunteers[$volunteer_id]["round_matches"][$round_number] = $fellow_id;

			$fellows[$fellow_id]["match_count"]++;
			$fellows[$fellow_id]["round_matches"][$round_number] = $volunteer_id;
		}
	}
?><!DOCTYPE>
<html>
<head>
	<title>Interview Result Stats</title>
	<style>
		.interview-details {
			border-collapse: collapse;
			table-layout: fixed;
			width: 100%;
		}
		.interview-details th {
			padding: 0.2em;
		}
		.interview-details td {
			text-align: center;
		}
		.interview-details,
		.interview-details th,
		.interview-details td {
			border: solid 1px #ccc;
		}
		.interview-details tr:first-child th {
			border-bottom: solid 1px black;
			text-align: center;
		}
		.interview-details tr:last-child th {
			border-top: solid 1px black;
			text-align: center;
		}
		.interview-details th:first-child {
			border-right: solid 1px black;
			text-align: right;
		}
		.interview-details th:last-child {
			border-left: solid 1px black;
			text-align: left;
		}

		.interview-details tr:hover,
		.interview-details col:hover {
			background-color: #f0f0f0;
		}

		.interview-details col:nth-child(even) {
			background: #f8f8f8;
		}
	</style>
</head>
<body>
<?php
	// fellow stat - how many matches? in which rounds?
	// volunteer stats - how many interviews performed? which rounds?
	// match table: fellows on one axis, volunteers on another. x for interview done

	/*
	echo "<pre>";
	print_r($volunteers);
	echo "</pre>";
	*/

	echo "<table class=\"interview-details\">";
	echo "<col />";
	foreach($volunteers as $volunteer) {
		echo "<col />";
	}

	echo "<tr>";
	echo "<th></th>";
	foreach($volunteers as $key => $volunteer) {
		echo "<th>".htmlentities($volunteer["name"])."</th>";
	}
	echo "<th></th>";
	echo "</tr>";
	foreach($fellows as $fellow) {
		echo "<tr>";
		echo "<th>".htmlentities($fellow["name"])."</th>";

		foreach($volunteers as $volunteer) {
			echo "<td>";
			$had_any = false;
			foreach($fellow["round_matches"] as $round_number => $volunteer_id)
				if($volunteer_id == $volunteer["id"]) {
					if($had_any)
						echo ", ";
					else
						$had_any = true;
					echo $round_number;
				}
			echo "</td>";
		}

		echo "<th>".htmlentities($fellow["name"])."</th>";

		echo "</tr>";
	}

	echo "<tr>";
	echo "<th></th>";
	foreach($volunteers as $volunteer) {
		echo "<th>".htmlentities($volunteer["name"])."</th>";
	}
	echo "</tr>";


	echo "</table>";
?>
</body>
</html>
