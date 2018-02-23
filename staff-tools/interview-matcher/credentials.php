<?php
/*
	The purpose of this file is to piggy-back off the existing wp-config.php
	constants.

	We cannot simply include() that file since it would want to call a bunch
	of Wordpress functions too (why didn't they just make it a collection of
	data without function calls?), but we can pull it out with some basic
	grep action since all the defines follow the same pattern.
*/
// lol "parsing" with regular expressions

$WP_CONFIG = array();

function bzLoadWpConfig() {
	global $WP_CONFIG;

	$out = array();
	preg_match_all("/define\('([A-Z_0-9]+)', '(.*)'\);/", file_get_contents("../../wp-config.php"), $out, PREG_SET_ORDER);

	foreach($out as $match) {
		$WP_CONFIG[$match[1]] = $match[2];
	}
}

bzLoadWpConfig();
