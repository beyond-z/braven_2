<?php
	include_once("sso.php");
	requireLogin();

	include_once("db.php");

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
	return $fellows[$b]["score"] - $fellows[$a]["score"];
}

function get_fellows_by_matching_priority($fellows, $for_vips) {
	global $matches;

	$fellows_by_matching_priority = array();

	/*
		The way this works is we make a list of fellows to match in this order.

		First are those who have had the least amount of interview opportunities so far.
		Among them, the score is highest (if called for vips) or random shuffled (everyone else).

		But if they were matched in the previous round, it always puts them at the bottom of the list.

		If they were already matched in this round, they are right out.

		FIXME: virtual and in person opportunities should be evenly distributed, if possible
			we prioritize live, fall back to virtual if they already had a live one

		Make sure people aren't matched to the same vol again.
	*/
	$matched_in_previous_round = array();
	$interview_count_buckets = array();
	foreach($fellows as $fellow) {
		$matched_this_round = in_array($fellow['id'], $matches);
		if($matched_this_round)
			continue;
		if(fellow_was_matched_in_previous_round($fellow["id"])) {
			// we want to avoid matching in two consecutive rounds, so they are done in a separate list for the bottom
			$matched_in_previous_round[] = $fellow["id"];
			continue;
		}

		$c = times_fellow_matched_historically($fellow["id"]);
		if(!isset($interview_count_buckets[$c]))
			$interview_count_buckets[$c] = array();
		$interview_count_buckets[$c][] = $fellow["id"];
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

	// it only appends the last rounds ones if we had new unique ones for this round to avoid
	// potential infinite recursion (this means someone was taken off the list at least)
	// if everyone was matched in every round
	if(!empty($fellows_by_matching_priority))
		$fellows_by_matching_priority = array_merge($fellows_by_matching_priority, get_fellows_by_matching_priority($matched_in_previous_round, $for_vips));
	else {
		// no new matches; the event is prolly over. staff can improvise if need be to kill time or just dismiss
		// shouldn't happen in practice cuz we have a lot more students than volunteers.
	}

	return $fellows_by_matching_priority;
}


// and now display the info table
?>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?" . htmlspecialchars($_SERVER['QUERY_STRING']);?>" method="post">
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

	// vip first by score + interst
	$priorized_fellows = get_fellows_by_matching_priority($fellows, true);

	foreach ($volunteers_sorted as $volunteer) {
		if($volunteer['available'] && $volunteer['vip']) {
			bz_match_with_fellow($priorized_fellows, $volunteer, 'interests');
		} 
	}

	// vip by score
	foreach ($volunteers_sorted as $volunteer) {
		$volunteer_key = $volunteer["id"];
		if($volunteer['available'] && $volunteer['vip']) {
			if(!array_key_exists($volunteer_key, $matches))
				bz_match_with_fellow($priorized_fellows, $volunteer);
		} 
	}



	/* Iteration 2: Run again for all unmatched volunteers, but first shuffle the fellows to avoid biasing toward stronger fellows: */
	$priorized_fellows = get_fellows_by_matching_priority($fellows, false);

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

function bz_match_with_fellow($fellow_to_consider, $volunteer, $match_by = null) {
	
	// Find next available Fellow that matches criteria:

	global $matches;
	global $fellows;
	global $volunteers;

	$volunteer_key = $volunteer["id"];
	$repeat_key = null;
	foreach($fellow_to_consider as $fellow_id) {
		$matched_this_round = in_array($fellow_id, $matches);
		if($matched_this_round)
			continue;

		if(matched_any_round_historically($fellow_id, $volunteer_key)) {
			$repeat_key = $fellow_id;
			continue;
		}

		if ($match_by) {
			// If we're matching by criterion, need to make sure the following applies as well:
			if (array_intersect($volunteer[$match_by], $fellows[$fellow_id][$match_by])) {
				// if we can find an available fellow with matching interests, make the match:

				$matches[$volunteer_key] = $fellow_id;
				return;
			} 
		} else {
			// if this is a free-for all (no criterion) then just match whatever:
			$matches[$volunteer_key] = $fellow_id;
			return;
		}
	}

	// only option is a repeat, allow as last resort
	//if($repeat_key)
		//$matches[$volunteer_key] = $repeat_key;
}

function bz_sort_desc_by($array, $criterion = 'vip', $direction = SORT_DESC) {
	array_multisort(array_column($array, $criterion), $direction, $array);
	return $array;
}
	 
function bz_list_items($array) {
	echo '<ul>';
	foreach ($array as $key => $value) {
		echo '<li>'.$value.'</li>';
	}
	echo '</ul>';
}

function bz_show_proposed_matches() {
	global $matches;
	global $volunteers;
	global $fellows;
	global $event_id;

//echo "<pre>"; print_r($matches); echo "</pre>";

	if (!empty($matches)) {
		echo '<h2>Matches:</h2>';
		echo '<br>';

		?>
		<table id="proposed-matches">
			<thead>
				<tr>
					<th>Station/Number</th>
					<th>Interviewer</th>
					<th>Fellow</th>
					<th>Fellow Score</th>
					<th>Fellow Interests</th>
					<th>Interviewer Fields</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($matches as $volunteer_key => $fellow_key) {
					?>
					<tr class="<?php echo ($volunteers[$volunteer_key]['vip']) ? 'vip' : ''; // Use VIP status to style the row ?>">
						<td><?php echo $volunteers[$volunteer_key]['number']; ?></td>
						<td class="name"><?php echo $volunteers[$volunteer_key]['name']; ?></td>
						<td><?php echo $fellows[$fellow_key]['name']; ?></td>
						<td><?php echo $fellows[$fellow_key]['score']; ?></td>
						<td><?php bz_list_items($fellows[$fellow_key]['interests']); ?></td>
						<td><?php bz_list_items($volunteers[$volunteer_key]['interests']); ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<br>
		<?php
		?>

		<form action="match-history-saver.php" method="post">
			<input type="hidden" name="event_id" value="<?php echo htmlentities($event_id); ?>" />
			<?php
			foreach ($matches as $vid => $fellow_ID) {
				//$volunteer_key = array_search($volunteer_key, array_column($volunteers, 'id'));
				//$vid = $volunteers[$volunteer_key]["id"];
				echo "<input type=\"hidden\" name=\"matches[$vid]\" value=\"{$fellow_ID}\" />";
			}
			?>
			<input type="submit" value="Finalize match!">
		</form>
		<?php

	} else {
		echo '<h2>No matches possible</h2>';
	}
}
?>	
</body>
</html>
