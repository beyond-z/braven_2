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
		.interview-details tbody tr:last-child th,
		.interview-details tbody tr:last-child td {
			border-bottom: none;
		}
		.interview-details thead th {
			border-bottom: solid 1px black;
			text-align: center;
		}
		.interview-details tfoot th {
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

		.total {
			background: #ddd !important;
		}

		.had-none {
			color: red;
			font-weight: bold;
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
	echo "<h1>".htmlentities(get_event_name($event_id))." - Stats</h1>";

	echo "<h2>Volunteer Matches</h2>";
	echo "<dl>";
	foreach($volunteers as $volunteer) {
		echo "<dt>".htmlspecialchars($volunteer["name"])."</dt>";
		echo "<dd>";
		$first = true;
		foreach($volunteer["round_matches"] as $fid) {
			if(!$first)
				echo ", ";
			if($fid) {
				echo htmlspecialchars($fellows[$fid]["name"]);
				$first = false;
			}
		}
		echo "</dd>";
	}
	echo "</dl>";

	echo "<h2>Fellow Matches</h2>";
	echo "<p>Bold indicates VIP. Italics indicate virtual.</p>";
	echo "<dl>";
	foreach($fellows as $fellow) {
		echo "<dt>".htmlspecialchars($fellow["name"])."</dt>";
		echo "<dd>";
		$first = true;
		$had_none = true;
		foreach($fellow["round_matches"] as $fid) {
			if(!$first)
				echo ", ";
			if($fid) {
				if($volunteers[$fid]["vip"])
					echo "<b>";
				if($volunteers[$fid]["virtual"])
					echo "<i>";
				echo htmlspecialchars($volunteers[$fid]["name"]);
				if($volunteers[$fid]["virtual"])
					echo "</i>";
				if($volunteers[$fid]["vip"])
					echo "</b>";
				$had_none = false;
			} else {
				echo "&lt;bye&gt";
			}
			$first = false;
		}
		if($had_none)
			echo " <span class=\"had-none\">NO MATCHES!</span>";
		if(!$fellow["available"])
			echo " <span>unavailable though</span>";
		echo "</dd>";
	}
	echo "</dl>";



	echo "<h2>Match Matrix</h2>";

	echo "<p>This table shows who matched to whom and in which round. The number in the cell tells the round in which they paired up.</p>";

	echo "<p>If any row or column has a repeated number, that indicates a problem! People can't be in two places at once.</p>";
	echo "<p>Similarly, if any cell has two or more numbers, that is also a problem because it means they matched together more than once and thus wasted time.</p>";

	echo "<table class=\"interview-details\">";
	echo "<col />";
	foreach($volunteers as $volunteer) {
		echo "<col />";
	}

	echo "<thead>";
	echo "<tr>";
	echo "<th></th>";
	echo "<th>Total</th>";
	foreach($volunteers as $volunteer) {
		echo "<th>".htmlentities($volunteer["name"])."</th>";
	}
	echo "<th></th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	$total_totals = 0;
	foreach($fellows as $fellow) {
		echo "<tr>";
		echo "<th>".htmlentities($fellow["name"])."</th>";

		$total = 0;
		foreach($fellow["round_matches"] as $r)
			if($r) $total++;

		echo "<td class=\"total\">$total</td>";

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

		$total_totals += $total;
	}

	echo "<tr>";
	echo "<th>Total</th>";
	echo "<td class=\"total\">$total_totals</td>";
	foreach($volunteers as $volunteer) {
		$total = 0;
		foreach($volunteer["round_matches"] as $r)
			if($r) $total++;
		echo "<td class=\"total\">$total</td>";
	}
	echo "</tr>";

	echo "</tbody>";

	echo "<tfoot>";
	echo "<tr>";
	echo "<th></th>";
	echo "<th>Total</th>";
	foreach($volunteers as $volunteer) {
		echo "<th>".htmlentities($volunteer["name"])."</th>";
	}
	echo "<th></th>";
	echo "</tr>";
	echo "</tfoot>";


	echo "</table>";
?>
</body>
</html>
