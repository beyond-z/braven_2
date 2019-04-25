<?php
	include_once("sso.php");
	requireAdmin();

	include_once("db.php");

	include_once("shared.php");

	$event_id = (int) $_REQUEST["event_id"];
	if($event_id == 0) {
		include_once("event_selection.php");
		show_event_selection_page();
		exit;
	}

	if(isset($_REQUEST["table"]) && $_REQUEST["table"] == "volunteers") {
		$editing = load_volunteers_from_database($event_id);
		$editing_table = "volunteers";
		$what = "Volunteer";
	} else {
		$editing = load_fellows_from_database($event_id);
		$editing_table = "fellows";
		$what = "Fellow";
	}

	function nonExcludedFields($var) {
		return $var != "UUID" && $var != "match_count";
	}

	function displayName($key) {
		if($key == "number")
			return "station/number";
		else
			return $key;
	}

?><!DOCTYPE html>
<html lang="en">
<head>
	<title>Braven Mock Interview Matcher</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<style>
		label, label > span { display: block; }
		label { margin-bottom: 1em; }
	</style>
</head>
<body>

	<?php

	if(isset($_POST["editing_table"])) {
		$success = false;
		if($_POST["editing_table"] == "fellows") {
			if(isset($_POST["id"]))
				update_fellow_in_database($_POST["id"], $_POST);
			else {
				$_POST["interests"] = explode(";", $_POST["interests"]);
				save_fellows_to_database($event_id, array($_POST));
			}
			$success = true;
		} else if($_POST["editing_table"] == "volunteers") {
			if(isset($_POST["id"]))
				update_volunteer_in_database($_POST["id"], $_POST);
			else {
				$_POST["interests"] = explode(";", $_POST["interests"]);
				save_volunteers_to_database($event_id, array($_POST));
			}
			$success = true;
		}

		if($success) {
			echo "Save successful.";
			echo " <a href=\"matcher.php?event_id=$event_id\">Go to Matcher</a>";
			echo " or ";
			echo "<a href=\"edit-sheet.php?table={$_POST["editing_table"]}&event_id=$event_id\">review sheet to do more last-minute changes</a>";
		} else {
			echo "failed, contact the tech team asap";
		}
	} else if(isset($_GET["id"])) {
		$editing = $editing[$_GET["id"]];
		$keys = array_filter(array_keys($editing), "nonExcludedFields");
	?>
		<form method="POST">
		<h3>Edit <?php echo $what; ?></h3>
		<input type="hidden" name="editing_table" value="<?php echo $editing_table; ?>" />
		<input type="hidden" name="id" value="<?php echo $editing["id"]; ?>" />
		<input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
		<?php
		foreach($keys as $key) {
			if($key == "id") continue;
			if($key == "interests")
				$val = join($editing[$key], "; ");
			else
				$val = $editing[$key];
			echo '<label><span>'.htmlentities(displayName($key)).'</span>
				<input type="text" name="'.htmlentities($key).'" value="'.htmlentities($val).'" /></label>';
		}
		?>
		<input type="submit" value="Save" />
		</form>
	<?php
	} else {
	?>

	<div style="float: right">
		View: <a href="edit-sheet.php?event_id=<?php echo $event_id;?>&table=fellows">Fellows</a> | <a href="edit-sheet.php?event_id=<?php echo $event_id; ?>&table=volunteers">Volunteers</a>
	</div>

	<a href="#new" onclick="document.getElementById('new').scrollIntoView(); document.querySelector('input[type=text]').focus(); return false;">Add Row</a>
	<table class="pretty">
	<caption><?php echo htmlentities($editing_table); ?></caption>
		<?php
			$keys = null;
			foreach($editing as $id => $value) {
				if($keys === null) {
					echo "<tr>";
					$keys = array_filter(array_keys($value), "nonExcludedFields");
					foreach($keys as $key) {
						echo "<th>";
						echo htmlentities(displayName($key));
						echo "</th>";
					}
					echo "</tr>";
				}

				echo "<tr>";
				foreach($keys as $key) {
					echo "<td>";
					if($key == "interests")
						echo htmlentities(join($value[$key], "; "));
					else if($key == "id")
						echo "<a href=\"edit-sheet.php?event_id=$event_id&table=$editing_table&id={$value[$key]}\">{$value[$key]}</a>";
					else
						echo htmlentities($value[$key]);
					echo "</td>";

				}
				echo "</tr>";
			}
		?>
	</table>

	<form id="new" method="POST">
	<h3>Add New <?php echo $what; ?></h3>
	<input type="hidden" name="editing_table" value="<?php echo $editing_table; ?>" />
	<input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
	<?php
	foreach($keys as $key) {
		if($key == "id") continue;
		echo '<label><span>'.htmlentities(displayName($key)).'</span>
			<input type="text" name="'.htmlentities($key).'" value="'.htmlentities("").'" /></label>';
	}
	?>
	<input type="submit" value="Save" />
	</form>

	<?php } ?>
</body>
</html>
