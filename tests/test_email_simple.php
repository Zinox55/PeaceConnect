<?php
/**
 * Test simple et rapide d'envoi d'email
 */

// Désactiver la limite de temps
set_time_limit(60);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Test Email Simple</title></head><body>";
echo "<h2>🧪 Test d'envoi d'email simple</h2><hr>";

// 1. Charger PHPMailer
echo "<p>1. Chargement PHPMailer...</p>";
require_once __DIR__ . '/vendor/autoload.php';
echo "<p style='color: green;'>✓ PHPMailer chargé</p>";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// 2. Charger configuration
echo "<p>2. Chargement configuration...</p>";
$config = require __DIR__ . '/config/config_mail.php';
echo "<p style='color: green;'>✓ Configuration chargée</p>";
echo "<ul>";
echo "<li>Host: " . htmlspecialchars($config['smtp']['host']) . "</li>";
echo "<li>Port: " . htmlspecialchars($config['smtp']['port']) . "</li>";
echo "<li>From: " . htmlspecialchars($config['from']['email']) . "</li>";
echo "</ul>";

// 3. Créer le mailer
echo "<p>3. Initialisation PHPMailer...</p>";
$mail = new PHPMailer(true);

try {
    // Configuration SMTP
    $mail->isSMTP();
    $mail->Host = $config['smtp']['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp']['username'];
    $mail->Password = $config['smtp']['password'];
    $mail->SMTPSecure = $config['smtp']['secure'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $config['smtp']['port'];
    $mail->CharSet = 'UTF-8';
    
    // IMPORTANT: Activer le debug pour voir les erreurs
    $mail->SMTPDebug = SMTP::DEBUG_CONNECTION; // Niveau minimum de debug
    $mail->Debugoutput = function($str, $level) {
        echo "<div style='background: #f0f0f0; padding: 5px; margin: 2px 0; font-size: 0.85rem; font-family: monospace;'>$str</div>";
    };
    
    echo "<p style='color: green;'>✓ SMTP configuré</p>";
    
    // 4. Préparer l'email
    echo "<p>4. Préparation de l'email...</p>";
    $mail->setFrom($config['from']['email'], $config['from']['name']);
    $mail->addAddress($config['from']['email']); // Envoyer à soi-même pour tester
    
    $mail->isHTML(true);
    $mail->Subject = 'Test Email PeaceConnect - ' . date('Y-m-d H:i:s');
    $mail->Body = '<h1>Test réussi !</h1><p>Cet email a été envoyé depuis PeaceConnect.</p><p>Date: ' . date('Y-m-d H:i:s') . '</p>';
    
    echo "<p style='color: green;'>✓ Email préparé</p>";
    
    // 5. Envoyer
    echo "<hr><h3>5. Envoi en cours...</h3>";
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    
    $result = $mail->send();
    
    echo "</div>";
    
    if ($result) {
        echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 5px solid #28a745;'>";
        echo "<h2>✅ EMAIL ENVOYÉ AVEC SUCCÈS !</h2>";
        echo "<p><strong>Destinataire:</strong> " . htmlspecialchars($config['from']['email']) . "</p>";
        echo "<p><strong>Sujet:</strong> Test Email PeaceConnect</p>";
        echo "<p>Vérifiez votre boîte mail (et le dossier spam).</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 5px solid #dc3545;'>";
    echo "<h2>❌ ERREUR D'ENVOI</h2>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($mail->ErrorInfo) . "</p>";
    echo "<p><strong>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 5px solid #ffc107;'>";
    echo "<h3>💡 Solutions possibles:</h3>";
    echo "<ol>";
    echo "<li><strong>Mot de passe d'application Gmail:</strong><br>";
    echo "- Allez sur <a href='https://myaccount.google.com/apppasswords' target='_blank'>https://myaccount.google.com/apppasswords</a><br>";
    echo "- Activez l'authentification à 2 facteurs si ce n'est pas fait<br>";
    echo "- Générez un nouveau mot de passe d'application<br>";
    echo "- Remplacez le mot de passe dans <code>config/config_mail.php</code></li>";
    
    echo "<li><strong>Vérifiez les paramètres SMTP:</strong><br>";
    echo "- Host: smtp.gmail.com<br>";
    echo "- Port: 587 (TLS) ou 465 (SSL)<br>";
    echo "- Secure: 'tls' pour 587, 'ssl' pour 465</li>";
    
    echo "<li><strong>Pare-feu/Antivirus:</strong><br>";
    echo "- Vérifiez que le port " . $config['smtp']['port'] . " n'est pas bloqué<br>";
    echo "- Essayez de désactiver temporairement l'antivirus</li>";
    
    echo "<li><strong>Connexion internet:</strong><br>";
    echo "- Vérifiez votre connexion internet<br>";
    echo "- Essayez depuis un autre réseau si possible</li>";
    
    echo "</ol>";
    echo "</div>";
}

echo "<hr>";
echo "<p style='text-align: center;'>";
echo "<a href='view/front/commande.html' style='background: #5F9E7F; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>🛒 Passer une commande</a> ";
echo "<a href='voir_logs_emails.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>📋 Voir les logs</a>";
echo "</p>";

echo "</body></html>";
?>
