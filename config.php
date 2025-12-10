<?php
// config.php

// ========== DATABASE ==========
function getPDO() {
    try {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=voluenteer_db;charset=utf8mb4",
            "root",
            ""
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (Exception $e) {
        die("Erreur connexion DB : " . $e->getMessage());
    }
}

// ========== EMAIL (GMAIL SMTP avec PHPMailer) ==========
// Pour Gmail : crée un App Password sur https://myaccount.google.com/apppasswords
define('MAIL_SMTP_HOST', 'smtp.gmail.com');
define('MAIL_SMTP_PORT', 587); // Port STARTTLS
define('MAIL_SMTP_USER', 'peaceconnect3@gmail.com'); // Remplace par ton Gmail
define('MAIL_SMTP_PASS', 'qzhc zjxw vkyf ygoe'); // App Password, pas ton mot de passe normal
define('MAIL_FROM_EMAIL', 'peaceconnect3@gmail.com'); // Même email que MAIL_SMTP_USER
define('MAIL_FROM_NAME', 'PeaceConnect');

// ========== BASE URL ==========
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host . '/PeaceConnect';
}

define('BASE_URL', getBaseUrl());
?>