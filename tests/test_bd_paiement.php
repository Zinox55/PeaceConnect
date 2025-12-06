<?php
// Test rapide de l'API de commande
require_once __DIR__ . '/config.php';

try {
    $pdo = config::getConnexion();
    echo "✓ Connexion BD réussie\n\n";
    
    // Vérifier la structure
    $result = $pdo->query("DESCRIBE commandes");
    $columns = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Colonnes disponibles :\n";
    echo "  - " . implode("\n  - ", $columns) . "\n\n";
    
    // Vérifier le panier
    $result = $pdo->query("SELECT COUNT(*) as count FROM panier");
    $panierCount = $result->fetch()['count'];
    echo "✓ Articles dans le panier : $panierCount\n\n";
    
    if ($panierCount == 0) {
        echo "⚠️  Le panier est vide. Ajoutez des produits d'abord.\n";
    } else {
        echo "✓ Le système est prêt à créer des commandes !\n";
    }
    
} catch (Exception $e) {
    echo "✗ ERREUR : " . $e->getMessage() . "\n";
}
?>
