<?php
/**
 * Script de mise à jour automatique de la base de données
 * Ajoute les champs de paiement à la table commandes
 */

require_once __DIR__ . '/config.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Mise à jour Base de Données - PeaceConnect</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #5F9E7F;
            margin-top: 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #17a2b8;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #ffc107;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #5F9E7F;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #4a7d63;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border: 1px solid #dee2e6;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .step h3 {
            margin-top: 0;
            color: #5F9E7F;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔄 Mise à jour de la Base de Données</h1>
        <p>Ce script va ajouter les champs nécessaires pour le système de paiement.</p>
";

try {
    $db = config::getConnexion();
    
    echo "<div class='success'>✅ Connexion à la base de données réussie</div>";
    
    // Vérifier si les colonnes existent déjà
    $query = "SHOW COLUMNS FROM commandes";
    $stmt = $db->query($query);
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $requiredColumns = [
        'methode_paiement' => "ENUM('card', 'paypal', 'virement') DEFAULT NULL",
        'statut_paiement' => "ENUM('en_attente', 'paye', 'echoue', 'rembourse') DEFAULT 'en_attente'",
        'date_paiement' => "TIMESTAMP NULL DEFAULT NULL",
        'transaction_id' => "VARCHAR(100) NULL DEFAULT NULL"
    ];
    
    $missingColumns = [];
    foreach ($requiredColumns as $col => $def) {
        if (!in_array($col, $columns)) {
            $missingColumns[$col] = $def;
        }
    }
    
    if (empty($missingColumns)) {
        echo "<div class='info'>ℹ️ Toutes les colonnes de paiement sont déjà présentes dans la base de données.</div>";
        echo "<div class='success'>✅ Votre base de données est à jour !</div>";
    } else {
        echo "<div class='warning'>⚠️ Colonnes manquantes détectées : " . implode(', ', array_keys($missingColumns)) . "</div>";
        
        // Ajouter les colonnes manquantes
        $db->beginTransaction();
        
        try {
            foreach ($missingColumns as $col => $def) {
                $alterQuery = "ALTER TABLE commandes ADD COLUMN $col $def";
                
                // Position des colonnes
                if ($col === 'methode_paiement') {
                    $alterQuery .= " AFTER statut";
                } elseif ($col === 'statut_paiement') {
                    $alterQuery .= " AFTER methode_paiement";
                } elseif ($col === 'date_paiement') {
                    $alterQuery .= " AFTER statut_paiement";
                } elseif ($col === 'transaction_id') {
                    $alterQuery .= " AFTER date_paiement";
                }
                
                $db->exec($alterQuery);
                echo "<div class='success'>✅ Colonne '$col' ajoutée avec succès</div>";
            }
            
            // Ajouter les index
            try {
                $db->exec("CREATE INDEX idx_statut_paiement ON commandes(statut_paiement)");
                echo "<div class='success'>✅ Index 'idx_statut_paiement' créé</div>";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate key name') === false) {
                    throw $e;
                }
                echo "<div class='info'>ℹ️ Index 'idx_statut_paiement' existe déjà</div>";
            }
            
            try {
                $db->exec("CREATE INDEX idx_methode_paiement ON commandes(methode_paiement)");
                echo "<div class='success'>✅ Index 'idx_methode_paiement' créé</div>";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate key name') === false) {
                    throw $e;
                }
                echo "<div class='info'>ℹ️ Index 'idx_methode_paiement' existe déjà</div>";
            }
            
            $db->commit();
            echo "<div class='success'><strong>🎉 Mise à jour terminée avec succès !</strong></div>";
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    // Afficher la structure actuelle
    echo "<div class='step'>";
    echo "<h3>📋 Structure actuelle de la table 'commandes'</h3>";
    $query = "DESCRIBE commandes";
    $stmt = $db->query($query);
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    foreach ($structure as $col) {
        $highlight = in_array($col['Field'], array_keys($requiredColumns)) ? ' 👈 PAIEMENT' : '';
        echo sprintf("%-20s %-30s %s\n", $col['Field'], $col['Type'], $highlight);
    }
    echo "</pre>";
    echo "</div>";
    
    // Statistiques
    echo "<div class='step'>";
    echo "<h3>📊 Statistiques</h3>";
    
    $query = "SELECT COUNT(*) as total FROM commandes";
    $stmt = $db->query($query);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Total de commandes : <strong>$total</strong></p>";
    
    $query = "SELECT COUNT(*) as total FROM commandes WHERE methode_paiement IS NOT NULL";
    $stmt = $db->query($query);
    $withPayment = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "<p>Commandes avec méthode de paiement : <strong>$withPayment</strong></p>";
    
    $query = "SELECT statut_paiement, COUNT(*) as count FROM commandes GROUP BY statut_paiement";
    $stmt = $db->query($query);
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($stats)) {
        echo "<p>Répartition par statut de paiement :</p>";
        echo "<ul>";
        foreach ($stats as $stat) {
            $statut = $stat['statut_paiement'] ?? 'Non défini';
            echo "<li>$statut : <strong>{$stat['count']}</strong></li>";
        }
        echo "</ul>";
    }
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div class='error'>";
    echo "<strong>❌ Erreur de base de données :</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
    
    echo "<div class='warning'>";
    echo "<h3>💡 Solution alternative</h3>";
    echo "<p>Vous pouvez exécuter manuellement le script SQL :</p>";
    echo "<pre>mysql -u root -p peaceconnect < sql/add_payment_fields.sql</pre>";
    echo "<p>Ou via phpMyAdmin, exécutez le contenu du fichier <code>sql/add_payment_fields.sql</code></p>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<strong>❌ Erreur :</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "
        <div class='step'>
            <h3>🚀 Prochaines étapes</h3>
            <ol>
                <li>Testez le système de paiement : <a href='tests/test_paiement.php' class='btn'>🧪 Page de test</a></li>
                <li>Consultez la documentation : <a href='docs/PAIEMENT_GUIDE.md' class='btn btn-secondary'>📖 Guide</a></li>
                <li>Essayez une commande : <a href='view/front/produits.html' class='btn btn-secondary'>🛍️ Produits</a></li>
            </ol>
        </div>
        
        <div style='text-align: center; margin-top: 30px;'>
            <a href='view/front/index.html' class='btn'>🏠 Retour à l'accueil</a>
            <a href='view/back/dashboard.html' class='btn btn-secondary'>📊 Back office</a>
        </div>
    </div>
</body>
</html>
";
?>
