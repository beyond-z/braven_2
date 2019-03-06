<?php
	include_once("sso.php");
	requireLogin();

	include_once("db.php");

	$event_id = (int) $_REQUEST["event_id"];

	if($event_id == 0) {
		// need to choose an event
		include_once("event_selection.php");
		show_event_selection_page();
		exit;
	}

	// I need to map match_sets to round numbers for the export,
	// and since our version of mysql doesn't support the necessary
	// function to do it inline, i will have to do it here separately.
	$round_number_mapping = array();
	$statement = $pdo->prepare("
		SELECT
			id
		FROM
			match_sets
		WHERE
			event_id = ?
		ORDER BY
			when_created
	");
	$statement->execute(array($event_id));
	$rn = 0;
	while($row = $statement->fetch()) {
		$rn++;
		$round_number_mapping[$row["id"]] = $rn;
	}


	$statement = $pdo->prepare("
		SELECT
			fellows.name AS supposed_fellow_name,
			volunteers.name AS supposed_volunteer_name,

			match_sets.id AS round_number,

			feedback_for_fellow.fellow_name,
			feedback_for_fellow.fellow_university,
			feedback_for_fellow.interviewer_name,

			feedback_for_fellow.q1,
			feedback_for_fellow.q2,
			feedback_for_fellow.q3,
			feedback_for_fellow.q4,
			feedback_for_fellow.q5,
			feedback_for_fellow.q6,
			feedback_for_fellow.q7,
			feedback_for_fellow.q8,
			feedback_for_fellow.q9,
			feedback_for_fellow.q10,

			feedback_for_fellow.comments,

			feedback_for_fellow.when_started,
			feedback_for_fellow.when_submitted,
			feedback_for_fellow.when_last_changed
		FROM
			feedback_for_fellow
		INNER JOIN
			match_sets_members msm ON msm.match_member_id = feedback_for_fellow.msm_id
		INNER JOIN
			match_sets ON msm.match_set_id = match_sets.id
		INNER JOIN
			fellows ON fellows.id = msm.fellow_id
		INNER JOIN
			volunteers ON volunteers.id = msm.volunteer_id
		WHERE
			match_sets.event_id = ?
		ORDER BY
			fellow_name, match_sets.when_created
	");

	$statement->execute(array($event_id));

	header('Content-Disposition: attachment; filename="interview_grades.csv"');
	header("Content-Type: text/csv");
	header("Content-Transfer-Encoding: binary");

	$fp = fopen("php://output", "w");
	ob_start();

	$first = true;
	while($row = $statement->fetch()) {
		if($first) {
			$headers = array_keys($row);
			fputcsv($fp, $headers);
			$first = false;
		} else {
			// translate sorted IDs into human-readable round numbers
			$row["round_number"] = $round_number_mapping[$row["round_number"]];
		}

		$data = $row;
		fputcsv($fp, $data);
	}

	$string = ob_get_clean();
	exit($string);
