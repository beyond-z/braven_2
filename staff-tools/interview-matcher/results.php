<?php
	include_once("db.php");

	$event_id = (int) $_REQUEST["event_id"];

	if($event_id == 0) {
		// need to choose an event
		include_once("event_selection.php");
		show_event_selection_page();
		exit;
	}

	$round_number = (int) $_REQUEST["round_number"];

	if($round_number == 0) {
		$round_number = 1;
	}

	$matches = load_match_history($event_id);
	$fellows = load_fellows_from_database($event_id);
	$volunteers = load_volunteers_from_database($event_id);

	$results = array();

	$next_round = 0;
	$previous_round = 0;

	// just arranging the data from the db into the presentation format expected below
	$looping_round = 0;
	foreach($matches as $match_set) {
		$looping_round++;
		if($looping_round == $round_number) {
			foreach($match_set as $match_pair) {
				foreach($match_pair as $volunteer_id => $fellow_id) {
					$results[] = array (
						"room" => $volunteers[$volunteer_id]["number"],
						"volunteer" => $volunteers[$volunteer_id]["name"],
						"fellow" => $fellows[$fellow_id]["name"]
					);
				}
			}
		}
		if($looping_round == $round_number - 1)
			$previous_round = $looping_round;
		if($looping_round == $round_number + 1)
			$next_round = $looping_round;
	}


?><!DOCTYPE html>
<html lang="en">
<head>
<title>Interview Matches</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div id="page">

	<h1><?php echo htmlentities(get_event_name($event_id)); ?>: Round <?php echo $round_number ?> Matches</h1>
	<?php
		if($previous_round)
			echo "<a href=\"results.php?event_id=$event_id&round_number=$previous_round\">Previous Round</a>";
		echo " ";
		if($next_round)
			echo "<a href=\"results.php?event_id=$event_id&round_number=$next_round\">Next Round</a>";
	?>

<?php

$displays = array(
	array(
		'display_name' => 'by-room',
		'title' => 'By Room / Station / Number',
		'columns'=> array(
			'room' => 'Room / Station / Number',
			'volunteer' => 'Volunteer',
			'fellow' => 'Fellow',
		),
	),
	array(
		'display_name' => 'by-fellow',
		'title' => 'By Fellow',
		'columns'=> array(
			'fellow' => 'Fellow',
			'room' => 'Room / Station / Number',
			'volunteer' => 'Volunteer',
		),
	),
);

foreach ($displays as $display_key => $display) {

	// Sort by the columns from last to first (i.e. the leftmost column is primary, next is secondary, etc.):
	$columns = array_reverse($display['columns']);
	foreach ($columns as $column_key => $column) {
		usort($results, function ($item1, $item2) {
			global $column_key;
		    return strcmp($item1["$column_key"], $item2["$column_key"]);
		});
	}



	?>
	<div id="<?php echo $display['display_name'];?>">
		<h2><?php echo 'Browse by ' . $display['title'] ;?></h2>
		<table>
			<thead>
				<tr>
					<?php foreach ($display['columns'] as $column_key => $column) { ?>
						<th><?php echo $column;?></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>

				<?php

				foreach ($results as $key => $result) {
					?>
					<tr>
						<?php foreach ($display['columns'] as $column_key => $column) { ?>
						<td><?php echo $result[$column_key];?></td>
						<?php } ?>
					</tr>

					<?php
				}


				?>
			</tbody>
		</table>
	</div>
	<?php }	?>
</div>

</body>
</html>
