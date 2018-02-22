<!DOCTYPE html>
<html lang="en">
<head>
<title>Braven Mock Interview Matcher</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<?php


$volunteers = array(

	array(
		'name' => 'BART',
		'vip' => 'VIP',
		'interests' => array( 'psychology', 'sociology' ),
		'available' => false,
		'virtual' => true,
		'number' => '212-555-1234',
	),
	array(
		'name' => 'HOMER',
		'vip' => 'VIP',
		'interests' => array( 'psychology', 'medicine' ),
		'available' => false,
		'virtual' => true,
		'number' => '212-555-1234',
	),
	array(
		'name' => 'LISA',
		'vip' => 'VIP',
		'interests' => array( 'sociology', 'medicine' ),
		'available' => true,
		'number' => 'Room 301',
	),
	array(
		'name' => 'AL',
		'vip' => '',
		'interests' => array( 'psychology', 'sociology', 'medicine' ),
		'available' => true,
		'virtual' => true,
		'number' => '212-555-1234',
	),
	array(
		'name' => 'PEGGY',
		'vip' => '',
		'interests' => array( 'psychology', 'medicine' ),
		'available' => true,
		'number' => 'Room 123',
	),
	array(
		'name' => 'BUD',
		'vip' => '',
		'interests' => array( 'sociology', 'medicine' ),
		'available' => true,
		'virtual' => true,
		'number' => '212-555-1234',
	),
	array(
		'name' => 'KELLY',
		'vip' => '',
		'interests' => array( 'junk science', 'spaceships' ),
		'available' => true,
		'number' => 'Lido Deck',
	),
	array(
		'name' => 'MARGE',
		'vip' => 'VIP',
		'interests' => array( 'junk science', 'spaceships' ),
		'available' => true,
		'number' => 'Room 23',
	),


);

$fellows = array(
	array(
		'UUID' => 'F001',
		'name' => 'Anne',
		'score' => '4',
		'interests' => array( 'law', 'order' ),
		'available' => false,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F002',
		'name' => 'Burt',
		'score' => '16',
		'interests' => array( 'pride', 'prejudice' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F003',
		'name' => 'Chuck',
		'score' => '15',
		'interests' => array( 'theology', 'finance' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F004',
		'name' => 'Dave',
		'score' => '16',
		'interests' => array(),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F005',
		'name' => 'Emma',
		'score' => '23',
		'interests' => array( 'gerbils' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F006',
		'name' => 'Fred',
		'score' => '42',
		'interests' => array( 'france' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F007',
		'name' => 'Gina',
		'score' => '11',
		'interests' => array( 'entomology' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F008',
		'name' => 'Helen',
		'score' => '38',
		'interests' => array( 'law' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F009',
		'name' => 'Iris',
		'score' => '10',
		'interests' => array( 'law' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F010',
		'name' => 'Jane',
		'score' => '15',
		'interests' => array( 'psychology', 'medicine' ),
		'available' => false,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F011',
		'name' => 'Karl',
		'score' => '74',
		'interests' => array( 'psychology', 'medicine' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F012',
		'name' => 'Lily',
		'score' => '74',
		'interests' => array( 'theology', 'medicine' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F013',
		'name' => 'Mimi',
		'score' => '50',
		'interests' => array( 'psychology', 'medicine' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F014',
		'name' => 'Nate',
		'score' => '58',
		'interests' => array( 'medicine', 'marketing' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F015',
		'name' => 'Opie',
		'score' => '25',
		'interests' => array( 'medicine' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F016',
		'name' => 'Pete',
		'score' => '25',
		'interests' => array( 'entomology' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F017',
		'name' => 'Quinn',
		'score' => '75',
		'interests' => array( 'law' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F018',
		'name' => 'Rita',
		'score' => '',
		'interests' => array( 'medicine', 'marketing' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F019',
		'name' => 'Sam',
		'score' => '66',
		'interests' => array( 'medicine' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F020',
		'name' => 'Tina',
		'score' => '34',
		'interests' => array( 'entomology' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F021',
		'name' => 'Ursula',
		'score' => '22',
		'interests' => array( 'law' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F022',
		'name' => 'Val',
		'score' => '34',
		'interests' => array( 'medicine', 'marketing' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F023',
		'name' => 'Wendy',
		'score' => '13',
		'interests' => array( 'medicine' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F024',
		'name' => 'Xavier',
		'score' => '17',
		'interests' => array( 'entomology' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F025',
		'name' => 'Yvette',
		'score' => '19',
		'interests' => array( 'law' ),
		'available' => true,
		'match_count' => 0,
	),
	array(
		'UUID' => 'F026',
		'name' => 'Zeb',
		'score' => '23',
		'interests' => array( 'law' ),
		'available' => true,
		'match_count' => 0,
	),
);

$matches = array();
$filename = 'matches.csv';

// Get history and parse it to an array that we can use to avoid re-matching identical pairs.
// I know this is a dumb way to do it, but I still suck at this... :(
$filename = 'matches.csv';
$str = file_get_contents($filename);
$match_history = explode('|', $str);
// We're also going to figure out who's had the most matches so far, so let's start collecting:
$fellows_to_count = array();
foreach ($match_history as $key => &$value) {
	$value = explode(',', $value);
	foreach ($value as $pair_key => &$pair) {
		$pair = explode(':', $pair);
		// pair[0] is the volunteer, [1] is the fellow:
		$fellows_to_count[] = $pair[1];
		// Make the volunteer into the key:
		$pair = array($pair[0] => $pair[1]);
	
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

				foreach ($volunteers as $volunteer_key => &$volunteer) {
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
	global $volunteer_key;
	global $fellows;
	global $match_history;

	foreach ($volunteers as $volunteer_key => &$volunteer) {
		
		if($volunteer['available']) {
			bz_match_with_fellow($volunteer, 'interests');
		} 
	}
	// Destroy the foreach reference: 
	unset($volunteer);

	/* Iteration 2: Run again for all unmatched volunteers, but first shuffle the fellows to avoid biasing toward stronger fellows: */

	shuffle($fellows);

	foreach ($volunteers as $volunteer_key => &$volunteer) {
		
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

	global $volunteer_key;
	global $matches;
	global $match_history;
	global $fellows;
	global $volunteers;
	global $fellow_match_counts;
	global $most_matches_so_far;

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

			if ( null == $fellow_match_counts[$fellow['UUID']] || $fellow_match_counts[$fellow['UUID']] < max($available_fellow_match_counts) || min($available_fellow_match_counts) == max($available_fellow_match_counts) ) {
				
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

		// Create a string we can save into the match history using the following loop.
		// TODO: this is a crappy way to do it, of course... :)
		$matches_str = '';
		foreach ($matches as $volunteer_key => $fellow_ID) {
			
			
		}
		$matches_str .= '|';
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

					// Add this matched pair to the string we're going to save to history:
					$matches_str .= $volunteer_key .':'.$fellow_ID.',';

					// Figure out who the fellow is:
					$fellow_key = array_search($fellow_ID, array_column($fellows, 'UUID'));

					?>
					<tr class="<?php echo ($volunteers[$match_key]['vip']) ? 'vip' : ''; // Use VIP status to style the row ?>">
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
			<input type="hidden" name="matches" value="<?php echo $matches_str;?>">
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