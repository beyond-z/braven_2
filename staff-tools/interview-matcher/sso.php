<?php

// to use this: include it before anything else (including whitespace!) then call requireLogin(); before anything else too
session_start();

require_once("credentials.php");

function bz_current_full_url() {
	$url = "http";
	if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
		$url .= "s";
	$url .= "://";
	$url .= $_SERVER["HTTP_HOST"];
	$url .= $_SERVER["PHP_SELF"];
	$url .= "?";
	$url .= $_SERVER["QUERY_STRING"];
	return $url;
}

if(isset($_SESSION["sso_service"]) && isset($_SESSION["coming_from"]) && isset($_GET["ticket"])) {
	// validate ticket from the SSO server

	$ticket = $_GET["ticket"];
	$service = $_SESSION["sso_service"];
	$coming_from = $_SESSION["coming_from"];
	unset($_SESSION["sso_service"]);
	unset($_SESSION["coming_from"]);

	$content = file_get_contents("https://{$WP_CONFIG["BRAVEN_SSO_DOMAIN"]}/serviceValidate?ticket=".urlencode($ticket)."&service=".urlencode($service));

	$xml = new DOMDocument();
	$xml->loadXML($content);
	$user = $xml->getElementsByTagNameNS("*", "user")->item(0)->textContent;

	if($user == "admin@beyondz.org" || strpos($user, "@bebraven.org") !== FALSE) {
		// login successful
		$_SESSION["user"] = $user;
	} else {
		echo "User " . htmlentities($user) . " is not authorized. Try logging out of SSO first.";
		exit;
	}

	header("Location: " . $coming_from);
	exit;
} else if(isset($_SESSION["coming_from"]) && !isset($_SESSION["sso_service"])) {
	$ssoService = bz_current_full_url();
	$_SESSION["sso_service"] = $ssoService;
	header("Location: https://{$WP_CONFIG["BRAVEN_SSO_DOMAIN"]}/login?service=" . urlencode($ssoService));
	exit;
} // otherwise it is just an api thing for other uses

// returns the currently logged in user, or redirects+exits to SSO
function requireLogin() {
	if(!isset($_SESSION["user"])) {
		$_SESSION["coming_from"] = bz_current_full_url();
		unset($_SESSION["sso_service"]);
		header("Location: sso.php");
		exit;
	}
	return $_SESSION["user"];
}
