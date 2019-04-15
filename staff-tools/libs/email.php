<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require dirname(__DIR__) . '/libs/PHPMailer/Exception.php';
require dirname(__DIR__) . '/libs/PHPMailer/PHPMailer.php';
require dirname(__DIR__) . '/libs/PHPMailer/SMTP.php';

function getMailer() {
	global $WP_CONFIG;

	$mail = new PHPMailer(true);
	$mail->isSMTP();
	$mail->Host = $WP_CONFIG["BRAVEN_SMTP_HOST"];
	$mail->SMTPAuth = true;
	$mail->Username = $WP_CONFIG["BRAVEN_SMTP_USERNAME"];
	$mail->Password = $WP_CONFIG["BRAVEN_SMTP_PASSWORD"];
	$mail->SMTPSecure = "tls";
	$mail->Port = 587;
	$mail->setFrom("no-reply@bebraven.org", "Braven Team");

	return $mail;
}

function sendEmail($to, $subject, $message) {
	$mail = getMailer();

	$mail->addAddress($to);
	$mail->Subject = $subject;
	$mail->Body = $message;

	$mail->send();
}

