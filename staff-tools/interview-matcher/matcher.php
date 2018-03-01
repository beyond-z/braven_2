<?php
	include_once("sso.php");
	requireLogin();

	include_once("db.php");

	include_once("shared.php");

	$event_id = (int) $_REQUEST["event_id"];
	if($event_id == 0) {
		include_once("event_selection.php");
		show_event_selection_page();
		exit;
	}

?><!DOCTYPE html>
<html lang="en">
<head>
<title>Braven Mock Interview Matcher</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<?php

$volunteers = load_volunteers_from_database($event_id);
$fellows = load_fellows_from_database($event_id);
$match_history = load_match_history($event_id);

// update last-minute changes, if present
foreach($volunteers as &$volunteer) {
	$volunteer_key = $volunteer["id"];
	if (isset($_POST['vol-'.$volunteer_key.'-available'])) {
		// See if we have a manually configured availability setting from last time:
		if ($_POST['vol-'.$volunteer_key.'-available']) {
			if($volunteer['available'] != true)
				save_volunteer_availability_to_database($volunteer_key, true);
			$volunteer['available'] = true;
		} else {
			// if the box is unchecked 
			if($volunteer['available'] != false)
				save_volunteer_availability_to_database($volunteer_key, false);
			$volunteer['available'] = false;
		}
	}
	if (isset($_POST['vol-'.$volunteer_key.'-number'])) {
		if($volunteer['number'] != $_POST['vol-'.$volunteer_key.'-number'])
			save_volunteer_number_to_database($volunteer_key, $_POST['vol-'.$volunteer_key.'-number']);
		$volunteer['number'] = $_POST['vol-'.$volunteer_key.'-number'];
	}
}
unset($volunteer);

foreach($fellows as &$fellow) {
	$fellow_key = $fellow["id"];

	if (isset($_POST['fellow-'.$fellow_key.'-available'])) {
		// See if we have a manually configured availability setting from last time:
		if ($_POST['fellow-'.$fellow_key.'-available']) {
			save_fellow_availability_to_database($fellow_key, true);
			$fellow['available'] = true;
			$fellow_available = 'checked';
		} else {
			// if the box is unchecked 
			save_fellow_availability_to_database($fellow_key, false);
			$fellow['available'] = false;
			$fellow_available = '';
		}
	}
}
unset($fellow);

// calculate necessary stats

$matches = array();

function fellow_was_matched_in_previous_round($fellow_id_to_check) {
	global $match_history;
	$previous_round = 0;
	foreach ($match_history as $round => $match_array) {
		$previous_round = $round;
	}

	foreach ($match_history[$previous_round] as $pair)
	foreach ($pair as $volunteer_id => $fellow_id) {
		if($fellow_id == $fellow_id_to_check)
			return true;
	}

	return false;
}

function times_fellow_matched_historically($fellow_id_to_check) {
	global $match_history;
	$count = 0;
	foreach ($match_history as $match_array) {
		foreach ($match_array as $pair)
		foreach ($pair as $volunteer_id => $fellow_id) {
			if($fellow_id == $fellow_id_to_check)
				$count++;
		}
	}

	return $count;
}

function special_score_sort($a, $b) {
	global $fellows;
	return $fellows[$b[0]]["score"] - $fellows[$a[0]]["score"];
}

