<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../controller/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../controller/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../controller/PHPMailer/src/SMTP.php';

class Mailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $fromEmail;
    private $fromName;

    public function __construct() {
        require_once __DIR__ . '/../config.php';
        $this->host = MAIL_SMTP_HOST;
        $this->port = MAIL_SMTP_PORT;
        $this->username = MAIL_SMTP_USER;
        $this->password = MAIL_SMTP_PASS;
        $this->fromEmail = MAIL_FROM_ADDRESS;
        $this->fromName = MAIL_FROM_NAME;
    }

    public function sendVerificationEmail($to, $nom, $token) {
        $verifyUrl = BASE_URL . '/view/FrontOffice/verify.php?token=' . urlencode($token);
        $subject = 'Confirmez votre inscription - PeaceConnect';

        $htmlBody = $this->getVerificationEmailTemplate($nom, $verifyUrl);
        $textBody = "Bonjour $nom,\n\nVeuillez confirmer votre inscription en cliquant sur ce lien :\n$verifyUrl\n\nCordialement,\nPeaceConnect";

        return $this->sendSMTP($to, $subject, $htmlBody, $textBody);
    }

    private function getVerificationEmailTemplate($nom, $verifyUrl) {
        $year = date('Y');
        return <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .content { padding: 30px; }
        .content p { color: #333; line-height: 1.6; margin: 15px 0; }
        .btn { display: inline-block; background-color: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; text-align: center; }
        .btn:hover { background-color: #764ba2; }
        .footer { background-color: #f9f9f9; color: #666; padding: 20px; text-align: center; font-size: 12px; border-top: 1px solid #eee; }
        .warning { background-color: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 12px; border-radius: 5px; margin: 15px 0; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PeaceConnect</h1>
            <p>Confirmez votre inscription</p>
        </div>
        <div class="content">
            <p>Bonjour <strong>{$nom}</strong>,</p>
            <p>Merci de vous être inscrit à PeaceConnect ! Pour finaliser votre inscription, veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous :</p>
            <center>
                <a href="{$verifyUrl}" class="btn">Confirmer mon inscription</a>
            </center>
            <p>Ou copiez-collez ce lien dans votre navigateur :</p>
            <p style="word-break: break-all; color: #667eea; font-size: 12px;">{$verifyUrl}</p>
            <div class="warning">
                <strong>⚠️ Sécurité :</strong> Ce lien expire dans 24h. Si vous n'avez pas créé ce compte, ignorez cet email.
            </div>
            <p>À bientôt sur PeaceConnect !<br><strong>L'équipe PeaceConnect</strong></p>
        </div>
        <div class="footer">
            <p>© {$year} PeaceConnect. Tous droits réservés.<br>
            Pour toute question, contactez-nous à contact@peaceconnect.com</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function sendSMTP($to, $subject, $htmlBody, $textBody) {
        try {
            $mail = new PHPMailer(true);
            
            // Configuration SMTP Gmail
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->port;
            $mail->CharSet = 'UTF-8';

            // Debug option (disabled by default)
            // Uncomment the next line to get verbose SMTP logs in PHP error log
            $mail->SMTPDebug = 2; // 0=no, 1=commands, 2=commands+data
            $mail->Debugoutput = function($str, $level) {
                $logDir = __DIR__ . '/../logs';
                if (!is_dir($logDir)) {
                    @mkdir($logDir, 0777, true);
                }
                $logFile = $logDir . '/smtp_debug.log';
                @file_put_contents($logFile, date('Y-m-d H:i:s') . " | " . $str . "\n", FILE_APPEND);
            };
            
            // Expéditeur et destinataire
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);
            
            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody;
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            // Write a dedicated log file for mail errors
            $logDir = __DIR__ . '/../logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0777, true);
            }
            $logFile = $logDir . '/mail_error.log';
            $message = date('Y-m-d H:i:s') . " | PHPMailer Error: " . $mail->ErrorInfo . " | Exception: " . $e->getMessage() . "\n";
            @file_put_contents($logFile, $message, FILE_APPEND);
            error_log($message);
            return false;
        }
    }
}
?>
