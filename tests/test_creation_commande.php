<?php
/**
 * Test rapide de création de commande
 */

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

try {
    $db = config::getConnexion();
    
    // Récupérer les articles du panier
    $query = "SELECT p.id as panier_id, pr.id, pr.nom, pr.prix, p.quantite, (pr.prix * p.quantite) as sous_total
              FROM panier p
              INNER JOIN produits pr ON p.produit_id = pr.id";
    $stmt = $db->query($query);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($articles)) {
        echo json_encode([
            'success' => false,
            'message' => 'Panier vide. Ajoutez des produits d\'abord.',
            'redirect' => 'view/front/produits.html'
        ]);
        exit;
    }
    
    // Calculer le total
    $total = array_sum(array_column($articles, 'sous_total'));
    
    // Données de test
    $testData = [
        'client' => [
            'nom' => 'Dhia Eddin Hamdouni',
            'email' => 'hamdounidhiaeddine@gmail.com',
            'telephone' => '0612345612',
            'adresse' => '123 Rue de Test, Paris'
        ],
        'articles' => $articles,
        'total' => $total,
        'methode_paiement' => 'paypal'
    ];
    
    echo "<h1>Test Données Paiement</h1>";
    echo "<h2>Données du panier:</h2>";
    echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    
    echo "<h2>Test de la requête SQL:</h2>";
    
    // Tester la création
    $db->beginTransaction();
    
    $numeroCommande = 'TEST-' . date('YmdHis');
    $methodePaiement = 'paypal';
    $statutPaiement = 'paye';
    $transactionId = 'TEST-' . uniqid();
    $paymentIntentId = null;
    $paymentMethodDetails = json_encode(['test' => true]);
    $statut = 'confirmee';
    $datePaiement = date('Y-m-d H:i:s');
    
    $queryCommande = "INSERT INTO commandes 
        (numero_commande, nom_client, email_client, telephone_client, adresse_client, 
         total, statut, methode_paiement, statut_paiement, date_paiement, 
         transaction_id, payment_intent_id, payment_method_details)
        VALUES 
        (:numero, :nom, :email, :telephone, :adresse, 
         :total, :statut, :methode, :statut_paiement, :date_paiement,
         :transaction_id, :payment_intent_id, :payment_method_details)";
    
    $stmtCommande = $db->prepare($queryCommande);
    $stmtCommande->bindParam(':numero', $numeroCommande);
    $stmtCommande->bindParam(':nom', $testData['client']['nom']);
    $stmtCommande->bindParam(':email', $testData['client']['email']);
    $stmtCommande->bindParam(':telephone', $testData['client']['telephone']);
    $stmtCommande->bindParam(':adresse', $testData['client']['adresse']);
    $stmtCommande->bindParam(':total', $total);
    $stmtCommande->bindParam(':statut', $statut);
    $stmtCommande->bindParam(':methode', $methodePaiement);
    $stmtCommande->bindParam(':statut_paiement', $statutPaiement);
    $stmtCommande->bindParam(':date_paiement', $datePaiement);
    $stmtCommande->bindParam(':transaction_id', $transactionId);
    $stmtCommande->bindParam(':payment_intent_id', $paymentIntentId);
    $stmtCommande->bindParam(':payment_method_details', $paymentMethodDetails);
    
    if ($stmtCommande->execute()) {
        $commandeId = $db->lastInsertId();
        echo "<p style='color: green;'>✓ Commande créée avec ID: $commandeId</p>";
        
        // Ajouter les détails
        $queryDetails = "INSERT INTO details_commande 
            (commande_id, produit_id, quantite, prix_unitaire)
            VALUES (:commande_id, :produit_id, :quantite, :prix)";
        
        $stmtDetails = $db->prepare($queryDetails);
        
        foreach ($articles as $article) {
            $stmtDetails->bindParam(':commande_id', $commandeId, PDO::PARAM_INT);
            $stmtDetails->bindParam(':produit_id', $article['id'], PDO::PARAM_INT);
            $stmtDetails->bindParam(':quantite', $article['quantite'], PDO::PARAM_INT);
            $stmtDetails->bindParam(':prix', $article['prix']);
            
            if ($stmtDetails->execute()) {
                echo "<p style='color: green;'>✓ Article ajouté: {$article['nom']}</p>";
            } else {
                throw new Exception("Erreur ajout article: " . implode(', ', $stmtDetails->errorInfo()));
            }
        }
        
        // Test de mise à jour du stock
        echo "<h3>Test mise à jour stock:</h3>";
        foreach ($articles as $article) {
            $queryStock = "UPDATE produits SET stock = stock - :quantite WHERE id = :id AND stock >= :quantite_check";
            $stmtStock = $db->prepare($queryStock);
            $stmtStock->bindParam(':quantite', $article['quantite'], PDO::PARAM_INT);
            $stmtStock->bindParam(':quantite_check', $article['quantite'], PDO::PARAM_INT);
            $stmtStock->bindParam(':id', $article['id'], PDO::PARAM_INT);
            
            if ($stmtStock->execute()) {
                $affected = $stmtStock->rowCount();
                if ($affected > 0) {
                    echo "<p style='color: green;'>✓ Stock mis à jour pour: {$article['nom']} (-{$article['quantite']})</p>";
                } else {
                    echo "<p style='color: red;'>✗ Stock insuffisant pour: {$article['nom']}</p>";
                }
            } else {
                throw new Exception("Erreur MAJ stock: " . implode(', ', $stmtStock->errorInfo()));
            }
        }
        
        echo "<h3>Résultat:</h3>";
        echo "<pre style='background: #d4edda; padding: 20px; border-radius: 8px;'>";
        echo json_encode([
            'success' => true,
            'message' => 'Test réussi !',
            'numero_commande' => $numeroCommande,
            'transaction_id' => $transactionId,
            'commande_id' => $commandeId,
            'total' => $total
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "</pre>";
        
        echo "<p><strong>IMPORTANT:</strong> Transaction annulée (ROLLBACK) - aucune donnée sauvegardée.</p>";
    } else {
        throw new Exception("Erreur création commande: " . implode(', ', $stmtCommande->errorInfo()));
    }
    
    // Annuler pour ne pas polluer la base
    $db->rollBack();
    echo "<p style='color: blue;'>✓ Transaction annulée (test uniquement)</p>";
    
    echo "<hr>";
    echo "<h2>Actions:</h2>";
    echo "<a href='view/front/commande.html' style='padding: 10px 20px; background: #5F9E7F; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px;'>Tester avec l'interface</a>";
    echo "<a href='verif_paiement.php' style='padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px;'>Vérifier la base</a>";
    
} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "<h1 style='color: red;'>Erreur</h1>";
    echo "<pre style='background: #f8d7da; padding: 20px; border-radius: 8px;'>";
    echo htmlspecialchars($e->getMessage());
    echo "</pre>";
    echo "<p><strong>Fichier:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Ligne:</strong> " . $e->getLine() . "</p>";
}
?>
