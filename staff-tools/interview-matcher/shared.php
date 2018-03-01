<?php

function bz_list_items($array) {
	echo '<ul>';
	foreach ($array as $key => $value) {
		echo '<li>'.$value.'</li>';
	}
	echo '</ul>';
}

function bz_show_proposed_matches($show_button = true) {
	global $matches;
	global $volunteers;
	global $fellows;
	global $event_id;

//echo "<pre>"; print_r($matches); echo "</pre>";

	if (!empty($matches)) {
		echo '<h2 d="matches">Matches:</h2>';
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
		if($show_button) {
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
		}

	} else {
		echo '<h2>No matches possible</h2>';
	}
}
