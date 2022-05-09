<?php

declare(strict_types=1);

namespace SallePW\SlimApp\Utilities;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use SallePW\SlimApp\Model\RegisteredUser;
use SallePW\SlimApp\Model\Token;
use SallePW\SlimApp\Model\User;

final class EmailHandler
{
    public function __construct()
    {
    }

    public function sendActivationToken(RegisteredUser $user, Token $token) : void
    {
        // send a confirmation email with their token 
        // (/activate?token=12345678)

        $mail = new PHPMailer(true);
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                //Enable verbose debug output
            $mail->isSMTP();                                      //Send using SMTP
            $mail->Host       = 'mail.smtpbucket.com';            //Set the SMTP server to send through
            $mail->Port       = 8025;                              //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom($_ENV['SENDER'], 'Me');
            $mail->addAddress($user->email(), 'Destination');   //Add a recipient
            //Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            $emailUrl = 'http://'.$_SERVER['SERVER_NAME'].':'.$_ENV['PHP_PORT'].$_SERVER['ACTIVATE_TOKEN_URL'].'?token='.$token->getTokenValue();
            $emailUrl;
            //Content 
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Confirm the registration';
            $mail->Body    = 'To confirm the registration, please <a href = '.$emailUrl.'> activate your account </a>';
            $mail->AltBody = 'Confirm the registration with this link: '.$emailUrl;

            $mail->send();
            
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function sendRegistrationConfirmation(String $user_email) : void
    {
        //Send an email with registration confirmation and link to login
        // /login
        $mail = new PHPMailer(true);
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                //Enable verbose debug output
            $mail->isSMTP();                                      //Send using SMTP
            $mail->Host       = 'mail.smtpbucket.com';            //Set the SMTP server to send through
            $mail->Port       = 8025;                              //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom($_ENV['SENDER'], 'Me');
            $mail->addAddress($user_email, 'Destination');   //Add a recipient
            //Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            $emailUrl = 'http://'.$_SERVER['SERVER_NAME'].':'.$_ENV['PHP_PORT'].'/login';
            $emailUrl;
            //Content 
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Registration has been successful';
            $mail->Body    = 'Thank you for the registration. Your account has been activated. You can now <a href = '.$emailUrl.'> login </a>';
            $mail->AltBody = 'Thank you for the registration. Your account has been activated. You can now login: '.$emailUrl;

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    }
}
