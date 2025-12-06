<?php
/**
 * Voir les logs d'envoi d'emails
 */

echo "<h2>📧 Logs d'envoi d'emails</h2>";
echo "<hr>";

$logsDir = __DIR__ . '/logs';
if (!is_dir($logsDir)) {
    echo "<p style='color: orange;'>⚠️ Aucun dossier de logs trouvé</p>";
    exit;
}

// Lister tous les fichiers de logs
$logFiles = glob($logsDir . '/emails_*.log');

if (empty($logFiles)) {
    echo "<p style='color: orange;'>⚠️ Aucun log d'email trouvé</p>";
    exit;
}

// Trier par date (plus récent en premier)
rsort($logFiles);

echo "<p style='color: green;'>✓ " . count($logFiles) . " fichier(s) de logs trouvé(s)</p>";

foreach ($logFiles as $logFile) {
    $filename = basename($logFile);
    $content = file_get_contents($logFile);
    $lines = explode("\n", trim($content));
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>📄 " . htmlspecialchars($filename) . " <span style='color: #666; font-size: 0.9rem;'>(" . count($lines) . " entrées)</span></h3>";
    
    // Afficher les dernières lignes en premier
    $lines = array_reverse($lines);
    
    echo "<div style='background: white; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto; font-family: monospace; font-size: 0.85rem;'>";
    
    foreach ($lines as $line) {
        if (empty(trim($line))) continue;
        
        // Colorier selon le statut
        if (strpos($line, 'SUCCESS') !== false) {
            echo "<div style='color: #28a745; padding: 5px 0; border-bottom: 1px solid #e0e0e0;'>" . htmlspecialchars($line) . "</div>";
        } elseif (strpos($line, 'ERROR') !== false) {
            echo "<div style='color: #dc3545; padding: 5px 0; border-bottom: 1px solid #e0e0e0;'>" . htmlspecialchars($line) . "</div>";
        } else {
            echo "<div style='padding: 5px 0; border-bottom: 1px solid #e0e0e0;'>" . htmlspecialchars($line) . "</div>";
        }
    }
    
    echo "</div>";
    echo "</div>";
}

echo "<hr>";
echo "<p style='text-align: center;'>";
echo "<a href='test_email_direct.php' style='background: #5F9E7F; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px;'>🧪 Tester l'envoi</a>";
echo "<a href='view/front/commande.html' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 5px;'>🛒 Passer commande</a>";
echo "</p>";
?>
