<?php
// Library: EmailSender
// Utilitaire pour l'envoi d'emails

include_once __DIR__ . '/email_config.php';
include_once __DIR__ . '/SimpleSMTPMailer.php';

class EmailSender {
    private $logFile;
    
    public function __construct() {
        $this->logFile = __DIR__ . '/logs/email_log.txt';
    }
    
    /**
     * Envoie un email avec gestion d'erreur et logging
     */
    public function send($to, $subject, $htmlMessage, $fromEmail = null, $fromName = null) {
        $fromEmail = $fromEmail ?? EmailConfig::$from_email;
        $fromName = $fromName ?? EmailConfig::$from_name;
        
        $result = false;
        $error = null;
        
        try {
            switch(EmailConfig::$method) {
                case 'phpmailer':
                    $result = $this->sendWithPHPMailer($to, $subject, $htmlMessage, $fromEmail, $fromName);
                    break;
                    
                case 'smtp':
                    // Utilise SimpleSMTPMailer (ne nécessite pas Composer)
                    $result = $this->sendWithSimpleSMTP($to, $subject, $htmlMessage, $fromEmail, $fromName);
                    break;
                    
                case 'mail':
                default:
                    $result = $this->sendWithMail($to, $subject, $htmlMessage, $fromEmail, $fromName);
                    break;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            $result = false;
        }
        
        // Logging
        if (EmailConfig::$debug) {
            $this->log($to, $subject, $result, $error);
        }
        
        return $result;
    }
    
    /**
     * Envoi avec la fonction mail() native de PHP
     */
    private function sendWithMail($to, $subject, $htmlMessage, $fromEmail, $fromName) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . $fromName . " <" . $fromEmail . ">\r\n";
        $headers .= "Reply-To: " . $fromEmail . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        $result = @mail($to, $subject, $htmlMessage, $headers);
        
        if (!$result) {
            $lastError = error_get_last();
            throw new Exception($lastError['message'] ?? 'mail() returned false');
        }
        
        return $result;
    }
    
    /**
     * Envoi avec SimpleSMTPMailer (ne nécessite PAS Composer!)
     */
    private function sendWithSimpleSMTP($to, $subject, $htmlMessage, $fromEmail, $fromName) {
        $mailer = new SimpleSMTPMailer([
            'host' => EmailConfig::$smtp_host,
            'port' => EmailConfig::$smtp_port,
            'username' => EmailConfig::$smtp_username,
            'password' => EmailConfig::$smtp_password,
            'from' => $fromEmail,
            'fromName' => $fromName,
            'debug' => EmailConfig::$debug
        ]);
        
        $result = $mailer->send($to, $subject, $htmlMessage);
        
        if (!$result) {
            throw new Exception($mailer->getLastError());
        }
        
        return true;
    }
    
    /**
     * Envoi avec PHPMailer (nécessite: composer require phpmailer/phpmailer)
     */
    private function sendWithPHPMailer($to, $subject, $htmlMessage, $fromEmail, $fromName) {
        // Vérifier si PHPMailer est installé
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            throw new Exception('PHPMailer n\'est pas installé. Installez-le avec: composer require phpmailer/phpmailer');
        }
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Configuration serveur
            $mail->isSMTP();
            $mail->Host = EmailConfig::$smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = EmailConfig::$smtp_username;
            $mail->Password = EmailConfig::$smtp_password;
            $mail->SMTPSecure = EmailConfig::$smtp_secure;
            $mail->Port = EmailConfig::$smtp_port;
            $mail->CharSet = 'UTF-8';
            
            // Destinataires
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);
            
            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlMessage;
            $mail->AltBody = strip_tags($htmlMessage);
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            throw new Exception("PHPMailer Error: {$mail->ErrorInfo}");
        }
    }
    
    /**
     * Log les tentatives d'envoi
     */
    private function log($to, $subject, $success, $error = null) {
        $logEntry = "\n========== " . date('Y-m-d H:i:s') . " ==========\n";
        $logEntry .= "TO: " . $to . "\n";
        $logEntry .= "SUBJECT: " . $subject . "\n";
        $logEntry .= "METHOD: " . EmailConfig::$method . "\n";
        $logEntry .= "STATUS: " . ($success ? "✓ SUCCESS" : "✗ FAILED") . "\n";
        
        if ($error) {
            $logEntry .= "ERROR: " . $error . "\n";
        }
        
        if (!$success && EmailConfig::$method == 'mail') {
            $logEntry .= "\nNOTE: XAMPP n'envoie pas d'emails par défaut!\n";
            $logEntry .= "Solutions:\n";
            $logEntry .= "1. Configurez SMTP dans php.ini\n";
            $logEntry .= "2. Utilisez PHPMailer (changez EmailConfig::\$method = 'phpmailer')\n";
            $logEntry .= "3. Utilisez un service comme SendGrid ou Mailgun\n";
        }
        
        $logEntry .= "-------------------------------------------\n";
        
        @file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }
}
?>
