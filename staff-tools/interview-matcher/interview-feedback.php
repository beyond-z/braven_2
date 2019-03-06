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

	if(isset($_POST["msmid"])) {
		global $pdo;
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

					comments
				)
			VALUES
				(
				?,
				?,?,?,
				?,?,? ,?,?,?, ?,?,?, ?,
				?
				)
		");

		$statement->execute(array(
			$_POST["msmid"],
			$_POST["fellow_name"], $_POST["fellow_university"], $_POST["interviewer_name"],

			$_POST["q1"],
			$_POST["q2"],
			$_POST["q3"],
			$_POST["q4"],
			$_POST["q5"],
			$_POST["q6"],
			$_POST["q7"],
			$_POST["q8"],
			$_POST["q9"],
			$_POST["q10"],

			$_POST["comments"]
		));
	?>
		Thank you. Your feedback has been recorded and is appreciated!
	<?php

		exit;
	}
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

<p>Rubric for interview round <?php echo $match["round_number"]; ?></p>

<div class="field">
	<label><span class="question">Interviewer Name (first and last):</span>
		<input required="required" type="text" name="interviewer_name" value="<?php echo htmlentities($match["volunteer_name"]); ?>" /></label>
</div>

<div class="field">
	<label><span class="question">Fellow Name (first and last):</span>
		<input required="required" type="text" name="fellow_name" value="<?php echo htmlentities($match["fellow_name"]); ?>" /></label>
</div>

<div class="field">
	<label><span class="question">Fellow's university:</span>
		<input required="required" type="text" name="fellow_university" value="<?php echo htmlentities($match["fellow_university"]); ?>" /></label>
</div>

<div class="field">
	<span class="question">1. The Fellow speaks professionally.</span>
	<label><input required="required" type="radio" name="q1" value="10" /> Exceeds or meets expectation</label>
	<label><input required="required" type="radio" name="q1" value="8" /> Somewhat meets expectation</label>
	<label><input required="required" type="radio" name="q1" value="6" /> Still developing the skill</label>
	<label><input required="required" type="radio" name="q1" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">2. The Fellow makes eye contact (if in-person).</span>
	<label><input required="required" type="radio" name="q2" value="10" /> Exceeds or meets expectation</label>
	<label><input required="required" type="radio" name="q2" value="8" /> Somewhat meets expectation</label>
	<label><input required="required" type="radio" name="q2" value="6" /> Still developing the skill</label>
	<label><input required="required" type="radio" name="q2" value="0" /> Does not meet expectation</label>
	<label><input required="required" type="radio" name="q2" value="" /> N/A</label>
</div>
<div class="field">
	<span class="question">3. The Fellow has a solid handshake (if in-person).</span>
	<label><input required="required" type="radio" name="q3" value="10" /> Exceeds or meets expectation</label>
	<label><input required="required" type="radio" name="q3" value="8" /> Somewhat meets expectation</label>
	<label><input required="required" type="radio" name="q3" value="6" /> Still developing the skill</label>
	<label><input required="required" type="radio" name="q3" value="0" /> Does not meet expectation</label>
	<label><input required="required" type="radio" name="q3" value="" /> N/A</label>
</div>
<div class="field">
	<span class="question">4. The Fellow uses specific and relevant examples of experiences that demonstrate required skills.</span>
	<label><input required="required" type="radio" name="q4" value="10" /> Exceeds or meets expectation</label>
	<label><input required="required" type="radio" name="q4" value="8" /> Somewhat meets expectation</label>
	<label><input required="required" type="radio" name="q4" value="6" /> Still developing the skill</label>
	<label><input required="required" type="radio" name="q4" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">5. The Fellow explains how their skills are transferable to future roles.</span>
	<label><input required="required" type="radio" name="q5" value="10" /> Exceeds or meets expectation</label>
	<label><input required="required" type="radio" name="q5" value="8" /> Somewhat meets expectation</label>
	<label><input required="required" type="radio" name="q5" value="6" /> Still developing the skill</label>
	<label><input required="required" type="radio" name="q5" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">6. The Fellow is clear and concise.</span>
	<label><input required="required" type="radio" name="q6" value="10" /> Exceeds or meets expectation</label>
	<label><input required="required" type="radio" name="q6" value="8" /> Somewhat meets expectation</label>
	<label><input required="required" type="radio" name="q6" value="6" /> Still developing the skill</label>
	<label><input required="required" type="radio" name="q6" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">7. The Fellow reinforces connections through compelling storytelling.</span>
	<label><input required="required" type="radio" name="q7" value="10" /> Exceeds or meets expectation</label>
	<label><input required="required" type="radio" name="q7" value="8" /> Somewhat meets expectation</label>
	<label><input required="required" type="radio" name="q7" value="6" /> Still developing the skill</label>
	<label><input required="required" type="radio" name="q7" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">8. The Fellow confidently persists in answering a question even if it is a difficult question.</span>
	<label><input required="required" type="radio" name="q8" value="10" /> Exceeds or meets expectation</label>
	<label><input required="required" type="radio" name="q8" value="8" /> Somewhat meets expectation</label>
	<label><input required="required" type="radio" name="q8" value="6" /> Still developing the skill</label>
	<label><input required="required" type="radio" name="q8" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">9. The Fellow creates a personal connection with the interviewer (e.g. relates on personal background or a hobby).</span>
	<label><input required="required" type="radio" name="q9" value="10" /> Exceeds or meets expectation</label>
	<label><input required="required" type="radio" name="q9" value="8" /> Somewhat meets expectation</label>
	<label><input required="required" type="radio" name="q9" value="6" /> Still developing the skill</label>
	<label><input required="required" type="radio" name="q9" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<span class="question">10. The Fellow offers to continue the connection past the interview.</span>
	<label><input required="required" type="radio" name="q10" value="10" /> Exceeds or meets expectation</label>
	<label><input required="required" type="radio" name="q10" value="8" /> Somewhat meets expectation</label>
	<label><input required="required" type="radio" name="q10" value="6" /> Still developing the skill</label>
	<label><input required="required" type="radio" name="q10" value="0" /> Does not meet expectation</label>
</div>
<div class="field">
	<label><span class="question">Comments for Fellow:</span>
		<textarea name="comments"></textarea></label>
</div>

	<input type="submit" value="Submit Feedback" />

</form>

</div>

</body>
</html>
