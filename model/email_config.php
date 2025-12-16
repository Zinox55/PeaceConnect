<?php
// Configuration email pour PeaceConnect
// Utilisez ce fichier pour configurer l'envoi d'emails

class EmailConfig {
    // Méthode d'envoi: 'mail' (PHP mail), 'smtp' (SimpleSMTPMailer - SANS Composer), 'phpmailer' (PHPMailer avec Composer)
    public static $method = 'smtp'; // ← Changé pour utiliser SimpleSMTPMailer
    
    // Configuration SMTP (si method = 'smtp' ou 'phpmailer')
    public static $smtp_host = 'smtp.gmail.com';
    public static $smtp_port = 587;
    public static $smtp_username = 'ichrak9117@gmail.com'; // ← Votre email Gmail
    public static $smtp_password = 'votre-mot-de-passe-app'; // ← METTEZ ICI le mot de passe d'application Gmail
    public static $smtp_secure = 'tls'; // 'tls' ou 'ssl'
    
    // Configuration expéditeur
    public static $from_email = 'noreply@peaceconnect.com';
    public static $from_name = 'PeaceConnect';
    
    // Mode debug (true = logs détaillés)
    public static $debug = true;
}
?>