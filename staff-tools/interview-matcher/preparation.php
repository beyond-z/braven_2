<?php
	include_once("sso.php");
	requireLogin();
?><!DOCTYPE html>
<html>
<head>
	<title>Preparation for Interview Event</title>
</head>
<body>

<form method="POST" action="finish-preparation.php" enctype="multipart/form-data">
	<p>In preparation for the event, we need to get some information.</p>

	<p>First, let's give it a name so we can archive the results for later.</p>
	<p>The name should include your region and other info so it is easy to recognize in a list, like maybe "Rutgers DYC Feb 2018"</p>

	<label>
		<span>Event name:</span><br />
		<input type="text" name="event_name" />
	</label>

	<br /><br />

	<label>
		<span>University:</span><br />
		<select name="university">
			<option value="(none)"></option>
			<option value="National Louis University">National Louis University</option>
			<option value="Rutgers University - Newark">Rutgers University - Newark</option>
			<option value="San Jose State University">San Jose State University</option>
		</select>
	</label>

	<hr />

	<p>Then, we need to know who is participating. The volunteers spreadsheet you create should look like this:</p>

	<table>
		<caption>Sample Volunteers Spreadsheet</caption>
		<tr>
			<th>Name</th>
			<th>VIP</th>
			<th>Available</th>
			<th>Virtual</th>
			<th>Number</th>
			<th>Interests</th>
			<th>Feedback Nag Address</th>
		</tr>
		<tr>
			<td>Bart</td>
			<td>true</td>
			<td>true</td>
			<td>false</td>
			<td>212-555-1234</td>
			<td>psychology; sociology</td>
			<td>212-555-1234</td>
		</tr>
		<tr>
			<td>Peggy</td>
			<td>false</td>
			<td>true</td>
			<td>false</td>
			<td>Room 123</td>
			<td>psychology; medicine</td>
			<td>212-555-9876</td>
		</tr>
	</table>

	<p>The feedback nag address is <b>new in 2019</b>. It should be the volunteer's cell number, not necessarily the number from which they will conduct the interview. The system will text that number a link to the rubric for all their fellow matches.</p>

	<p>Note that the interests are separated by semicolons in the cell.</p>

	<p>And make sure it is in <b>.csv</b> format, comma-separated values.</p>

	<label>
		<span>Volunteers spreadsheet:</span><br />
		<input type="file" name="volunteers_csv" />
	</label>

	<hr />

	<p>Fellows get a score column. This is a number you make up that gives them preferred treatment.</p>

	<p>Therefore, the fellows spreadsheet you create should look like this:</p>

	<table>
		<caption>Sample Fellows Spreadsheet</caption>
		<tr>
			<th>Name</th>
			<th>Score</th>
			<th>Available</th>
			<th>Interests</th>
		</tr>
		<tr>
			<td>Anne</td>
			<td>45</td>
			<td>true</td>
			<td>psychology; pride; prejudice</td>
		</tr>
		<tr>
			<td>Bert</td>
			<td>25</td>
			<td>true</td>
			<td>theology; spaceships</td>
		</tr>
	</table>

	<p>Note that the interests are separated by semicolons in the cell.</p>

	<p>And make sure it is in <b>.csv</b> format, comma-separated values.</p>

	<label>
		<span>Fellows spreadsheet:</span><br />
		<input type="file" name="fellows_csv" />
	</label>

	<br /><br /><br />

	<input type="submit" value="Submit" />
</form>
</body>
</html>
