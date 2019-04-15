<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . "../interview-matcher");

	require_once("sso.php");

	requireAdmin();

	require_once("db.php");

	// so csvs from Macs work as well as from Linux or Windows boxes
	ini_set('auto_detect_line_endings', true);

	if(isset($_POST["send_nag"])) {
		$pm_nag_group_id = $_POST["pm_nag_group_id"];

		$statement = $pdo->prepare("INSERT INTO pm_nag_group_member_nag_batch
			(created_by, created_at) VALUES (?, now())");
		$statement->execute(array($_SESSION["user"]));

		$id = $pdo->lastInsertId();

		$statement = $pdo->prepare("INSERT INTO
			pm_nag_group_member_nag
				(pm_nag_group_member_nag_batch_id, pm_nag_group_member_id, reply)
			VALUES
				(?, ?, ?)
		");

		$message = $_POST["message"];

		echo "Sending messages, please wait...<br />";
		flush();

		foreach($_POST["nag_number"] as $to_combined) {
			// it goes id_number
			$idx = strpos($to_combined, "_");

			$mid = substr($to_combined, 0, $idx);
			$to = substr($to_combined, $idx + 1);

			$reply = send_sms($to, $message);
			$statement->execute(array($id, $mid, $reply));
			echo ".";
			flush();
		}

		echo "<br />Messages sent!";

		exit;
	}

	if(isset($_POST["pmmapping_name"])) {
		$fellowsToContact = array();
		$pmsToContact = array();

		$id = (int) $_POST["pm_nag_group_id"];
		if($id == 0) {
			// load from a file
			$fp = fopen($_FILES["pmmapping"]["tmp_name"], "r");
			$first = true;

			$pdo->beginTransaction();

			$statement = $pdo->prepare("INSERT INTO pm_nag_group (name, created_by, default_message) VALUES (?, ?, ?)");
			$statement->execute(array($_POST["pmmapping_name"], $_SESSION["user"], $_POST["default_message"]));
			$id = $pdo->lastInsertId();

			$statement = $pdo->prepare("
				INSERT INTO
					pm_nag_group_member
					(
						pm_nag_group_id,
						fellow_name,
						pm_name,
						fellow_number,
						pm_number
					)
					VALUES
					(?, ?, ?, ?, ?)
			");

			while($data = fgetcsv($fp)) {
				// to skip the header
				if($first) {
					$first = false;
					continue;
				}

				$statement->execute(array($id, $data[0], $data[1], $data[2], $data[3]));
			}
			fclose($fp);

			$pdo->commit();
		}

		$statement = $pdo->prepare("SELECT default_message FROM pm_nag_group WHERE id = ?");
		$statement->execute(array($id));
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		$default_message = $row["default_message"];

		$statement = $pdo->prepare("
			SELECT
				fellow_name,
				pm_name,
				fellow_number,
				pm_number,
				id
			FROM
				pm_nag_group_member
			WHERE
				pm_nag_group_id = ?
		");
		$statement->execute(array($id));
		while($row = $statement->fetch(PDO::FETCH_NUM)) {
			$fellowsToContact[$row[0]] = array($row[2], $row[4]);
			// not a typo there - the survey responses are always keyed by fellow name
			$pmsToContact[$row[0]] = array($row[3], $row[4]);
		}

		$zip = new ZipArchive();
		if($zip->open($_FILES["sheet"]["tmp_name"])) {
			$fp = $zip->getStream($zip->getNameIndex(0));
			if(!$fp) exit("zip failed...");

			while($data = fgetcsv($fp)) {

				// the results include last week too, so
				// we only want to consider those submitted
				// this week
				$timestamp = strtotime($data[0]);
				$max = strtotime("last Sunday");
				if($timestamp < $max) {
					continue;
				}

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
		<input type="hidden" name="pm_nag_group_id" value="<?php echo $id; ?>" />
		<label>Message to send:<br />
		<textarea name="message" maxlength="140"><?php echo htmlentities($default_message); ?></textarea></label>
		<br /><br />
	<?php
		foreach($pmsToContact as $fellowName => $info) {
			if($info === null)
				continue;

			echo "<label><input checked=\"checked\" type=\"checkbox\" name=\"nag_number[]\" value=\"{$info[1]}_".htmlentities($info[0])."\" /> PM for $fellowName via {$info[0]}</label>";
			echo "<br />";
		}
		foreach($fellowsToContact as $fellowName => $info) {
			if($info === null)
				continue;

			echo "<label><input checked=\"checked\" type=\"checkbox\" name=\"nag_number[]\" value=\"{$info[1]}_".htmlentities($info[0])."\" />$fellowName via {$info[0]}</label>";
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
		textarea {
			width: 30em;
			height: 5em;
		}

		.using-preset,
		.using-preset * {
			background-color: #666;
		}
	</style>
</head>
<body>

<form method="POST" enctype="multipart/form-data">

<h1>Step one: identify participants</h1>

<p>First, you need to upload a participants sheet. This should show the Fellow/PM pairs for your region who are supposed to fill out the survey, along with their phone numbers.</p>

<fieldset>
	<legend>Reuse saved sheet</legend>

	<label>Reuse saved file:

	<select name="pm_nag_group_id" onchange="
		var fs = document.getElementById('upload-new-sheet');
		fs.classList[this.value == '0' ? 'remove' : 'add']('using-preset');
	">
		<option value="0">-- Upload New File --</option>
	<?php
		$statement = $pdo->query("SELECT id, name, created_by FROM pm_nag_group");
		$statement->execute();
		$found_any = false;
		$preset = false;
		while($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			$found_any = true;
			$preset = $row["created_by"] == $_SESSION["user"];
			echo "<option value=\"{$row["id"]}\"".(($preset) ? " selected" : "").">".htmlentities($row["name"])."</option>";
		}
	?>
	</select>
	</label>
</fieldset>

	<br />
	<br />
	- OR - 
	<br />
	<br />

<fieldset id="upload-new-sheet"
<?php
	if($preset)
		echo "class='using-preset'";
?>
>
	<legend>Upload new sheet</legend>

	<p>Note that the names should be identical to the options on the Google Form, since it matches up by name, exactly.</p>

	<table class="sample">
	<caption>Sample Spreadsheet</caption>
	<tr><th>Fellow Name</th><th>PM Name</th><th>Fellow Number</th><th>PM Number</th></tr>
	<tr><td>Alice Example</td><td>Emily Post</td><td>800-555-3456</td><td>800-555-1234</td></tr>
	<tr><td>Bobby Tables</td><td>Sample Mentor</td><td>800-555-3456</td><td>800-555-1234</td></tr>
	</table>

	<p>Upload that here in csv format and give it a name so you can reuse it next time (remember this tool is shared across regions, so be sure to give it a distinct name):</p>
		<label>New name:
			<input type="text" name="pmmapping_name" /></label>
		<br />
		<label>Upload new file:
			<input type="file" name="pmmapping" /></label>

		<br />
		<label>Message to pre-fill (saves time later, but optional)
			<textarea maxlength="140" name="default_message">Please fill our the survey for your Braven PM experience.</textarea>
		</label>
</fieldset>

<h1>Step two: get updated responses</h1>

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
