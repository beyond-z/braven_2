<?php 

$filename = 'matches.csv';
$matches_file = fopen($filename, "a+") or die("Unable to open matches file!");
$match_history = fwrite($matches_file,$_POST['matches'].',');
fclose($matches_file);

?>
<h2><a href="./">Match again</a></h2>