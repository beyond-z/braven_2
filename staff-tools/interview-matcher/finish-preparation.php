<?php
	include_once("sso.php");
	requireLogin();

	include_once("db.php");

	try {
		$fellows = array();
		$volunteers = array();
		if($fp = fopen($_FILES["fellows_csv"]["tmp_name"], "r")) {
			$rowNumber = 0;
			while(($data = fgetcsv($fp)) !== FALSE) {
				$rowNumber++;
				if($rowNumber == 1)
					continue; // skipping header
				if(empty(trim($data[0])))
					continue;
				$fellow = array(
					"name" => trim($data[0]),
					"score" => trim($data[1]),
					"available" => strtolower(trim($data[2])) == "true",
					"interests" => array_map("trim", explode(";", $data[3]))
				);

				$fellows[] = $fellow;
			}
			fclose($fp);
		}

		if($fp = fopen($_FILES["volunteers_csv"]["tmp_name"], "r")) {
			$rowNumber = 0;
			while(($data = fgetcsv($fp)) !== FALSE) {
				$rowNumber++;
				if($rowNumber == 1)
					continue; // skipping header
				if(empty(trim($data[0])))
					continue;
				$volunteer = array(
					"name" => trim($data[0]),
					"vip" => (strtolower(trim($data[1])) == "true" || strtolower(trim($data[1])) == "vip"),
					"available" => strtolower(trim($data[2])) == "true",
					"virtual" => strtolower(trim($data[3])) == "true",
					"number" => trim($data[4]),
					"interests" => array_map("trim", explode(";", $data[5]))
				);

				$volunteers[] = $volunteer;
			}
			fclose($fp);
		}

		$pdo->beginTransaction();
		$event_id = create_event_in_database($_POST["event_name"]);
		save_fellows_to_database($event_id, $fellows);
		save_volunteers_to_database($event_id, $volunteers);
		$pdo->commit();

		header("Location: matcher.php?event_id=$event_id");
	} catch (Exception $e) {
		$pdo->rollBack();
		echo "<h1>Error</h1>";
		echo "<pre>";
		echo htmlentities($e->getMessage());
		echo "</pre>";
	}
