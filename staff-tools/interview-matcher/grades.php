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


	$statement = $pdo->prepare("
		SELECT
			fellows.name AS supposed_fellow_name,
			volunteers.name AS supposed_volunteer_name,

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

			feedback_for_fellow.comments
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
			fellow_name
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
		}

		$data = $row;
		fputcsv($fp, $data);
	}

	$string = ob_get_clean();
	exit($string);
