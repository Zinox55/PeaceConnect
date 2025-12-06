<?php
/**
 * Script de vérification du système de paiement
 */

require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Système Paiement</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .box {
            background: white;
            padding: 25px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        h1 { color: #5F9E7F; }
        h2 { 
            color: #5F9E7F; 
            border-bottom: 2px solid #5F9E7F;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #5F9E7F;
            color: white;
        }
        tr:hover {
            background: #f9fdfb;
        }
        code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-paye { background: #d4edda; color: #155724; }
        .status-attente { background: #fff3cd; color: #856404; }
        .status-echoue { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>🔍 Vérification du Système de Paiement</h1>
    
    <?php
    try {
        $db = config::getConnexion();
        echo '<div class="box"><p class="success">✓ Connexion base de données réussie</p></div>';
        
        // 1. Vérifier la structure de la table commandes
        echo '<div class="box">';
        echo '<h2>1. Structure de la table commandes</h2>';
        $query = "DESCRIBE commandes";
        $stmt = $db->query($query);
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo '<table>';
        echo '<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>';
        foreach ($columns as $col) {
            echo '<tr>';
            echo '<td><code>' . htmlspecialchars($col['Field']) . '</code></td>';
            echo '<td>' . htmlspecialchars($col['Type']) . '</td>';
            echo '<td>' . htmlspecialchars($col['Null']) . '</td>';
            echo '<td>' . htmlspecialchars($col['Key']) . '</td>';
            echo '<td>' . htmlspecialchars($col['Default'] ?? 'NULL') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        
        // Vérifier les colonnes de paiement
        $requiredColumns = ['methode_paiement', 'statut_paiement', 'date_paiement', 'transaction_id', 'payment_intent_id', 'payment_method_details'];
        $existingColumns = array_column($columns, 'Field');
        $missingColumns = array_diff($requiredColumns, $existingColumns);
        
        if (empty($missingColumns)) {
            echo '<p class="success">✓ Toutes les colonnes de paiement sont présentes</p>';
        } else {
            echo '<p class="error">✗ Colonnes manquantes: ' . implode(', ', $missingColumns) . '</p>';
        }
        echo '</div>';
        
        // 2. Vérifier les commandes existantes
        echo '<div class="box">';
        echo '<h2>2. Commandes avec paiement</h2>';
        $query = "SELECT 
                    numero_commande, 
                    nom_client, 
                    total, 
                    methode_paiement, 
                    statut_paiement, 
                    transaction_id,
                    DATE_FORMAT(date_paiement, '%d/%m/%Y %H:%i') as date_paiement,
                    statut
                  FROM commandes 
                  ORDER BY id DESC 
                  LIMIT 10";
        $stmt = $db->query($query);
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($commandes) > 0) {
            echo '<p>Dernières commandes :</p>';
            echo '<table>';
            echo '<tr>
                    <th>N° Commande</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Méthode</th>
                    <th>Statut Paiement</th>
                    <th>Transaction ID</th>
                    <th>Date</th>
                  </tr>';
            foreach ($commandes as $cmd) {
                $statusClass = '';
                switch ($cmd['statut_paiement']) {
                    case 'paye': $statusClass = 'status-paye'; break;
                    case 'en_attente': $statusClass = 'status-attente'; break;
                    case 'echoue': $statusClass = 'status-echoue'; break;
                }
                
                echo '<tr>';
                echo '<td><strong>' . htmlspecialchars($cmd['numero_commande']) . '</strong></td>';
                echo '<td>' . htmlspecialchars($cmd['nom_client']) . '</td>';
                echo '<td>' . number_format($cmd['total'], 2) . ' €</td>';
                echo '<td>' . htmlspecialchars($cmd['methode_paiement'] ?? '-') . '</td>';
                echo '<td><span class="status-badge ' . $statusClass . '">' . htmlspecialchars($cmd['statut_paiement']) . '</span></td>';
                echo '<td><small>' . htmlspecialchars($cmd['transaction_id'] ?? '-') . '</small></td>';
                echo '<td>' . htmlspecialchars($cmd['date_paiement'] ?? '-') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p class="warning">⚠ Aucune commande trouvée</p>';
        }
        echo '</div>';
        
        // 3. Statistiques
        echo '<div class="box">';
        echo '<h2>3. Statistiques</h2>';
        
        $stats = [];
        
        // Total commandes
        $query = "SELECT COUNT(*) as total FROM commandes";
        $result = $db->query($query)->fetch();
        $stats['total_commandes'] = $result['total'];
        
        // Commandes payées
        $query = "SELECT COUNT(*) as total FROM commandes WHERE statut_paiement = 'paye'";
        $result = $db->query($query)->fetch();
        $stats['commandes_payees'] = $result['total'];
        
        // Commandes en attente
        $query = "SELECT COUNT(*) as total FROM commandes WHERE statut_paiement = 'en_attente'";
        $result = $db->query($query)->fetch();
        $stats['commandes_attente'] = $result['total'];
        
        // Méthodes de paiement
        $query = "SELECT methode_paiement, COUNT(*) as nb, SUM(total) as montant 
                  FROM commandes 
                  WHERE methode_paiement IS NOT NULL 
                  GROUP BY methode_paiement";
        $stmt = $db->query($query);
        $methodes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo '<table>';
        echo '<tr><th>Statistique</th><th>Valeur</th></tr>';
        echo '<tr><td>Total commandes</td><td><strong>' . $stats['total_commandes'] . '</strong></td></tr>';
        echo '<tr><td>Commandes payées</td><td class="success">' . $stats['commandes_payees'] . '</td></tr>';
        echo '<tr><td>Commandes en attente</td><td class="warning">' . $stats['commandes_attente'] . '</td></tr>';
        echo '</table>';
        
        if (count($methodes) > 0) {
            echo '<h3>Répartition par méthode de paiement :</h3>';
            echo '<table>';
            echo '<tr><th>Méthode</th><th>Nombre</th><th>Montant total</th></tr>';
            foreach ($methodes as $m) {
                echo '<tr>';
                echo '<td><strong>' . htmlspecialchars($m['methode_paiement']) . '</strong></td>';
                echo '<td>' . $m['nb'] . '</td>';
                echo '<td>' . number_format($m['montant'], 2) . ' €</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
        
        // 4. Vérifier le panier
        echo '<div class="box">';
        echo '<h2>4. État du panier</h2>';
        $query = "SELECT COUNT(*) as nb_articles, 
                         SUM(p.quantite) as quantite_totale 
                  FROM panier p";
        $result = $db->query($query)->fetch();
        
        echo '<table>';
        echo '<tr><th>Métrique</th><th>Valeur</th></tr>';
        echo '<tr><td>Articles différents</td><td>' . $result['nb_articles'] . '</td></tr>';
        echo '<tr><td>Quantité totale</td><td>' . ($result['quantite_totale'] ?? 0) . '</td></tr>';
        echo '</table>';
        
        if ($result['nb_articles'] > 0) {
            $query = "SELECT pr.nom, p.quantite, pr.prix, (pr.prix * p.quantite) as sous_total
                      FROM panier p
                      JOIN produits pr ON p.produit_id = pr.id";
            $stmt = $db->query($query);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<h3>Contenu du panier :</h3>';
            echo '<table>';
            echo '<tr><th>Produit</th><th>Quantité</th><th>Prix unitaire</th><th>Sous-total</th></tr>';
            foreach ($items as $item) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($item['nom']) . '</td>';
                echo '<td>' . $item['quantite'] . '</td>';
                echo '<td>' . number_format($item['prix'], 2) . ' €</td>';
                echo '<td><strong>' . number_format($item['sous_total'], 2) . ' €</strong></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '</div>';
        
        // 5. Test des contrôleurs
        echo '<div class="box">';
        echo '<h2>5. Test des contrôleurs</h2>';
        echo '<p><a href="tests/test_paiement_complet_v2.html" target="_blank" style="color: #5F9E7F; font-weight: bold;">→ Ouvrir la page de test interactive</a></p>';
        echo '<p><a href="view/front/commande.html" target="_blank" style="color: #5F9E7F; font-weight: bold;">→ Ouvrir la page de commande</a></p>';
        echo '</div>';
        
    } catch (Exception $e) {
        echo '<div class="box">';
        echo '<p class="error">✗ Erreur: ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
    ?>
    
    <div class="box" style="background: #e8f5e9; border-left: 4px solid #5F9E7F;">
        <h2>✅ Actions à faire</h2>
        <ol>
            <li>Vérifiez que toutes les colonnes sont présentes dans la base</li>
            <li>Ajoutez des articles au panier via <a href="view/front/produits.html">produits.html</a></li>
            <li>Testez le paiement via <a href="view/front/commande.html">commande.html</a></li>
            <li>Ou utilisez la <a href="tests/test_paiement_complet_v2.html">page de test automatique</a></li>
        </ol>
    </div>
</body>
</html>
