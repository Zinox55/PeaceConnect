<?php
/**
 * Script de test pour diagnostiquer le problème de chargement des détails
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

try {
    $db = config::getConnexion();
    
    // Récupérer le numéro de commande depuis l'URL
    $numero = isset($_GET['numero']) ? trim($_GET['numero']) : '';
    
    if (empty($numero)) {
        echo json_encode([
            'success' => false,
            'message' => 'Numéro de commande manquant',
            'debug' => 'Ajoutez ?numero=CMD-XXX à l\'URL'
        ]);
        exit;
    }
    
    // Étape 1 : Vérifier que la commande existe
    $query = "SELECT * FROM commandes WHERE numero_commande = :numero";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':numero', $numero);
    $stmt->execute();
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$commande) {
        echo json_encode([
            'success' => false,
            'message' => 'Commande non trouvée',
            'numero_recherche' => $numero
        ]);
        exit;
    }
    
    // Étape 2 : Vérifier les colonnes de la table commandes
    $queryColumns = "SHOW COLUMNS FROM commandes";
    $stmtColumns = $db->query($queryColumns);
    $columns = $stmtColumns->fetchAll(PDO::FETCH_COLUMN);
    
    $colonnesPaiement = ['methode_paiement', 'statut_paiement', 'date_paiement', 'transaction_id'];
    $colonnesManquantes = array_diff($colonnesPaiement, $columns);
    
    // Étape 3 : Récupérer les articles
    $queryArticles = "SELECT dc.*, p.nom, p.image, p.description
                      FROM details_commande dc
                      INNER JOIN produits p ON dc.produit_id = p.id
                      WHERE dc.commande_id = :commande_id";
    $stmtArticles = $db->prepare($queryArticles);
    $stmtArticles->bindParam(':commande_id', $commande['id'], PDO::PARAM_INT);
    $stmtArticles->execute();
    $articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);
    
    // Résultat
    echo json_encode([
        'success' => true,
        'message' => 'Test réussi',
        'debug' => [
            'colonnes_table' => $columns,
            'colonnes_paiement_manquantes' => $colonnesManquantes,
            'nombre_articles' => count($articles)
        ],
        'commande' => $commande,
        'articles' => $articles
    ], JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur base de données',
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur',
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
