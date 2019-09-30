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

					q_speaks_professionally = ?,
					q_eye_contact = ?,
					q_solid_handshake = ?,
					q_body_language = ?,
					q_specific_examples = ?,
					q_transferable_skills = ?,
					q_clear_concise = ?,
					q_compelling_storytelling = ?,
					q_confidently_persists = ?,
					q_personal_connection = ?,
					q_continue_connection = ?,
					q_prepared_questions = ?,

					comments = ?,

					when_last_changed = NOW()
					".((isset($_POST["submitted"]) && $result["when_submitted"] == null) ? ", when_submitted = NOW()" : ""). "
				WHERE
					id = ?
			");

			$statement->execute(array(
				$_POST["fellow_name"], $_POST["fellow_university"], $_POST["interviewer_name"],

				coalesce($_POST["q_speaks_professionally"]),
				coalesce($_POST["q_eye_contact"]),
				coalesce($_POST["q_solid_handshake"]),
				coalesce($_POST["q_body_language"]),
				coalesce($_POST["q_specific_examples"]),
				coalesce($_POST["q_transferable_skills"]),
				coalesce($_POST["q_clear_concise"]),
				coalesce($_POST["q_compelling_storytelling"]),
				coalesce($_POST["q_confidently_persists"]),
				coalesce($_POST["q_personal_connection"]),
				coalesce($_POST["q_continue_connection"]),
				coalesce($_POST["q_prepared_questions"]),

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

						q_speaks_professionally,
						q_eye_contact,
						q_solid_handshake,
						q_body_language,
						q_specific_examples,
						q_transferable_skills,
						q_clear_concise,
						q_compelling_storytelling,
						q_confidently_persists,
						q_personal_connection,
						q_continue_connection,
						q_prepared_questions,

						comments,

						when_started,
						when_last_changed
						".(isset($_POST["submitted"]) ? ", when_submitted" : "")."
					)
				VALUES
					(
					?,
					?,?,?,
					?,?,? ,?,?,?, ?,?,?, ?,?,?,

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
				coalesce($_POST["q_speaks_professionally"]),
				coalesce($_POST["q_eye_contact"]),
				coalesce($_POST["q_solid_handshake"]),
				coalesce($_POST["q_body_language"]),
				coalesce($_POST["q_specific_examples"]),
				coalesce($_POST["q_transferable_skills"]),
				coalesce($_POST["q_clear_concise"]),
				coalesce($_POST["q_compelling_storytelling"]),
				coalesce($_POST["q_confidently_persists"]),
				coalesce($_POST["q_personal_connection"]),
				coalesce($_POST["q_continue_connection"]),
				coalesce($_POST["q_prepared_questions"]),

				coalesce($_POST["comments"])
			));
		}
	?>
		Thank you. Your feedback has been recorded and is appreciated!

		<?php if($next_link) ?>
			<a href="<?php echo $next_link;?>">Record feedback for next round</a>
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
<br />Exceeds or meets expectations: 10
<br />Somewhat meets expectations: 8
<br />Still developing the skill: 6
<br />Does not meet expectations: 0
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

<h2>1. PRESENCE: FELLOW GIVES A STRONG FIRST IMPRESSION</h2>

