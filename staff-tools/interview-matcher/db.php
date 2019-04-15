<?php

require_once("credentials.php");

$pdo_opt = [
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO("mysql:host={$WP_CONFIG["DB_HOST"]};dbname={$WP_CONFIG["DB_INTERVIEW_MATCHER_NAME"]};charset=utf8mb4", $WP_CONFIG["DB_USER"], $WP_CONFIG["DB_PASSWORD"], $pdo_opt);

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
			volunteers.is_virtual,
			volunteers.contact_number,
			volunteers.feedback_nag_address,
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
				'id' => $row["id"],
				'name' => $row["name"],
				'vip' => $row["vip"],
				'interests' => $row["interest"] ? array( $row["interest"] ) : array(),
				'available' => $row["available"],
				'virtual' => $row["is_virtual"],
				'number' => $row["contact_number"],
				'feedback_nag_address' => $row['feedback_nag_address']
			);
		}
	}

	return $volunteers;
}

function update_volunteer_in_database($id, $volunteer) {
	global $pdo;

	$statement = $pdo->prepare("
		UPDATE volunteers SET
			name = ?, vip = ?, available = ?, is_virtual = ?, contact_number = ?, feedback_nag_address = ?
		WHERE
			id = ?
	");

	$statement->execute(array(
		$volunteer["name"],
		$volunteer["vip"] ? 1 : 0,
		$volunteer["available"] ? 1 : 0,
		$volunteer["virtual"] ? 1 : 0,
		$volunteer["number"],
		$volunteer["feedback_nag_address"],
		$id
	));

	$s = $pdo->prepare("DELETE FROM volunteer_interests WHERE volunteer_id = ?");
	$s->execute(array($id));

	saveVolunteerInterests($id, array_map("trim", explode(";", $volunteer["interests"])));
}

function save_volunteers_to_database($event_id, $volunteers) {
	global $pdo;

	$statement = $pdo->prepare("
		INSERT INTO
			volunteers
			(event_id, name, vip, available, is_virtual, contact_number, feedback_nag_address)
		VALUES
			(?, ?, ?, ?, ?, ?, ?)
	");

	foreach($volunteers as $volunteer) {
		$statement->execute(array(
			$event_id,
			$volunteer["name"],
			$volunteer["vip"] ? 1 : 0,
			$volunteer["available"] ? 1 : 0,
			$volunteer["virtual"] ? 1 : 0,
			$volunteer["number"],
			$volunteer["feedback_nag_address"]
		));

		$id = $pdo->lastInsertId();
		saveVolunteerInterests($id, $volunteer["interests"]);
	}
}

function saveVolunteerInterests($id, $interests) {
	global $pdo;

	$interest_statement = $pdo->prepare("
		INSERT INTO
			volunteer_interests
			(volunteer_id, interest_id)
		VALUES
			(?, ?)
	");

	$ints_done = array();
	foreach($interests as $interest) {
		$interest = trim(strtolower($interest));
		if($interest == "" || isset($ints_done[$interest]))
			continue;
		$ints_done[$interest] = true;
		$interest_statement->execute(array($id, get_interest_id($interest)));
	}
}

/**
	Updates the availability for an existing volunteer.
*/
function save_volunteer_availability_to_database($volunteer_id, $is_available) {
	global $pdo;
	$statement = $pdo->prepare("UPDATE volunteers SET available = ? WHERE id = ?");
	$statement->execute(array($is_available ? 1 : 0, $volunteer_id));
}

/**
	Updates the number field for an existing volunteer.
*/
function save_volunteer_number_to_database($volunteer_id, $number) {
	global $pdo;
	$statement = $pdo->prepare("UPDATE volunteers SET contact_number = ? WHERE id = ?");
	$statement->execute(array($number, $volunteer_id));
}

/**
	Updates the availability for an existing fellow.
*/
function save_fellow_availability_to_database($fellow_id, $is_available) {
	global $pdo;
	$statement = $pdo->prepare("UPDATE fellows SET available = ? WHERE id = ?");
	$statement->execute(array($is_available ? 1 : 0, $fellow_id));
}

/**
	Use this to load fellows into the $fellows array in the
	format the other file expects.
*/
function load_fellows_from_database($event_id) {
	global $pdo;

	// FIXME: do we need match_count? COUNT() AS match_count
	$statement = $pdo->prepare("
		SELECT
			fellows.id,
			fellows.name,
			fellows.score,
			fellows.available,
			interests.interest,
			fellows.email_address,
			fellows.phone_number
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
				'id' => $row["id"],
				'name' => $row["name"],
				'score' => $row["score"],
				'interests' => $row["interest"] ? array( $row["interest"] ) : array(),
				'available' => $row["available"],
				'email_address' => $row["email_address"],
				'phone_number' => $row["phone_number"],
				'match_count' => 0 // FIXME do we need this at all? or do we need it populated?
			);
		}
	}

	return $fellows;
}

function update_fellow_in_database($id, $fellow) {
	global $pdo;

	$statement = $pdo->prepare("
		UPDATE fellows SET
			name = ?, score = ?, available = ?, email_address = ?, phone_number = ?
		WHERE
			id = ?
	");

	$statement->execute(array(
		$fellow["name"],
		(int) $fellow["score"],
		$fellow["available"] ? 1 : 0,
		$fellow["email_address"],
		$fellow["phone_number"],
		$id
	));

	$s = $pdo->prepare("DELETE FROM fellow_interests WHERE fellow_id = ?");
	$s->execute(array($id));

	saveFellowInterests($id, array_map("trim", explode(";", $fellow["interests"])));
}

function save_fellows_to_database($event_id, $fellows) {
	global $pdo;

	$statement = $pdo->prepare("
		INSERT INTO
			fellows
			(event_id, name, score, available, email_address, phone_number)
		VALUES
			(?, ?, ?, ?, ?, ?)
	");

	foreach($fellows as $fellow) {
		$statement->execute(array(
			$event_id,
			$fellow["name"],
			(int) $fellow["score"],
			$fellow["available"] ? 1 : 0,
			$fellow["email_address"],
			$fellow["phone_number"]
		));

		$id = $pdo->lastInsertId();
		saveFellowInterests($id, $fellow["interests"]);
	}
}

function saveFellowInterests($id, $interests) {
	global $pdo;

	$interest_statement = $pdo->prepare("
		INSERT INTO
			fellow_interests
			(fellow_id, interest_id)
		VALUES
			(?, ?)
	");

	$ints_done = array();
	foreach($interests as $interest) {
		$interest = trim(strtolower($interest));
		if($interest == "" || isset($ints_done[$interest]))
			continue;
		$ints_done[$interest] = true;

		$interest_statement->execute(array($id, get_interest_id($interest)));
	}
}

/**
	Loads the match history as an array:

	array(
		match_set_id => array (
			array(volunteer_id => fellow_id),
			array(volunteer_id => fellow_id),
			array(volunteer_id => fellow_id)
			// etc
		)
	)

	The match_set_id is unique for each time you do a submit.
*/
function load_match_history($event_id) {
	$match_history = array();

	global $pdo;

	$statement = $pdo->prepare("
		SELECT
			match_sets.id,
			match_sets_members.volunteer_id,
			match_sets_members.fellow_id
		FROM
			match_sets_members
		INNER JOIN
			match_sets ON match_sets.id = match_sets_members.match_set_id
		WHERE
			match_sets.event_id = ?
		ORDER BY
			match_sets.when_created ASC
	");
	$statement->execute(array($event_id));
	while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		if(!isset($match_history[$row["id"]]))
			$match_history[$row["id"]] = array();
		$match_history[$row["id"]][] = array($row["volunteer_id"] => $row["fellow_id"]);
	}

	return $match_history;
}

function load_match_history_details($event_id, $match_set_id = null) {
	$match_history = array();

	global $pdo;

	$feedbackUrl = "http";
	if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
		$feedbackUrl .= "s";
	$feedbackUrl .= "://";
	$feedbackUrl .= $_SERVER["HTTP_HOST"];
	$link = $_SERVER["PHP_SELF"];
	$slashPos = strrpos($link, "/");
	if($slashPos !== FALSE)
		$link = substr($link, 0, $slashPos);
	$feedbackUrl .= "/" . $link . "/interview-feedback.php";

	$statement = $pdo->prepare("
		SELECT
			match_sets.id,
			match_sets_members.match_member_id as msmid,
			match_sets_members.link_nonce,
			match_sets_members.volunteer_id,
			match_sets_members.fellow_id
		FROM
			match_sets_members
		INNER JOIN
			match_sets ON match_sets.id = match_sets_members.match_set_id
		WHERE
			match_sets.event_id = ?
		".
		($match_set_id === null ? "" : "AND match_sets.id = '".((int)($match_set_id) . "'"))
		."
		ORDER BY
			match_sets.when_created ASC
	");
	$statement->execute(array($event_id));
	while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
		if(!isset($match_history[$row["id"]]))
			$match_history[$row["id"]] = array();
		$match_history[$row["id"]][] = array(
			"volunteer_id" => $row["volunteer_id"],
			"fellow_id" => $row["fellow_id"],
			"nonce" => $row["link_nonce"],
			"msmid" => $row["msmid"],
			"link" => $feedbackUrl . "?msmid={$row["msmid"]}&link_nonce={$row["link_nonce"]}"
		);
	}

	return $match_history;
}

/**
	matches is an array of (volunteer_id => fellow_id)

	Returns: the new match ID
*/
function save_matches($event_id, $matches) {
	global $pdo;

	$statement = $pdo->prepare("
		INSERT INTO
			match_sets
			(event_id, when_created)
		VALUES
			(?, NOW())
	");

	$statement->execute(array($event_id));
	$match_id = $pdo->lastInsertId();

	$statement = $pdo->prepare("
		INSERT INTO
			match_sets_members
			(match_set_id, volunteer_id, fellow_id, link_nonce)
		VALUES
			(?, ?, ?, FLOOR(RAND() * 2000000000))
	");

	foreach($matches as $volunteer_id => $fellow_id) {
		$statement->execute(array(
			$match_id,
			$volunteer_id,
			$fellow_id
		));
	}

	return $match_id;
}

function send_sms($to, $message) {
	global $WP_CONFIG;

	$to = preg_replace('/[^0-9]/', '', $to);
	if(strlen($to) == 10)
		$to = "1" . $to;
	$to = "+" . $to;

	if(strlen($to) != 12)
		throw new Exception("bad phone number $to");

	$ch = curl_init();
	$url = "https://api.twilio.com/2010-04-01/Accounts/".$WP_CONFIG["TWILIO_SID"]."/Messages.json";
	$auth = $WP_CONFIG["TWILIO_SID"] . ":" . $WP_CONFIG["TWILIO_TOKEN"];

	$post  = "From=".urlencode($WP_CONFIG['TWILIO_FROM'])."&";
	$post .= "To=".urlencode($to)."&";
	$post .= "Body=".urlencode($message);

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_USERPWD, $auth);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

	$answer = curl_exec($ch);
	curl_close($ch);

	return "Twilio responded: $answer \n";
}

require("../libs/email.php");

function send_stations($event_id, $match_id) {
	$matches = load_match_history_details($event_id, $match_id);
	$volunteers = load_volunteers_from_database($event_id);
	$fellows = load_fellows_from_database($event_id);

	$match_set = $matches[$match_id];

	foreach($match_set as $match_details) {
		$fellow_id = $match_details["fellow_id"];
		$volunteer_id = $match_details["volunteer_id"];
		$volunteer = $volunteers[$volunteer_id];

		$in_person = !$volunteer["virtual"];
		$station  = $volunteer["number"];

		$in_person_text = $in_person ? "" : "(virtual)";

		$fellow = $fellows[$fellow_id];
		if($fellow["phone_number"] != null && $fellow["phone_number"] != "") {
			$msg = "You have been assigned to $in_person_text $station for the next interview round!";
			$answer = send_sms($fellow["phone_number"], $msg);

			$fp = fopen("log/sms.txt", "a");
			fwrite($fp, $answer);
			fclose($fp);
		}
	}
}

function send_nags($event_id, $match_id) {
	$matches = load_match_history_details($event_id, $match_id);
	$volunteers = load_volunteers_from_database($event_id);
	$fellows = load_fellows_from_database($event_id);

	$match_set = $matches[$match_id];

	foreach($match_set as $match_details) {
		$volunteer_id = $match_details["volunteer_id"];
		$fellow_id = $match_details["fellow_id"];

		$volunteer = $volunteers[$volunteer_id];

		$msg = "Please fill in this survey about your mock interview:\n" . $match_details["link"];

		$cn = $volunteer["feedback_nag_address"];
		if($cn !== null && $cn != "") {
			if(strpos($cn, "@") !== FALSE) {
				sendEmail($cn, "Please record feedback about your interview", $msg);
			} else {
				$answer = send_sms($cn, $msg);

				$fp = fopen("log/sms.txt", "a");
				fwrite($fp, $answer);
				fclose($fp);
			}
		}
	}
}

///
function create_event_in_database($name, $university) {
	global $pdo;

	$statement = $pdo->prepare("
		INSERT INTO
			events
			(name, when_created, university)
		VALUES
			(?, NOW(), ?)
	");

	$statement->execute(array($name, $university));
	$event_id = $pdo->lastInsertId();

	return $event_id;
}

///
function get_event_name($event_id) {
	global $pdo;

	$statement = $pdo->prepare("SELECT name FROM events WHERE id = ?");
	$statement->execute(array($event_id));
	return $statement->fetch()["name"];
}


///
function get_events() {
	global $pdo;

	$statement = $pdo->prepare("SELECT id, name, when_created FROM events ORDER BY when_created");
	$statement->execute();
	return $statement->fetchAll();
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

/**
	Loads information about a specific match. Intended for use by the feedback page.

	Returns an array with keys:
		fellow_name
		fellow_university
		volunteer_name
		virtual_meeting
		msmid
		link_nonce

	Returns FALSE if no such thing.
*/
function loadMatch($msmid) {
	global $pdo;

	$statement = $pdo->prepare("
		SELECT
			fellows.name AS fellow_name,
			volunteers.name AS volunteer_name,
			volunteers.is_virtual AS virtual_meeting,
			events.university AS fellow_university,
			msm.link_nonce AS link_nonce,
			msm.match_member_id AS msmid,

			events.id AS event_id,
			match_sets.when_created AS round_scheduled_at,
			msm.volunteer_id AS volunteer_id
		FROM
			match_sets_members msm
		INNER JOIN
			match_sets ON msm.match_set_id = match_sets.id
		INNER JOIN
			events ON match_sets.event_id = events.id
		INNER JOIN
			fellows ON fellows.id = msm.fellow_id
		INNER JOIN
			volunteers ON volunteers.id = msm.volunteer_id
		WHERE
			msm.match_member_id = ?
	");
	$statement->execute(array($msmid));
	$result = $statement->fetch();

	// this is inefficient but the data sets are small enough that it is ok
	// at least until we get new mysql with window function support <3
	$round_number = 0;
	$statement = $pdo->prepare("
		SELECT
			msm.link_nonce AS link_nonce,
			msm.match_member_id AS msmid,
			msm.volunteer_id AS volunteer_id,
			match_sets.id AS match_set_id
		FROM
			match_sets_members msm
		INNER JOIN
			match_sets ON msm.match_set_id = match_sets.id
		WHERE
			event_id = ?
		ORDER BY
			when_created
	");
	$statement->execute(array($result["event_id"]));
	$prev = null;
	$next_on_deck = false;
	$previous_match_set_id = null;
	while($r2 = $statement->fetch()) {
		if($previous_match_set_id != $r2["match_set_id"]) {
			$round_number++;
			$previous_match_set_id = $r2["match_set_id"];
		}

		if($r2["volunteer_id"] != $result["volunteer_id"])
			continue;

		$this_link = "interview-feedback.php?msmid={$r2["msmid"]}&link_nonce={$r2["link_nonce"]}";

		if($next_on_deck) {
			$result["next_link"] = $this_link;
			break;
		}

		if($r2["msmid"] == $msmid) {
			$result["round_number"] = $round_number;
			$result["previous_link"] = $prev;
			$next_on_deck = true;
		}

		$prev = $this_link;
	}

	return $result;
}

function loadExistingRubric($msmid) {
	global $pdo;
	$statement = $pdo->prepare("
		SELECT
			*
		FROM
			feedback_for_fellow
		WHERE
			msm_id = ?
	");
	$statement->execute(array($msmid));
	$result = $statement->fetch();

	return $result;
}
