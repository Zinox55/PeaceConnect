<?php
// Library: SimpleSMTPMailer
// Classe simple pour envoyer des emails via SMTP sans dépendances externes

/**
 * Classe simple pour envoyer des emails via SMTP sans dépendances
 * Alternative à PHPMailer pour XAMPP
 */
class SimpleSMTPMailer {
    private $host;
    private $port;
    private $username;
    private $password;
    private $from;
    private $fromName;
    private $timeout = 30;
    private $debug = false;
    private $errors = [];
    
    public function __construct($config = []) {
        $this->host = $config['host'] ?? 'smtp.gmail.com';
        $this->port = $config['port'] ?? 587;
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->from = $config['from'] ?? $this->username;
        $this->fromName = $config['fromName'] ?? 'PeaceConnect';
        $this->debug = $config['debug'] ?? false;
    }
    
    public function send($to, $subject, $htmlBody) {
        $this->errors = [];
        
        // Validation
        if (empty($this->username) || empty($this->password)) {
            $this->errors[] = "SMTP username/password not configured";
            return false;
        }
        
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid recipient email: $to";
            return false;
        }
        
        try {
            // Connexion au serveur SMTP
            $socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
            
            if (!$socket) {
                $this->errors[] = "Cannot connect to SMTP server: $errstr ($errno)";
                return false;
            }
            
            stream_set_timeout($socket, $this->timeout);
            
            // Lire la réponse du serveur
            $response = fgets($socket, 515);
            $this->log("Server: $response");
            
            if (substr($response, 0, 3) != '220') {
                $this->errors[] = "SMTP connection failed: $response";
                fclose($socket);
                return false;
            }
            
            // EHLO
            $this->sendCommand($socket, "EHLO " . gethostname(), 250);
            
            // STARTTLS
            $this->sendCommand($socket, "STARTTLS", 220);
            
            // Activer le chiffrement TLS
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->errors[] = "Cannot enable TLS encryption";
                fclose($socket);
                return false;
            }
            
            // EHLO après TLS
            $this->sendCommand($socket, "EHLO " . gethostname(), 250);
            
            // AUTH LOGIN
            $this->sendCommand($socket, "AUTH LOGIN", 334);
            $this->sendCommand($socket, base64_encode($this->username), 334);
            $this->sendCommand($socket, base64_encode($this->password), 235);
            
            // MAIL FROM
            $this->sendCommand($socket, "MAIL FROM: <{$this->from}>", 250);
            
            // RCPT TO
            $this->sendCommand($socket, "RCPT TO: <$to>", 250);
            
            // DATA
            $this->sendCommand($socket, "DATA", 354);
            
            // En-têtes et corps de l'email
            $boundary = md5(time());
            $headers = "From: {$this->fromName} <{$this->from}>\r\n";
            $headers .= "To: $to\r\n";
            $headers .= "Subject: $subject\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
            
            $textBody = strip_tags($htmlBody);
            
            $body = "--$boundary\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
            $body .= $textBody . "\r\n\r\n";
            $body .= "--$boundary\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
            $body .= $htmlBody . "\r\n\r\n";
            $body .= "--$boundary--\r\n";
            
            $message = $headers . "\r\n" . $body . "\r\n.\r\n";
            
            fputs($socket, $message);
            $response = fgets($socket, 515);
            $this->log("Data response: $response");
            
            if (substr($response, 0, 3) != '250') {
                $this->errors[] = "Email sending failed: $response";
                fclose($socket);
                return false;
            }
            
            // QUIT
            fputs($socket, "QUIT\r\n");
            $response = fgets($socket, 515);
            $this->log("Quit response: $response");
            
            fclose($socket);
            return true;
            
        } catch (Exception $e) {
            $this->errors[] = "Exception: " . $e->getMessage();
            return false;
        }
    }
    
    private function sendCommand($socket, $command, $expectedCode) {
        $this->log("Client: $command");
        fputs($socket, "$command\r\n");
        $response = fgets($socket, 515);
        $this->log("Server: $response");
        
        if (substr($response, 0, 3) != $expectedCode) {
            throw new Exception("Command failed: $command. Response: $response");
        }
        
        return $response;
    }
    
    private function log($message) {
        if ($this->debug) {
            $logFile = __DIR__ . '/logs/smtp_debug.txt';
            @file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
        }
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getLastError() {
        return end($this->errors);
    }
}
?>
