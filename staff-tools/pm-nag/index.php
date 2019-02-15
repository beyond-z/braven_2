<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . "../interview-matcher");

	require_once("sso.php");

	requireLogin();

	if(isset($_POST["send_nag"])) {
		require_once("db.php");

		$message = $_POST["message"];

		echo "Sending messages, please wait...<br />";
		flush();

		foreach($_POST["nag_number"] as $to) {
			send_sms($to, $message);
			echo ".";
			flush();
		}

		echo "<br />Messages sent!";

		exit;
	}

	if(isset($_FILES["pmmapping"])) {
		$fellowsToContact = array();
		$pmsToContact = array();

		$fp = fopen($_FILES["pmmapping"]["tmp_name"], "r");
		$first = true;
		while($data = fgetcsv($fp)) {
			// to skip the header
			if($first) {
				$first = false;
				continue;
			}

			$fellowsToContact[$data[0]] = $data[2];
			// not a typo there - the survey responses are always keyed by fellow name
			$pmsToContact[$data[0]] = $data[3];
		}
		fclose($fp);

		$zip = new ZipArchive();
		if($zip->open($_FILES["sheet"]["tmp_name"])) {
			$fp = $zip->getStream($zip->getNameIndex(0));
			if(!$fp) exit("zip failed...");

			while($data = fgetcsv($fp)) {
				switch($data[1]) {
					case "Fellow":
						$fellowsToContact[$data[2]] = null;
					break;
					case "Professional Mentor":
						$pmsToContact[$data[2]] = null;
					break;
					default:
						// intentionally blank
				}

			}

			fclose($fp);
		}
?><!DOCTYPE html>
<html>
<head>
	<title>PM Nag Tool</title>
	<style>
		body {
			font-family: sans-serif;
			max-width: 700px;
			font-size: 16px;
		}

		label {
			display: block;
			padding: 4px;
		}
		textarea {
			width: 30em;
			height: 5em;
		}
	</style>
</head>
<body>
	Suggested messages:<br /><br /><br />

	<form method="POST">
		<label>Message to send:<br />
		<textarea name="message" maxlength="140">Please complete your the survey for your Braven PM experience.</textarea></label>
		<br /><br />
	<?php
		foreach($pmsToContact as $fellowName => $pmNumber) {
			if($pmNumber === null)
				continue;

			echo "<label><input checked=\"checked\" type=\"checkbox\" name=\"nag_number[]\" value=\"$pmNumber\" /> PM for $fellowName via $pmNumber</label>";
			echo "<br />";
		}
		foreach($fellowsToContact as $fellowName => $fellowNumber) {
			if($fellowNumber === null)
				continue;

			echo "<label><input checked=\"checked\" type=\"checkbox\" name=\"nag_number[]\" value=\"$fellowNumber\" />$fellowName via $fellowNumber</label>";
			echo "<br />";
		}
	?>
		<br /><br />
		<input type="submit" name="send_nag" value="Send Nags" />
	</form>
</body>
</html><?php
	exit;
	}
?><!DOCTYPE html>
<html>
<head>
	<title>PM Nag Tool</title>
	<style>
		body {
			font-family: sans-serif;
			max-width: 700px;
			font-size: 16px;
		}
		.sample {
			border: solid 1px #ccc;
			border-collapse: collapse;
		}
		.sample td, .sample th {
			border: solid 1px #ccc;
			padding: 2px 4px;
		}
		label {
			font-size: 120%;
			font-weight: bold;
		}
	</style>
</head>
<body>

<form method="POST" enctype="multipart/form-data">

<p>First, you need to upload a participants sheet. This should show the Fellow/PM pairs for your region who are supposed to fill out the survey, along with their phone numbers.</p>

<p>Note that the names should be identical to the options on the Google Form, since it matches up by name, exactly.</p>

<table class="sample">
<caption>Sample Spreadsheet</caption>
<tr><th>Fellow Name</th><th>PM Name</th><th>Fellow Number</th><th>PM Number</th></tr>
<tr><td>Alice Example</td><td>Emily Post</td><td>800-555-3456</td><td>800-555-1234</td></tr>
<tr><td>Bobby Tables</td><td>Sample Mentor</td><td>800-555-3456</td><td>800-555-1234</td></tr>
</table>

<p>Upload that here each time in csv format:</p>
	<label>Participants sheet:
		<input required="required" type="file" name="pmmapping" /></label>


<p>Next, you need to get the responses file out of Google.</p>

<ol>
	<li><p>Make sure you are logged into your Braven account on Google.</p></li>

	<li><p>Go to the form for your region:</p>
		<ul>
			<li><a target="_BLANK" href="https://docs.google.com/forms/d/1TUD_07Dz0okcnAst2SJvcJcNCdDmTZvhlBkKB1DnPEk/edit#responses">SJSU</a></li>
			<li><a target="_BLANK" href="https://docs.google.com/forms/d/1M2ZWlg4AhCw2PIJDxNOgCnjJqlyfseNZB37T1lMJJug/edit#responses">RU-N</a></li>
			<li><a target="_BLANK" href="https://docs.google.com/forms/d/1AlcbF-GdFB80ea2ciZZbLOIUzhedRTOzNrMHC4av3iM/edit#responses">NLU</a></li>
		</ul>
	</li>

	<li><p>On that page, right above "Accepting Responses", you will see a three dot thing. Click that.</p>

	<img src="google-pic.png" alt="The three dot thing you need to click is near the top of the white area." />
	</li>

	<li><p>Then press "Download Responses (.csv)" in the menu it pops up.</p></li>

	<li><p>Upload the file it gave you (which will be a .zip file, that's normal) here:</p></li>
</ol>

	<label>Results file:
		<input required="required" type="file" name="sheet" /></label>
	<br />
	<br />
	<input type="submit" value="Upload" />

	<p>After the upload, you'll have a chance to confirm before sending the text messages.</p>
</form>

</body>
</html>
