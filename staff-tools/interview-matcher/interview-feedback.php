<?php
	include_once("db.php");

	$msmid = isset($_REQUEST["msmid"]) ? $_REQUEST["msmid"] : 0;
	$nonce = isset($_REQUEST["link_nonce"]) ? $_REQUEST["link_nonce"] : 0;

	$match = loadMatch($msmid);

	if($match === FALSE) {
		echo "No match selected. Make sure you are using the link we sent you.";
		exit;
	}

	if($match["link_nonce"] != $nonce) {
		echo "Wrong link. Make your got the whole thing we sent you.";
		exit;
	}

	$previous_link = $match["previous_link"];
	$next_link = $match["next_link"];

	if(isset($_POST["msmid"])) {

		function coalesce($f) {
			if($f === null)
				return "incomplete";
			return $f;
		}

		global $pdo;

		$statement = $pdo->prepare("
			SELECT
				id, when_submitted
			FROM
				feedback_for_fellow
			WHERE
				msm_id = ?
		");
		$statement->execute(array($_POST["msmid"]));
		if(($result = $statement->fetch()) !== FALSE) {
			// exists, update

			$statement = $pdo->prepare("
				UPDATE
					feedback_for_fellow
				SET
					fellow_name = ?,
					fellow_university = ?,
					interviewer_name = ?,

					q1 = ?,
					q2 = ?,
					q3 = ?,
					q4 = ?,
					q5 = ?,
					q6 = ?,
					q7 = ?,
					q8 = ?,
					q9 = ?,
					q10 = ?,

					comments = ?,

					when_last_changed = NOW()
					".((isset($_POST["submitted"]) && $result["when_submitted"] == null) ? ", when_submitted = NOW()" : ""). "
				WHERE
					id = ?
			");

			$statement->execute(array(
				$_POST["fellow_name"], $_POST["fellow_university"], $_POST["interviewer_name"],

				coalesce($_POST["q1"]),
				coalesce($_POST["q2"]),
				coalesce($_POST["q3"]),
				coalesce($_POST["q4"]),
				coalesce($_POST["q5"]),
				coalesce($_POST["q6"]),
				coalesce($_POST["q7"]),
				coalesce($_POST["q8"]),
				coalesce($_POST["q9"]),
				coalesce($_POST["q10"]),

				coalesce($_POST["comments"]),

				$result["id"]
			));
		} else {
			// not exist, create

			$statement = $pdo->prepare("
				INSERT INTO
					feedback_for_fellow
					(
						msm_id,

						fellow_name,
						fellow_university,
						interviewer_name,

						q1,
						q2,
						q3,
						q4,
						q5,
						q6,
						q7,
						q8,
						q9,
						q10,

						comments,

						when_started,
						when_last_changed
						".(isset($_POST["submitted"]) ? ", when_submitted" : "")."
					)
				VALUES
					(
					?,
					?,?,?,
					?,?,? ,?,?,?, ?,?,?, ?,

					?,

					NOW(),
					NOW()
					".(isset($_POST["submitted"]) ? ", NOW()" : "")."
					)
			");

			$statement->execute(array(
				$_POST["msmid"],
				$_POST["fellow_name"], $_POST["fellow_university"], $_POST["interviewer_name"],

				// I do this so it will allow a partial save...
				coalesce($_POST["q1"]),
				coalesce($_POST["q2"]),
				coalesce($_POST["q3"]),
				coalesce($_POST["q4"]),
				coalesce($_POST["q5"]),
				coalesce($_POST["q6"]),
				coalesce($_POST["q7"]),
				coalesce($_POST["q8"]),
				coalesce($_POST["q9"]),
				coalesce($_POST["q10"]),

				coalesce($_POST["comments"])
			));
		}
	?>
		Thank you. Your feedback has been recorded and is appreciated!
	<?php

		exit;
	}

	$existing_data = loadExistingRubric($_GET["msmid"]);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Braven Mock Interview Fellow Rubric</title>

	<style>
		#page {
			max-width: 600px;
			margin: 0px auto;
		}
		p {
			max-width: 600px;
			max-width: 60cx;
		}
		form {
			margin: 2em 1em;
			max-width: 600px;
		}
		div.field {
			margin: 1em 0px;
			padding-left: 1em;
		}
		span.question {
			display: block;
			font-weight: bold;
			margin-left: -1em;
		}
		label {
			display: block;
		}
		input[type=text] {
			width: 100%;
		}
		textarea {
			width: 100%;
			min-height: 5em;
		}

	</style>
</head>
<body>

<div id="page">

<h1>Braven Mock Interview Fellow Rubric</h1>

<p>After each interview, please score the Fellows using the following rubric and add any additional comments as written feedback. Fellows will receive their scores and comments to continue improving their interview skills. </p>

<p>The four levels of competency equate with the following point structure:
<br />Exceeds or meets expectation: 10
<br />Somewhat meets expectation: 8
<br />Still developing the skill: 6
<br />Does not meet expectation: 0
</p>

All fields are required.

<form method="POST">

<input type="hidden" name="msmid" value="<?php echo htmlentities($match["msmid"]); ?>" />
<input type="hidden" name="link_nonce" value="<?php echo htmlentities($match["link_nonce"]); ?>" />

<h2>Round <?php echo $match["round_number"]; ?></h2>
<div style="margin-top: -12px; margin-bottom: 18px;">
	<?php if($previous_link) { ?>
		<a href="<?php echo $previous_link; ?>">Previous</a>
	<?php } ?>
	<?php if($previous_link && $next_link) { ?>
		|
	<?php } ?>
	<?php if($next_link) { ?>
		<a href="<?php echo $next_link; ?>">Next</a>
	<?php } ?>
	&nbsp;
</div>

<div class="field">
	<label><span class="question">Interviewer Name (first and last):</span>
		<input required="required" type="text" name="interviewer_name" value="<?php echo htmlentities($existing_data ? $existing_data["interviewer_name"] : $match["volunteer_name"]); ?>" /></label>
</div>

<div class="field">
	<label><span class="question">Fellow Name (first and last):</span>
		<input required="required" type="text" name="fellow_name" value="<?php echo htmlentities($existing_data ? $existing_data["fellow_name"] : $match["fellow_name"]); ?>" /></label>
</div>

<div class="field">
	<label><span class="question">Fellow's university:</span>
		<input required="required" type="text" name="fellow_university" value="<?php echo htmlentities($existing_data ? $existing_data["fellow_university"] : $match["fellow_university"]); ?>" /></label>
</div>

<div class="field">
	<span class="question">1. The Fellow speaks professionally.</span>
	<label><input <?php if($existing_data && $existing_data["q1"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q1" value="10" /> Exceeds or meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q1"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q1" value="8" /> Somewhat meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q1"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q1" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q1"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q1" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">2. The Fellow makes eye contact (if in-person).</span>
	<label><input <?php if($existing_data && $existing_data["q2"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q2" value="10" /> Exceeds or meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q2"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q2" value="8" /> Somewhat meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q2"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q2" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q2"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q2" value="0" /> Does not meet expectation</label>
	<label><input <?php if($existing_data && $existing_data["q2"] === '') echo 'checked="checked"'; ?> required="required" type="radio" name="q2" value="" /> N/A</label>
</div>
<div class="field">
	<span class="question">3. The Fellow has a solid handshake (if in-person).</span>
	<label><input <?php if($existing_data && $existing_data["q3"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q3" value="10" /> Exceeds or meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q3"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q3" value="8" /> Somewhat meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q3"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q3" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q3"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q3" value="0" /> Does not meet expectation</label>
	<label><input <?php if($existing_data && $existing_data["q3"] === '') echo 'checked="checked"'; ?> required="required" type="radio" name="q3" value="" /> N/A</label>
</div>
<div class="field">
	<span class="question">4. The Fellow uses specific and relevant examples of experiences that demonstrate required skills.</span>
	<label><input <?php if($existing_data && $existing_data["q4"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q4" value="10" /> Exceeds or meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q4"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q4" value="8" /> Somewhat meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q4"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q4" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q4"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q4" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">5. The Fellow explains how their skills are transferable to future roles.</span>
	<label><input <?php if($existing_data && $existing_data["q5"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q5" value="10" /> Exceeds or meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q5"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q5" value="8" /> Somewhat meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q5"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q5" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q5"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q5" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">6. The Fellow is clear and concise.</span>
	<label><input <?php if($existing_data && $existing_data["q6"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q6" value="10" /> Exceeds or meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q6"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q6" value="8" /> Somewhat meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q6"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q6" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q6"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q6" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">7. The Fellow reinforces connections through compelling storytelling.</span>
	<label><input <?php if($existing_data && $existing_data["q7"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q7" value="10" /> Exceeds or meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q7"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q7" value="8" /> Somewhat meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q7"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q7" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q7"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q7" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">8. The Fellow confidently persists in answering a question even if it is a difficult question.</span>
	<label><input <?php if($existing_data && $existing_data["q8"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q8" value="10" /> Exceeds or meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q8"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q8" value="8" /> Somewhat meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q8"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q8" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q8"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q8" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">9. The Fellow creates a personal connection with the interviewer (e.g. relates on personal background or a hobby).</span>
	<label><input <?php if($existing_data && $existing_data["q9"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q9" value="10" /> Exceeds or meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q9"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q9" value="8" /> Somewhat meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q9"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q9" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q9"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q9" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">10. The Fellow offers to continue the connection past the interview.</span>
	<label><input <?php if($existing_data && $existing_data["q10"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q10" value="10" /> Exceeds or meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q10"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q10" value="8" /> Somewhat meets expectation</label>
	<label><input <?php if($existing_data && $existing_data["q10"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q10" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q10"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q10" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<label><span class="question">Comments for Fellow:</span>
		<textarea name="comments"><?php if($existing_data) echo htmlentities($existing_data["comments"]); ?></textarea></label>
</div>

	<input name="submitted" type="submit" value="Submit Feedback" />

</form>

</div>

<script>
	// enable auto-save as they go...
	function backgroundSubmit() {
		var formElement = document.querySelector("form");
		var request = new XMLHttpRequest();
		request.open("POST", "interview-feedback.php");
		request.send(new FormData(formElement));
	}

	var formElement = document.querySelector("form");
	formElement.addEventListener("change", backgroundSubmit);
</script>


</body>
</html>
