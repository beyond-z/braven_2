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
	global $match_history;

//echo "<pre>"; print_r($matches); echo "</pre>";

	if (!empty($matches)) {
		echo '<h2 id="matches">Matches:</h2>';
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
						<td data-volunteer-matches="<?php
							$first_written = true;
							foreach($match_history as $match_array)
							foreach($match_array as $match_pair)
							foreach($match_pair as $lvid => $lfid)
								if($lvid == $volunteer_key) {
									if(!$first_written)
										echo ",";
									$first_written = false;
									echo $lfid;
								}
						?>" data-volunteer-id="<?php echo $volunteer_key; ?>"><div data-fellow-id="<?php echo $fellow_key;?>"><?php echo $fellows[$fellow_key]['name']; ?></div></td>
						<td><div><?php echo $fellows[$fellow_key]['score']; ?></div></td>
						<td><div><?php bz_list_items($fellows[$fellow_key]['interests']); ?></div></td>
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
				echo "<input type=\"hidden\" data-vid=\"$vid\" name=\"matches[$vid]\" value=\"{$fellow_ID}\" />";
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
