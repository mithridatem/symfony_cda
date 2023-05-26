<?php
namespace App\Service;
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class Messagerie{
    public function sendEmail($login, $mdp, $objet, $content, $addresse){
        //Load Composer's autoloader
        require '../vendor/autoload.php';
        
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $login;
            $mail->Password   = $mdp;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            //expéditeur
            $mail->setFrom($login, 'Admin');
            //destinataire
            $mail->addAddress($addresse);
            //Content
            $mail->isHTML(true);
            $mail->Subject = $objet;
            $mail->Body = $content;
            //envoi du mail
            $mail->send();
            return 'Le mail à été envoyé avec succés';
        } 
        catch (Exception $e) {
            return "Erreur Mail :{$mail->ErrorInfo}";
        }
    }
}
?>