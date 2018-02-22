<?php

include("database_credentials.php");

$pdo_opt = [
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, $pdo_opt);

/**
	Use this to load volunteers into the $volunteers array in the
	format the other file expects.
*/
function load_volunteers_from_database($event_id) {
	global $pdo;

	$statement = $pdo->prepare("
		SELECT
			volunteers.id,
			volunteers.name,
			volunteers.vip,
			volunteers.available,
			volunteers.virtual,
			volunteers.contact_number,
			interests.interest
		FROM
			volunteers
		LEFT OUTER JOIN
			volunteer_interests ON volunteer_interests.volunteer_id = volunteers.id
		LEFT OUTER JOIN
			interests ON volunteer_interests.interest_id = interests.id
		WHERE
			volunteers.event_id = ?
	");

	$volunteers = array();

	$statement->execute(array($event_id));
	while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		if(isset($volunteers[$row["id"]])) {
			// the join will retrieve each interest as a new row
			$volunteers[$row["id"]]["interests"][] = $row["interest"];
		} else {
			// translate db format to what the rest of the code expects
			$volunteers[$row["id"]] = array(
				'name' => $row["name"],
				'vip' => $row["vip"],
				'interests' => $row["interest"] ? array( $row["interest"] ) : array(),
				'available' => $row["available"],
				'virtual' => $row["virtual"],
				'number' => $row["contact_number"],
			);
		}
	}

	return $volunteers;
}

function save_volunteers_to_database($event_id, $volunteers) {
	global $pdo;

	$statement = $pdo->prepare("
		INSERT INTO
			volunteers
			(event_id, name, vip, available, virtual, contact_number)
		VALUES
			(?, ?, ?, ?, ?, ?)
	");

	$interest_statement = $pdo->prepare("
		INSERT INTO
			volunteer_interests
			(volunteer_id, interest_id)
		VALUES
			(?, ?)
	");

	foreach($volunteers as $volunteer) {
		$statement->execute(array(
			$event_id,
			$volunteer["name"],
			$volunteer["vip"],
			$volunteer["available"],
			$volunteer["virtual"],
			$volunteer["number"]
		));

		$id = $pdo->lastInsertId();
		foreach($volunteer["interests"] as $interest) {
			$interest_statement->execute(array($id, get_interest_id($interest)));
		}
	}
}

/**
	Use this to load fellows into the $fellows array in the
	format the other file expects.
*/
function load_fellows_from_database($event_id) {
	global $pdo;

	$statement = $pdo->prepare("
		SELECT
			fellows.id,
			fellows.name,
			fellows.score,
			fellows.available,
			fellows.match_count,
			interests.interest
		FROM
			fellows
		LEFT OUTER JOIN
			fellow_interests ON fellow_interests.fellow_id = fellows.id
		LEFT OUTER JOIN
			interests ON fellow_interests.interest_id = interests.id
		WHERE
			fellows.event_id = ?
	");

	$fellows = array();

	$statement->execute(array($event_id));
	while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		if(isset($fellows[$row["id"]])) {
			// the join will retrieve each interest as a new row
			$fellows[$row["id"]]["interests"][] = $row["interest"];
		} else {
			// translate db format to what the rest of the code expects
			$fellows[$row["id"]] = array(
				'UUID' => $row["id"],
				'name' => $row["name"],
				'score' => $row["score"],
				'interests' => $row["interest"] ? array( $row["interest"] ) : array(),
				'available' => $row["available"],
				'match_count' => $row["match_count"],
			);
		}
	}

	return $fellows;
}

function save_fellows_to_database($event_id, $fellows) {
	global $pdo;

	$statement = $pdo->prepare("
		INSERT INTO
			fellows
			(event_id, name, score, available, match_count)
		VALUES
			(?, ?, ?, ?, ?)
	");

	$interest_statement = $pdo->prepare("
		INSERT INTO
			fellow_interests
			(fellow_id, interest_id)
		VALUES
			(?, ?)
	");

	foreach($fellows as $fellow) {
		$statement->execute(array(
			$event_id,
			$fellow["name"],
			$fellow["score"],
			$fellow["available"],
			$fellow["match_count"]
		));

		$id = $pdo->lastInsertId();
		foreach($fellow["interests"] as $interest) {
			$interest_statement->execute(array($id, get_interest_id($interest)));
		}
	}
}


$interal_interest_cache = array();

function get_interest_id($interest) {
	global $pdo;
	global $interal_interest_cache;

	$interest = strtolower($interest);

	if(isset($interal_interest_cache[$interest]))
		return $interal_interest_cache[$interest];

	$statement = $pdo->prepare("SELECT id FROM interests WHERE interest = ?");
	$statement->execute(array($interest));
	$id = 0;
	if($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		$id = $row["id"];
	} else {
		$statement = $pdo->prepare("INSERT INTO interests (interest) VALUES (?)");
		$statement->execute(array($interest));
		$id = $pdo->lastInsertId();
	}

	$interal_interest_cache[$interest] = $id;
	return $id;
}

