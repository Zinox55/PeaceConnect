<?php
/**
 * Test direct d'envoi d'email de confirmation
 * Avec débogage complet
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test d'envoi d'email de confirmation - Mode Debug</h2>";
echo "<hr>";

// 1. Vérifier l'autoload
echo "<h3>1. Vérification PHPMailer</h3>";
$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("<p style='color: red;'>❌ PHPMailer non installé. Exécutez: <code>composer require phpmailer/phpmailer</code></p>");
}
echo "<p style='color: green;'>✓ PHPMailer trouvé</p>";
require_once $autoloadPath;

// 2. Vérifier la configuration
echo "<h3>2. Configuration Email</h3>";
$configFile = __DIR__ . '/config/config_mail.php';
if (!file_exists($configFile)) {
    die("<p style='color: red;'>❌ config_mail.php non trouvé</p>");
}
$config = require $configFile;
echo "<p style='color: green;'>✓ Configuration chargée</p>";
echo "<ul>";
echo "<li>Host: " . htmlspecialchars($config['smtp']['host']) . "</li>";
echo "<li>Port: " . htmlspecialchars($config['smtp']['port']) . "</li>";
echo "<li>Username: " . htmlspecialchars($config['smtp']['username']) . "</li>";
echo "<li>From: " . htmlspecialchars($config['from']['email']) . "</li>";
echo "</ul>";

// 3. Récupérer une commande de test
echo "<h3>3. Récupération d'une commande</h3>";
require_once 'config.php';
try {
    $db = config::getConnexion();
    $query = "SELECT * FROM commandes ORDER BY id DESC LIMIT 1";
    $result = $db->query($query);
    $commande = $result->fetch(PDO::FETCH_ASSOC);
    
    if (!$commande) {
        die("<p style='color: red;'>❌ Aucune commande trouvée</p>");
    }
    
    echo "<p style='color: green;'>✓ Commande trouvée: " . htmlspecialchars($commande['numero_commande']) . "</p>";
    echo "<ul>";
    echo "<li>Client: " . htmlspecialchars($commande['nom_client']) . "</li>";
    echo "<li>Email: " . htmlspecialchars($commande['email_client']) . "</li>";
    echo "<li>Total: " . number_format($commande['total'], 2) . " €</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    die("<p style='color: red;'>❌ Erreur DB: " . htmlspecialchars($e->getMessage()) . "</p>");
}

// 4. Récupérer les articles
echo "<h3>4. Articles de la commande</h3>";
try {
    $queryArticles = "SELECT dc.*, p.nom, p.prix 
                     FROM details_commande dc
                     INNER JOIN produits p ON dc.produit_id = p.id
                     WHERE dc.commande_id = :commande_id";
    $stmtArticles = $db->prepare($queryArticles);
    $stmtArticles->bindParam(':commande_id', $commande['id'], PDO::PARAM_INT);
    $stmtArticles->execute();
    $articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p style='color: green;'>✓ " . count($articles) . " article(s) trouvé(s)</p>";
    echo "<ul>";
    foreach ($articles as $article) {
        echo "<li>" . htmlspecialchars($article['nom']) . " x" . $article['quantite'] . " = " . number_format($article['prix_unitaire'] * $article['quantite'], 2) . " €</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    die("<p style='color: red;'>❌ Erreur articles: " . htmlspecialchars($e->getMessage()) . "</p>");
}

// 5. Préparer l'email
echo "<h3>5. Création du contenu email</h3>";

$emailBody = '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #5F9E7F; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: white; padding: 30px; border: 1px solid #e0e0e0; }
        .order-number { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center; }
        .order-number h2 { color: #5F9E7F; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #5F9E7F; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #e0e0e0; }
        .total { background: #e8f5e9; padding: 15px; border-radius: 8px; text-align: right; font-size: 1.2rem; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Commande Confirmée !</h1>
            <p>Merci pour votre confiance</p>
        </div>
        
        <div class="content">
            <p>Bonjour <strong>' . htmlspecialchars($commande['nom_client']) . '</strong>,</p>
            <p>Nous avons bien reçu votre commande et nous vous en remercions.</p>
            
            <div class="order-number">
                <p style="margin: 0; color: #666;">Numéro de commande</p>
                <h2>' . htmlspecialchars($commande['numero_commande']) . '</h2>
            </div>
            
            <h3 style="color: #5F9E7F;">📦 Articles commandés</h3>
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th style="text-align: center;">Qté</th>
                        <th style="text-align: right;">Prix</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>';

foreach ($articles as $article) {
    $sousTotal = $article['prix_unitaire'] * $article['quantite'];
    $emailBody .= '
                    <tr>
                        <td>' . htmlspecialchars($article['nom']) . '</td>
                        <td style="text-align: center;">' . $article['quantite'] . '</td>
                        <td style="text-align: right;">' . number_format($article['prix_unitaire'], 2) . ' €</td>
                        <td style="text-align: right;"><strong>' . number_format($sousTotal, 2) . ' €</strong></td>
                    </tr>';
}

$emailBody .= '
                </tbody>
            </table>
            
            <div class="total">
                <span style="color: #5F9E7F;">TOTAL: ' . number_format($commande['total'], 2) . ' €</span>
            </div>
        </div>
    </div>
</body>
</html>';

echo "<p style='color: green;'>✓ Email HTML généré (" . strlen($emailBody) . " caractères)</p>";

// 6. Initialiser PHPMailer
echo "<h3>6. Initialisation PHPMailer</h3>";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$mail = new PHPMailer(true);

try {
    // Configuration SMTP avec debug
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = 'html';
    
    $mail->isSMTP();
    $mail->Host = $config['smtp']['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp']['username'];
    $mail->Password = $config['smtp']['password'];
    $mail->SMTPSecure = $config['smtp']['secure'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $config['smtp']['port'];
    
    echo "<p style='color: green;'>✓ SMTP configuré</p>";
    
    // Configuration de l'encodage
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    
    // Expéditeur et destinataire
    $mail->setFrom($config['from']['email'], $config['from']['name']);
    $mail->addAddress($commande['email_client'], $commande['nom_client']);
    
    echo "<p style='color: green;'>✓ Expéditeur et destinataire configurés</p>";
    
    // Contenu
    $mail->isHTML(true);
    $mail->Subject = '✅ Confirmation de commande #' . $commande['numero_commande'] . ' - PeaceConnect';
    $mail->Body = $emailBody;
    $mail->AltBody = 'Commande confirmée: ' . $commande['numero_commande'];
    
    echo "<p style='color: green;'>✓ Contenu email préparé</p>";
    
    // 7. Envoi
    echo "<h3>7. Envoi de l'email</h3>";
    echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    
    $sent = $mail->send();
    
    echo "</div>";
    
    if ($sent) {
        echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h2>✅ EMAIL ENVOYÉ AVEC SUCCÈS !</h2>";
        echo "<p><strong>Destinataire:</strong> " . htmlspecialchars($commande['email_client']) . "</p>";
        echo "<p><strong>Commande:</strong> " . htmlspecialchars($commande['numero_commande']) . "</p>";
        echo "<p>Vérifiez la boîte mail du destinataire (et le dossier spam).</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h2>❌ ERREUR D'ENVOI</h2>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($mail->ErrorInfo) . "</p>";
    echo "<p><strong>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
    
    echo "<h3>Solutions possibles:</h3>";
    echo "<ul>";
    echo "<li>Vérifiez que le mot de passe d'application Gmail est correct</li>";
    echo "<li>Vérifiez que l'authentification à 2 facteurs est activée sur Gmail</li>";
    echo "<li>Vérifiez que les connexions moins sécurisées sont autorisées</li>";
    echo "<li>Vérifiez votre connexion internet</li>";
    echo "<li>Essayez le port 465 avec SSL au lieu de 587 avec TLS</li>";
    echo "</ul>";
}
?>