/// returns array of array(id, score (lower is better))
function get_fellows_by_matching_priority($fellows, $for_vips) {
	global $matches;

	$fellows_by_matching_priority = array();

	/*
		The way this works is we make a list of fellows to match in this order.

		First are those who have had the least amount of interview opportunities so far.
		Among them, the score is highest (if called for vips) or random shuffled (everyone else).

		But if they were matched in the previous round, it always puts them at the bottom of the list...
		unless they are two behind. Then I'll allow it to catch up.

		If they were already matched in this round, they are right out.

		FIXME: virtual and in person opportunities should be evenly distributed, if possible
			we prioritize live, fall back to virtual if they already had a live one

		Make sure people aren't matched to the same vol again.
	*/
	$lowest_thing = 9999;
	$interview_count_buckets = array();
	foreach($fellows as $fellow) {
		if(!$fellow["available"])
			continue;
		$matched_this_round = in_array($fellow['id'], $matches);
		if($matched_this_round)
			continue;
		$penalty = 0;
		if(fellow_was_matched_in_previous_round($fellow["id"])) {
			// we want to avoid matching in two consecutive rounds, so they are given a sorting
			// penalty, putting them a bit lower on the list to give other people a chance to
			// catch up
			$penalty = 1;
		}

		$c = times_fellow_matched_historically($fellow["id"]);
		$c += $penalty;

		if($c < $lowest_thing)
			$lowest_thing = c;

		if(!isset($interview_count_buckets[$c]))
			$interview_count_buckets[$c] = array();
		$interview_count_buckets[$c][] = array($fellow["id"], $c);
	}

	// I'm not sure if php arrays always come in numeric order or insertion
	// order, so i am explicitly sorting now because this must happen to keep
	// the low-opportunity people at the top
	$keys = array_keys($interview_count_buckets);
	sort($keys);
	foreach($keys as $bucket_id) {
		$bucket = $interview_count_buckets[$bucket_id];
		// and the internal sort varies - vips are done by score, others are randomized.
		if($for_vips) {
			usort($bucket, 'special_score_sort');
		} else {
			shuffle($bucket);
		}
		$fellows_by_matching_priority = array_merge($fellows_by_matching_priority, $bucket);
	}

	// normalize all so zero is lowest match count
	foreach($fellows_by_matching_priority as &$f) {
		$f[1] -= $lowest_thing;
	}
	unset($f);

	return $fellows_by_matching_priority;
}


