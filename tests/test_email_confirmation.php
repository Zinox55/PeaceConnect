<?php
/**
 * Test d'envoi d'email de confirmation
 * Simuler l'envoi d'un email pour une commande existante
 */

// Récupérer le numéro de commande (dernière commande créée)
require_once 'config.php';

try {
    $db = config::getConnexion();
    
    // Récupérer la dernière commande
    $query = "SELECT * FROM commandes ORDER BY id DESC LIMIT 1";
    $result = $db->query($query);
    $commande = $result->fetch(PDO::FETCH_ASSOC);
    
    if (!$commande) {
        die("Aucune commande trouvée dans la base de données");
    }
    
    echo "<h2>Test d'envoi d'email de confirmation</h2>";
    echo "<p>Commande trouvée: <strong>" . htmlspecialchars($commande['numero_commande']) . "</strong></p>";
    echo "<p>Client: <strong>" . htmlspecialchars($commande['nom_client']) . "</strong></p>";
    echo "<p>Email: <strong>" . htmlspecialchars($commande['email_client']) . "</strong></p>";
    echo "<hr>";
    
    // Vérifier la configuration email
    $configFile = __DIR__ . '/config/config_mail.php';
    
    if (!file_exists($configFile)) {
        echo "<p style='color: orange;'>⚠️ Configuration email non trouvée. Création de la configuration par défaut...</p>";
        
        // Créer une configuration de test
        if (!is_dir(__DIR__ . '/config')) {
            mkdir(__DIR__ . '/config', 0755, true);
        }
        
        $defaultConfig = "<?php
return [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'votre-email@gmail.com',
    'password' => 'votre-mot-de-passe',
    'from_email' => 'noreply@peaceconnect.org',
    'from_name' => 'PeaceConnect',
    'encryption' => 'tls'
];
?>";
        
        file_put_contents($configFile, $defaultConfig);
        echo "<p style='color: orange;'>✓ Fichier config_mail.php créé</p>";
        echo "<p style='background: #fff3cd; padding: 10px;'><strong>Important:</strong> Veuillez éditer le fichier <code>config/config_mail.php</code> avec vos identifiants SMTP réels.</p>";
    } else {
        echo "<p style='color: green;'>✓ Configuration email trouvée</p>";
    }
    
    echo "<hr>";
    echo "<h3>Appel de l'API d'envoi d'email</h3>";
    
    // Construire l'URL de l'API
    $url = 'http://localhost/PeaceConnect/controller/EmailController.php?action=confirmation&numero=' . urlencode($commande['numero_commande']);
    
    echo "<p>URL: <code>" . htmlspecialchars($url) . "</code></p>";
    echo "<p><a href='$url' target='_blank' style='background: #5F9E7F; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>🚀 Tester l'envoi d'email</a></p>";
    
    echo "<hr>";
    echo "<h3>Instructions</h3>";
    echo "<ol>";
    echo "<li>Configurez <code>config/config_mail.php</code> avec vos identifiants SMTP</li>";
    echo "<li>Cliquez sur le bouton ci-dessus pour tester l'envoi</li>";
    echo "<li>Vérifiez la boîte mail: <strong>" . htmlspecialchars($commande['email_client']) . "</strong></li>";
    echo "</ol>";
    
    echo "<hr>";
    echo "<h3>Configuration SMTP recommandée (Gmail)</h3>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> smtp.gmail.com</li>";
    echo "<li><strong>Port:</strong> 587</li>";
    echo "<li><strong>Encryption:</strong> TLS</li>";
    echo "<li><strong>Note:</strong> Utilisez un mot de passe d'application si vous avez l'authentification à 2 facteurs activée</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