<div class="field">
	<span class="question">1.1. The Fellow speaks professionally.</span>
	<label><input <?php if($existing_data && $existing_data["q_speaks_professionally"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q_speaks_professionally" value="10" /> Exceeds or meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_speaks_professionally"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q_speaks_professionally" value="8" /> Somewhat meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_speaks_professionally"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q_speaks_professionally" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q_speaks_professionally"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q_speaks_professionally" value="0" /> Does not meet expectations</label>
</div>
<div class="field">
	<span class="question">1.2. The Fellow makes eye contact (if in-person).</span>
	<label><input <?php if($existing_data && $existing_data["q_eye_contact"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q_eye_contact" value="10" /> Exceeds or meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_eye_contact"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q_eye_contact" value="8" /> Somewhat meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_eye_contact"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q_eye_contact" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q_eye_contact"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q_eye_contact" value="0" /> Does not meet expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_eye_contact"] === '') echo 'checked="checked"'; ?> required="required" type="radio" name="q_eye_contact" value="" /> N/A</label>
</div>
<div class="field">
	<span class="question">1.3. The Fellow has a solid handshake (if in-person).</span>
	<label><input <?php if($existing_data && $existing_data["q_solid_handshake"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q_solid_handshake" value="10" /> Exceeds or meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_solid_handshake"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q_solid_handshake" value="8" /> Somewhat meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_solid_handshake"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q_solid_handshake" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q_solid_handshake"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q_solid_handshake" value="0" /> Does not meet expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_solid_handshake"] === '') echo 'checked="checked"'; ?> required="required" type="radio" name="q_solid_handshake" value="" /> N/A</label>
</div>
<div class="field">
	<span class="question">1.4. The Fellow uses professional body language (e.g. sitting up straight, not fidgeting with hands).</span>
	<label><input <?php if($existing_data && $existing_data["q_body_language"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q_body_language" value="10" /> Exceeds or meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_body_language"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q_body_language" value="8" /> Somewhat meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_body_language"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q_body_language" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q_body_language"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q_body_language" value="0" /> Does not meet expectations</label>
</div>

<h2>2. FIT: FELLOW DEMONSTRATES NECESSARY SKILLS & EXPERIENCES</h2>

<div class="field">
	<span class="question">2.1. The Fellow uses specific and relevant examples of experiences that demonstrate required skills.</span>
	<label><input <?php if($existing_data && $existing_data["q_specific_examples"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q_specific_examples" value="10" /> Exceeds or meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_specific_examples"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q_specific_examples" value="8" /> Somewhat meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_specific_examples"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q_specific_examples" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q_specific_examples"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q_specific_examples" value="0" /> Does not meet expectations</label>
</div>
<div class="field">
	<span class="question">2.2. The Fellow explains how their skills are transferable to future roles.</span>
	<label><input <?php if($existing_data && $existing_data["q_transferable_skills"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q_transferable_skills" value="10" /> Exceeds or meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_transferable_skills"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q_transferable_skills" value="8" /> Somewhat meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_transferable_skills"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q_transferable_skills" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q_transferable_skills"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q_transferable_skills" value="0" /> Does not meet expectations</label>
</div>

<h2>3. PRESENTATION SKILLS: FELLOW ANSWERS QUESTIONS IN A COMPELLING WAY</h2>

<div class="field">
	<span class="question">3.1. The Fellow is clear and concise.</span>
	<label><input <?php if($existing_data && $existing_data["q_clear_concise"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q_clear_concise" value="10" /> Exceeds or meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_clear_concise"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q_clear_concise" value="8" /> Somewhat meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_clear_concise"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q_clear_concise" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q_clear_concise"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q_clear_concise" value="0" /> Does not meet expectations</label>
</div>
<div class="field">
	<span class="question">3.2. The Fellow reinforces connections through compelling storytelling.</span>
	<label><input <?php if($existing_data && $existing_data["q_compelling_storytelling"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q_compelling_storytelling" value="10" /> Exceeds or meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_compelling_storytelling"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q_compelling_storytelling" value="8" /> Somewhat meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_compelling_storytelling"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q_compelling_storytelling" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q_compelling_storytelling"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q_compelling_storytelling" value="0" /> Does not meet expectations</label>
</div>
<div class="field">
	<span class="question">3.3. The Fellow confidently persists in answering a question even if it is a difficult question.</span>
	<label><input <?php if($existing_data && $existing_data["q_confidently_persists"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q_confidently_persists" value="10" /> Exceeds or meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_confidently_persists"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q_confidently_persists" value="8" /> Somewhat meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_confidently_persists"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q_confidently_persists" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q_confidently_persists"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q_confidently_persists" value="0" /> Does not meet expectations</label>
</div>

<h2>4. INTERPERSONAL SKILLS: FELLOW ESTABLISHES AN INTERPERSONAL CONNECTION WITH THE INTERVIEWER</h2>

<div class="field">
	<span class="question">4.1. The Fellow creates a personal connection with the interviewer (e.g. relates on personal background or a hobby).</span>
	<label><input <?php if($existing_data && $existing_data["q_personal_connection"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q_personal_connection" value="10" /> Exceeds or meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_personal_connection"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q_personal_connection" value="8" /> Somewhat meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_personal_connection"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q_personal_connection" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q_personal_connection"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q_personal_connection" value="0" /> Does not meet expectations</label>
</div>
<div class="field">
	<span class="question">4.2. The Fellow offers to continue the connection past the interview.</span>
	<label><input <?php if($existing_data && $existing_data["q_continue_connection"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q_continue_connection" value="10" /> Exceeds or meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_continue_connection"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q_continue_connection" value="8" /> Somewhat meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_continue_connection"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q_continue_connection" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q_continue_connection"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q_continue_connection" value="0" /> Does not meet expectations</label>
</div>
<div class="field">
	<span class="question">4.3. The Fellow is prepared with 2-4 strong questions for the interviewer demonstrating their interest in and research on the company and role.</span>
	<label><input <?php if($existing_data && $existing_data["q_prepared_questions"] === "10") echo 'checked="checked"'; ?> required="required" type="radio" name="q_prepared_questions" value="10" /> Exceeds or meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_prepared_questions"] === "8") echo 'checked="checked"'; ?> required="required" type="radio" name="q_prepared_questions" value="8" /> Somewhat meets expectations</label>
	<label><input <?php if($existing_data && $existing_data["q_prepared_questions"] === "6") echo 'checked="checked"'; ?> required="required" type="radio" name="q_prepared_questions" value="6" /> Still developing the skill</label>
	<label><input <?php if($existing_data && $existing_data["q_prepared_questions"] === "0") echo 'checked="checked"'; ?> required="required" type="radio" name="q_prepared_questions" value="0" /> Does not meet expectations</label>
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