// and now display the info table
?>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?" . htmlspecialchars($_SERVER['QUERY_STRING']);?>#matches" method="post">
	<input type="hidden" name="event_id" value="<?php echo htmlentities($event_id); ?>" />
	<?php
	if(!empty($volunteers)) {
		// sort volunteers alphabetically and display them so staff can quickly check who's there and who isn't, update station number, etc.:
		$volunteers_sorted = bz_sort_desc_by($volunteers, 'name', SORT_ASC);
		?>
		<table>
			<caption>Volunteers</caption>
			<thead>
				<tr>
					<th>Available?</th>
					<th>Name</th>
					<th>Station or Phone#</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($volunteers_sorted as $volunteer)
				{
					$volunteer_key = $volunteer["id"];
				?>
					<tr id="vol-<?php echo $volunteer_key; ?>" class="<?php echo ($volunteer['vip']) ? 'vip' : ''; ?>">

						<?php
							$volunteer_available = ($volunteer['available']) ? 'checked' : '';
						?>

						<td class="available">
							<input type="hidden" name="vol-<?php echo $volunteer_key; ?>-available" value="0">
							<input type="checkbox" value="1" name="vol-<?php echo $volunteer_key; ?>-available" <?php echo ($volunteer_available);?>></td>
						<td class="name"><?php echo $volunteer['name'];?></td>
						<td class="number">
							<input type="text" name="vol-<?php echo $volunteer_key; ?>-number" value="<?php echo htmlspecialchars($volunteer['number']);?>" />
						</td>
						
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	<?php
	}

	if(!empty($fellows)) {
		// sort fellows alphabetically and display them so staff can quickly check who's there and who isn't, update station number, etc.:
		$fellows_sorted = bz_sort_desc_by($fellows, 'name', SORT_ASC);
	?>
		<table>
			<caption>Fellows</caption>
			<thead>
				<tr>
					<th>Available?</th>
					<th>Name</th>
					<th>Score</th>
				</tr>
			</thead>
			<tbody>
				<?php

				foreach ($fellows_sorted as $fellow) {
					$fellow_key = $fellow["id"];
				?>
					<tr id="fellow-<?php echo $fellow_key; ?>">

						<?php
						$fellow_available = ($fellow['available']) ? 'checked' : '';
						?>

						<td class="available">
							<input type="hidden" name="fellow-<?php echo $fellow_key; ?>-available" value="0">
							<input type="checkbox" value="1" name="fellow-<?php echo $fellow_key; ?>-available" <?php echo ($fellow_available);?>></td>
						<td class="name"><?php echo $fellow['name'];?></td>
						<td class="score"><?php echo $fellow['score'];?></td>
						
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	<?php
	}
	?>
	<br>
	<input type="submit" value="Match!">
</form>
<br>

<?php
/* ************************************************* */
/*  End availability tables */
/* ************************************************* */
?>

<?php

//echo "<pre>"; print_r($match_history); die;

// Sort the fellows and volunteers by VIP status

// Display some stats:
$filter = array(true);
$available_vols = 0;
foreach($volunteers as $v) if($v["available"]) $available_vols++;
$available_fellows = 0;
foreach($fellows as $v) if($v["available"]) $available_fellows++;
echo "We have $available_vols volunteers and $available_fellows Fellows.<br>"; 

?>

<?php

bz_match_volunteers($fellows);

function bz_match_volunteers($fellows) {
	// Iteration 1: Run through volunteers and match each one with a fellow:

	global $volunteers;
	global $matches;
	global $match_history;

	$volunteers_sorted = bz_sort_desc_by($volunteers);

	// vip first by score + interest
	$priorized_fellows = get_fellows_by_matching_priority($fellows, true);

	foreach ($volunteers_sorted as $volunteer) {
		if($volunteer['available'] && $volunteer['vip']) {
			bz_match_with_fellow($priorized_fellows, $volunteer, 'interests');
		} 
	}

	$priorized_fellows = get_fellows_by_matching_priority($fellows, false);

	// non-vip by interests
	foreach ($volunteers_sorted as $volunteer) {
		$volunteer_key = $volunteer["id"];

		if(!array_key_exists($volunteer_key, $matches) 
			&& $volunteer['available']) {
			bz_match_with_fellow($priorized_fellows, $volunteer, 'interests');
		} 
	}

	$priorized_fellows = get_fellows_by_matching_priority($fellows, true);
	// vip by score, non-matching interests
	foreach ($volunteers_sorted as $volunteer) {
		$volunteer_key = $volunteer["id"];
		if($volunteer['available'] && $volunteer['vip']) {
			if(!array_key_exists($volunteer_key, $matches))
				bz_match_with_fellow($priorized_fellows, $volunteer);
		} 
	}

	$priorized_fellows = get_fellows_by_matching_priority($fellows, false);

	// then random to fall back on remaining
	foreach ($volunteers_sorted as $volunteer) {
		$volunteer_key = $volunteer["id"];

		if(!array_key_exists($volunteer_key, $matches) 
			&& $volunteer['available']) {
			bz_match_with_fellow($priorized_fellows, $volunteer);
		} 
	}


	// Display the proposed matches:
	bz_show_proposed_matches();

	// Restore the score-based sorting of the fellows (we shuffled them for iteration 2):
	//$fellows = bz_sort_desc_by($fellows, 'score');
}

function matched_any_round_historically($fellow_id_to_check, $volunteer_id_to_check) {
	global $match_history;
	foreach ($match_history as $match_array) {
		foreach ($match_array as $pair)
		foreach ($pair as $volunteer_id => $fellow_id) {
			if($fellow_id == $fellow_id_to_check && $volunteer_id == $volunteer_id_to_check)
				$count++;
		}
	}

	return $count;

	return false;
}

function bz_match_with_fellow($fellows_to_consider, $volunteer, $match_by = null) {
	
	// Find next available Fellow that matches criteria:

	global $matches;
	global $fellows;
	global $volunteers;

	$volunteer_key = $volunteer["id"];
	$repeat_key = null;
	foreach($fellows_to_consider as $fellow_info) {
		$fellow_id = $fellow_info[0];
		$fellow_matches = $fellow_info[1];

		$matched_this_round = in_array($fellow_id, $matches);
		if($matched_this_round)
			continue;

		if(matched_any_round_historically($fellow_id, $volunteer_key)) {
			$repeat_key = $fellow_id;
			continue;
		}

		if ($match_by) {
			// If we're matching by criterion, need to make sure the following applies as well:
			if (!empty(array_intersect($volunteer[$match_by], $fellows[$fellow_id][$match_by]))) {
				// if we can find an available fellow with matching interests, make the match:

				$fellow_matches -= 1; // bias toward interest matches
				if($fellow_matches <= 0) {
					$matches[$volunteer_key] = $fellow_id;
					return true;
				}
			} 
		} else {
			// if this is a free-for all (no criterion) then just match whatever:
			$matches[$volunteer_key] = $fellow_id;
			return true;
		}
	}

	// only option is a repeat, allow as last resort
	//if($repeat_key)
		//$matches[$volunteer_key] = $repeat_key;

	return false;
}

function bz_sort_desc_by($array, $criterion = 'vip', $direction = SORT_DESC) {
	array_multisort(array_column($array, $criterion), $direction, $array);
	return $array;
}
?>	
</body>
</html>
