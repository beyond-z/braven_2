<!DOCTYPE html>
<html lang="en">
<head>
<title>Braven Mock Interview Matcher</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<?php

include("db.php");

$volunteers = load_volunteers_from_database(1);
$fellows = load_fellows_from_database(1);

$matches = array();

$match_history = load_match_history(1);

// We're also going to figure out who's had the most matches so far, so let's start collecting:
// this can also be done by the db later but for now i am trying to edit as little code in here as i reasonably can
$fellows_to_count = array();
foreach ($match_history as $key => &$value) {
	foreach ($value as $pair_key => &$pair) {
		// pair[0] is the volunteer, [1] is the fellow:
		$fellows_to_count[] = $pair[1];
	}
}

// Count how many times each fellow was matched and get the max number so far:
$fellow_match_counts = array_count_values($fellows_to_count);
$most_matches_so_far = max($fellow_match_counts);


// TODO: MAKE A SIMILAR AVAILABILITY FORM FOR FELLOWS AS WELL

?>

<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
	<?php
	if(!empty($volunteers)) {
		// sort volunteers alphabetically and display them so staff can quickly check who's there and who isn't, update station number, etc.:
		$volunteers = bz_sort_desc_by($volunteers, 'name', SORT_ASC);
		?>
		<table>
			<thead>
				<tr>
					<th>Available?</th>
					<th>Name</th>
					<th>Station or Phone#</th>
				</tr>
			</thead>
			<tbody>
				<?php

				foreach ($volunteers as &$volunteer) {
					$volunteer_key = $volunteer["id"];
					?>
					<tr id="vol-<?php echo $volunteer_key; ?>" class="<?php echo ($volunteer['vip']) ? 'vip' : ''; ?>">

						<?php

						if (isset($_POST['vol-'.$volunteer_key.'-available'])) {
							// See if we have a manually configured availability setting from last time:
							if ($_POST['vol-'.$volunteer_key.'-available']) {
									$volunteer['available'] = true;
									$volunteer_available = 'checked';
								} else {
									// if the box is unchecked 
									$volunteer['available'] = false;
									$volunteer_available = '';
								}
						} else if (isset($volunteer['available'])) {
							// Otherwise use what was originally set in the imported data:
							$volunteer_available = ($volunteer['available']) ? 'checked' : '';
						} else {
							// And if that wasn't defined, just mark as unavailable:
							$volunteer_available = '';
						}						

						?>

						<td class="available">
							<input type="hidden" name="vol-<?php echo $volunteer_key; ?>-available" value="0">
							<input type="checkbox" value="1" name="vol-<?php echo $volunteer_key; ?>-available" <?php echo ($volunteer_available);?>></td>
						<td class="name"><?php echo $volunteer['name'];?></td>
						<td class="number"><?php echo $volunteer['number'];?></td>
						
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

// Sort the fellows and volunteers by VIP status

$volunteers = bz_sort_desc_by($volunteers);
$fellows = bz_sort_desc_by($fellows, 'score');

// Display some stats:
$filter = array(true);
$available_vols = array_filter($volunteers, function($e) use ($filter){
    return in_array($e['available'], $filter);
});
echo 'We have '.count($available_vols).' volunteers and '.count($fellows).' Fellows.<br>'; 

?>

<?php

bz_match_volunteers();

function bz_match_volunteers() {
	// Iteration 1: Run through volunteers and match each one with a fellow:

	global $volunteers;
	global $matches;
	global $fellows;
	global $match_history;

	foreach ($volunteers as &$volunteer) {
		$volunteer_key = $volunteer["id"];
		
		if($volunteer['available']) {
			bz_match_with_fellow($volunteer, 'interests');
		} 
	}
	// Destroy the foreach reference: 
	unset($volunteer);

	/* Iteration 2: Run again for all unmatched volunteers, but first shuffle the fellows to avoid biasing toward stronger fellows: */

	shuffle($fellows);

	foreach ($volunteers as &$volunteer) {
		$volunteer_key = $volunteer["id"];

		if(!array_key_exists($volunteer_key, $matches) 
			&& $volunteer['available']) {
			bz_match_with_fellow($volunteer);
		} 
	}
	// Destroy the foreach reference: 
	unset($volunteer);

	// Display the proposed matches:
	bz_show_proposed_matches();

	// Restore the score-based sorting of the fellows (we shuffled them for iteration 2):
	//$fellows = bz_sort_desc_by($fellows, 'score');


}

function bz_match_with_fellow($volunteer, $match_by = null) {
	
	// Find next available Fellow that matches criteria:

	global $matches;
	global $match_history;
	global $fellows;
	global $volunteers;
	global $fellow_match_counts;
	global $most_matches_so_far;

	$volunteer_key = $volunteer["id"];

	$available_fellows = array();
	foreach ($fellows as $fk => &$fv) {
		if ($fv['available'])
			$available_fellows[$fv['UUID']] = $fv;
	}
	$available_fellow_match_counts = array_intersect_key($fellow_match_counts, $available_fellows);

	foreach($fellows as $fellow_key => &$fellow) {
		// figure out whether Fellow has already been matched earlier this round:
		$matched_this_round = in_array($fellow['UUID'], $matches);
		// and whether this Fellow was matched to this volunteer before:
		$matched_to_this_volunteer_before = false;
		$this_pair = array($volunteer_key => $fellow['UUID']);
		foreach ($match_history as $round_key => $round_matches) {
			// Iterate through all past matches to find this pair:
			if ( !empty( array_intersect_assoc( $round_matches[0], $this_pair ) ) ) {
				$matched_to_this_volunteer_before = true;
			}
		}

		// Make it so each Fellow is matched at least N times beofre anyone gets to be matched N+1 times. 

		// TODO: FIX THIS! If fellow was never matched, OR if fellow hasn't been matched to the max, OR if everyone has been matched the same number of times:
		$not_over_max = false;

			if ( !isset($fellow_match_counts[$fellow['UUID']]) || $fellow_match_counts[$fellow['UUID']] < max($available_fellow_match_counts) || min($available_fellow_match_counts) == max($available_fellow_match_counts) ) {
				
				$not_over_max = true;
				
			}

		// and whehter they're even available:
		$fellow_available = $fellow['available'];

		if (!$matched_this_round 
			&& !$matched_to_this_volunteer_before 
			&& $not_over_max
			&& $fellow_available

			// TODO: If optimization is needed, I'm sure we can cascade the above filters so we don't run through all fellows several times.

			) {
			// We already know the volunteer is available otherwise this function wouldn't have been called.

			if ($match_by) {
				// If we're matching by criterion, need to make sure the following applies as well:
				if (array_intersect($volunteer[$match_by], $fellow[$match_by])) {
					// if we can find an available fellow with matching interests, make the match:

					// TODO: MAKE IT SO WE USE VOL ID RATHER THAN ARRAY KEYS!

					$matches[$volunteer_key] = $fellow['UUID'];

					// increment the match count so we can avoid over-/under-matching fellows
					// TODO: This is a vestigial feature, should probably be merged with whatever we use to count match history, or renamed to avoid confusion.
					$fellow['match_count'] ++;
					break;
				} 
			} else {
				// if this is a free-for all (no criterion) then just match whatever:
				$matches[$volunteer_key] = $fellow['UUID'];
				$fellow['match_count'] ++;
				break;

			}
		}
	}
	// Destroy the foreach reference:
	unset($fellow);

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
				foreach ($matches as $volunteer_key => $fellow_ID) {
					// Figure out who the fellow is:
					$fellow_key = array_search($fellow_ID, array_column($fellows, 'UUID'));
					$volunteer_key = array_search($volunteer_key, array_column($volunteers, 'id'));

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
			<?php
			foreach ($matches as $volunteer_key => $fellow_ID) {
				echo "<input type=\"hidden\" name=\"matches[$volunteer_key]\" value=\"{$fellow_ID}\" />";
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
