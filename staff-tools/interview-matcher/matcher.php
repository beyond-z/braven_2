<?php
	include_once("sso.php");
	requireAdmin();

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
<style>
.inside-dragging {
	border: dashed 2px black;
}
[draggable] {
	cursor: pointer;
}
.double-interview {
	color: red !important;
}
</style>
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

function current_round_number() {
	global $match_history;
	$count = 1;
	foreach ($match_history as $match_array) {
		$count++;
	}

	return $count;
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
			if(current_round_number() < 4)
				$penalty = 2; // rounds 1,2,3 really don't try to do back-to-back
			else
				$penalty = 0; // but for the final rounds, give people an easier chance to catch up, allowing back-to-back more easily
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
echo "Proposing for round ".current_round_number()."...";

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
			&& !$volunteer['vip']
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
				return true;
		}
	}

	return false;
}

function fellow_matched_virtual_count_historically($fellow_id_to_check) {
	global $match_history;
	global $volunteers;
	$count = 0;
	foreach ($match_history as $match_array) {
		foreach ($match_array as $pair)
		foreach ($pair as $volunteer_id => $fellow_id) {
			if($fellow_id == $fellow_id_to_check && $volunteers[$volunteer_id]["virtual"])
				$count++;
		}
	}

	return $count;
}

function bz_match_with_fellow($fellows_to_consider, $volunteer, $match_by = null) {
	
	// Find next available Fellow that matches criteria:

	global $matches;
	global $fellows;
	global $volunteers;

	$best_match = 0;
	$best_match_score = 999; // lower is better

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

		// if this is a virtual interview, we want to bias against it to up the odds of
		// getting an in-person slot if the fellow has been virtual before
		if ($volunteer["virtual"]) {
			// in Feb 2019, this changed to be an enormous score - we'd rather match
			// fellows with at least one in-person interview, even if interests don't match.
			$fellow_matches += 0.6 * fellow_matched_virtual_count_historically($fellow_id);
		}

		if ($match_by) {
			// If we're matching by criterion, need to make sure the following applies as well:
			if (!empty(array_intersect($volunteer[$match_by], $fellows[$fellow_id][$match_by]))) {
				// if we can find an available fellow with matching interests, make the match:

				$fellow_matches -= 0.5; // slightly bias toward interest matches
				if($fellow_matches <= 0 && $fellow_matches < $best_match_score) {
					$best_match = $fellow_id;
					$best_match_score = $fellow_matches;
				}
			} 
		} else {
			// if this is a free-for all (no criterion) then just match whatever:
			if($fellow_matches < $best_match_score) {
				// the point here is to make sure people get an opportunity to go
				$best_match = $fellow_id;
				$best_match_score = $fellow_matches;
			}
		}
	}

	if($best_match) {
		$matches[$volunteer_key] = $best_match;
		return true;
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


<script>
	function swapNodeChild(a, b) {
		var c1 = a.firstChild;
		var c2 = b.firstChild;
		a.removeChild(c1);
		b.removeChild(c2);
		a.appendChild(c2);
		b.appendChild(c1);
	}

	function warnOnDoubled(p) {
		var c = p.firstChild;
		var existingMatches = p.getAttribute("data-volunteer-matches");
		if(existingMatches != null && existingMatches != "")
			existingMatches = existingMatches.split(",");
		else
			existingMatches = [];

		var hadMatch = false;
		for(var i = 0; i < existingMatches.length; i++) {
			if(existingMatches[i] == c.getAttribute("data-fellow-id")) {
				hadMatch = true;
				//alert(existingMatches[i]+ " == "+ c.getAttribute("data-fellow-id"));
				alert("This match would put a fellow with the same interviewer twice.");
				break;
			}
		}

		if(hadMatch)
			p.parentNode.classList.add("double-interview");
		else
			p.parentNode.classList.remove("double-interview");
	}



	var pm = document.getElementById("proposed-matches");
	if(pm) {
		var fellows = pm.querySelectorAll("[data-fellow-id]");
		var currentlyDragging;
		for(var i = 0; i < fellows.length; i++) {
			var f = fellows[i];
			f.setAttribute("draggable", "true");

			f.addEventListener("dragstart", function(event) {
				event.dataTransfer.setData("Text", event.target.getAttribute("data-fellow-id"));
				currentlyDragging = this;
			});

			f.parentNode.addEventListener("dragenter", function(event) {
				event.preventDefault();
				this.className += " inside-dragging";
			});
			f.parentNode.addEventListener("dragleave", function(event) {
				this.className = this.className.replace(" inside-dragging", "");
			});
			f.parentNode.addEventListener("dragover", function(event) {
				event.preventDefault();
			});
			f.parentNode.addEventListener("drop", function(event) {
				event.preventDefault();
				event.stopPropagation();
				this.className = this.className.replace(" inside-dragging", "");

				if(this == currentlyDragging.parentNode)
					return; // drop back in itself, no work needed

				// change the form value (this is what counts!)
				document.querySelector("input[data-vid=\"" + this.getAttribute("data-volunteer-id") + "\"]").
					value = currentlyDragging.getAttribute("data-fellow-id");
				document.querySelector("input[data-vid=\"" + currentlyDragging.parentNode.getAttribute("data-volunteer-id") + "\"]").
					value = this.firstChild.getAttribute("data-fellow-id");

				// and update the UI so people know what is going on

				// the fellow name field
				var toSwap = this.firstChild;
				this.removeChild(this.firstChild);
				var oldParent = currentlyDragging.parentNode;
				currentlyDragging.parentNode.removeChild(currentlyDragging);
				this.appendChild(currentlyDragging);
				oldParent.appendChild(toSwap);

				// the score field
				var n = this.nextElementSibling;
				var n2 = oldParent.nextElementSibling;
				swapNodeChild(n, n2);

				// the interests field
				n = n.nextElementSibling;
				n2 = n2.nextElementSibling;
				swapNodeChild(n, n2);

				warnOnDoubled(this);
				warnOnDoubled(oldParent);

				currentlyDragging = null;
			});
		}
	}
</script>
</body>
</html>
