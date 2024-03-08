<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

$email_send_data = $_JOB['data'];

$mail->SMTPDebug = SMTP::DEBUG_OFF;
$mail->isSMTP();
$mail->Host = MAIL_HOST;
$mail->SMTPAuth = true;
$mail->Username = MAIL_USERNAME;
$mail->Password = MAIL_PASSWORD;
$mail->SMTPSecure = MAIL_ENCRYPTION;
$mail->Port = MAIL_PORT;

$mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
$mail->addAddress($email_send_data['email'], $email_send_data['name']);

$mail->isHTML(true);
$mail->Subject = $email_send_data['subject'];
$mail->Body = $email_send_data['body'];
$mail->AltBody = $email_send_data['body'];

$mail->send();
