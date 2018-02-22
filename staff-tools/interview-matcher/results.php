<!DOCTYPE html>
<html lang="en">
<head>
<title>Interview Matches</title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div id="page">

	<h1>Round [ROUND NUMBER] Matches</h1>

<?php


$results = array(
	array( 
		'room' => 'Room 305',
		'volunteer' => 'Ben',
		'fellow' => 'Yves',
		),
	array( 
		'room' => 'Room 305',
		'volunteer' => 'Ada',
		'fellow' => 'Zelda',
		),
	array( 
		'room' => '32',
		'volunteer' => 'Kiki',
		'fellow' => 'Ray',
		),
	array( 
		'room' => 'Lido Deck',
		'volunteer' => 'Chuck',
		'fellow' => 'Xena',
		),
	array( 
		'room' => '212-555-1234',
		'volunteer' => 'Dale',
		'fellow' => 'Winona',
		),
	array( 
		'room' => 'Room 305',
		'volunteer' => 'Ada',
		'fellow' => 'Aardvark',
		),
);



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
		    return $item1["$column_key"] <=> $item2["$column_key"];
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