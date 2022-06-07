<?php


namespace core\lib;


use core\router\App;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    public static function smtp($to, $name, $subject, $message)
    {

        $SMTP = new PHPMailer(true);

        try {
            $SMTP->isSMTP();
            $SMTP->SMTPDebug = false;
            $SMTP->Host = getenv('HOST');
            $SMTP->SMTPAuth = true;
            $SMTP->Username = getenv('USERNAME');
            $SMTP->Password = getenv('PASSWORD');
            $SMTP->SMTPSecure = getenv('SMTP_TYPE');
            $SMTP->Port = 465;

            $SMTP->setFrom(getenv('FROM_EMAIL'), getenv('FROM_NAME'));
            $SMTP->addAddress($to, $name);
            $SMTP->addReplyTo(getenv('REPLY_TO'), getenv('REPLY_NAME'));

            $SMTP->isHTML(true);
            $SMTP->Subject = $subject;
            $SMTP->Body = $message;
            $SMTP->send();
            return true;
        } catch (Exception $e) {

            if (App::get('config')['options']['debug']) {
                App::logError('There was a PDO Exception. Details: ' . $e);
                return false;
            }
            return false;
        }
    }
}
