<?php
header('Content-Type: text/html; charset=utf-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function SendNewDiscountRequestEmail($data)
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = $_SERVER['MAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_SERVER['MAIL_USER'];
        $mail->Password = $_SERVER['MAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        //Recipients
        $mail->setFrom($_SERVER['MAIL_SEND_FROM'], 'Nsp Reseller no-reply');
        $mail->addAddress($_SERVER['MAIL_SEND_TO'], 'Elena Demenovich');
        $mail->addReplyTo('info@nspreseller.by', 'Information');


        $mail->isHTML(true);
        $mail->Subject = 'Новый запрос на дисконтную карту от "' . $data['userName'] . '"';
        $mail->Body = 'Новый запрос на дисконтную карту от <b>"' . $data['userName'] . '"</b>';
        $mail->AltBody = 'Новый запрос на дисконтную карту от "' . $data['userName'] . '"';

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
