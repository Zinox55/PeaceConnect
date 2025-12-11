<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Système de Paiement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .test-section h2 {
            color: #5F9E7F;
            margin-top: 0;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .info {
            color: #17a2b8;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #5F9E7F;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #5F9E7F;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn:hover {
            background: #4a7d63;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🧪 Test du Système de Paiement</h1>
    
    <?php
    require_once __DIR__ . '/../config.php';
    
    $db = config::getConnexion();
    $tests = [];
    
    // Test 1 : Vérifier la connexion à la base de données
    $tests['db_connection'] = [
        'name' => 'Connexion à la base de données',
        'status' => $db ? 'success' : 'error',
        'message' => $db ? 'Connexion réussie' : 'Échec de connexion'
    ];
    
    // Test 2 : Vérifier l'existence des colonnes de paiement
    try {
        $query = "SHOW COLUMNS FROM commandes";
        $stmt = $db->query($query);
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $requiredColumns = ['methode_paiement', 'statut_paiement', 'date_paiement', 'transaction_id'];
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (empty($missingColumns)) {
            $tests['db_columns'] = [
                'name' => 'Colonnes de paiement',
                'status' => 'success',
                'message' => 'Toutes les colonnes sont présentes',
                'details' => $requiredColumns
            ];
        } else {
            $tests['db_columns'] = [
                'name' => 'Colonnes de paiement',
                'status' => 'error',
                'message' => 'Colonnes manquantes : ' . implode(', ', $missingColumns),
                'details' => $missingColumns
            ];
        }
    } catch (Exception $e) {
        $tests['db_columns'] = [
            'name' => 'Colonnes de paiement',
            'status' => 'error',
            'message' => 'Erreur : ' . $e->getMessage()
        ];
    }
    
    // Test 3 : Vérifier l'existence des fichiers
    $requiredFiles = [
        'controller/PaiementController.php',
        'view/front/paiement.html',
        'view/front/confirmation.html',
        'view/assets/js/paiement.js',
        'sql/add_payment_fields.sql'
    ];
    
    $missingFiles = [];
    foreach ($requiredFiles as $file) {
        if (!file_exists(__DIR__ . '/../' . $file)) {
            $missingFiles[] = $file;
        }
    }
    
    if (empty($missingFiles)) {
        $tests['files'] = [
            'name' => 'Fichiers du système',
            'status' => 'success',
            'message' => 'Tous les fichiers sont présents',
            'details' => $requiredFiles
        ];
    } else {
        $tests['files'] = [
            'name' => 'Fichiers du système',
            'status' => 'error',
            'message' => 'Fichiers manquants : ' . implode(', ', $missingFiles),
            'details' => $missingFiles
        ];
    }
    
    // Test 4 : Vérifier les commandes existantes
    try {
        $query = "SELECT COUNT(*) as total FROM commandes";
        $stmt = $db->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $tests['commandes'] = [
            'name' => 'Commandes dans la base',
            'status' => 'info',
            'message' => $result['total'] . ' commande(s) trouvée(s)'
        ];
    } catch (Exception $e) {
        $tests['commandes'] = [
            'name' => 'Commandes dans la base',
            'status' => 'error',
            'message' => 'Erreur : ' . $e->getMessage()
        ];
    }
    
    // Test 5 : Vérifier les commandes avec paiement
    try {
        $query = "SELECT COUNT(*) as total FROM commandes WHERE methode_paiement IS NOT NULL";
        $stmt = $db->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $tests['commandes_paiement'] = [
            'name' => 'Commandes avec paiement',
            'status' => 'info',
            'message' => $result['total'] . ' commande(s) avec méthode de paiement'
        ];
    } catch (Exception $e) {
        $tests['commandes_paiement'] = [
            'name' => 'Commandes avec paiement',
            'status' => 'error',
            'message' => 'Erreur : ' . $e->getMessage()
        ];
    }
    
    // Test 6 : Test API PaiementController
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/../controller/PaiementController.php';
    $tests['api'] = [
        'name' => 'API PaiementController',
        'status' => 'info',
        'message' => 'URL : ' . $apiUrl
    ];
    
    // Afficher les résultats
    foreach ($tests as $key => $test) {
        echo '<div class="test-section">';
        echo '<h2>' . $test['name'] . '</h2>';
        echo '<p class="' . $test['status'] . '">' . strtoupper($test['status']) . ': ' . $test['message'] . '</p>';
        
        if (isset($test['details'])) {
            echo '<pre>' . print_r($test['details'], true) . '</pre>';
        }
        echo '</div>';
    }
    
    // Afficher les dernières commandes
    try {
        $query = "SELECT id, numero_commande, nom_client, total, statut, methode_paiement, statut_paiement, date_commande 
                  FROM commandes 
                  ORDER BY date_commande DESC 
                  LIMIT 10";
        $stmt = $db->query($query);
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($commandes)) {
            echo '<div class="test-section">';
            echo '<h2>Dernières Commandes</h2>';
            echo '<table>';
            echo '<tr>
                    <th>ID</th>
                    <th>Numéro</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Méthode Paiement</th>
                    <th>Statut Paiement</th>
                    <th>Date</th>
                  </tr>';
            
            foreach ($commandes as $cmd) {
                echo '<tr>';
                echo '<td>' . $cmd['id'] . '</td>';
                echo '<td>' . $cmd['numero_commande'] . '</td>';
                echo '<td>' . $cmd['nom_client'] . '</td>';
                echo '<td>' . number_format($cmd['total'], 2) . ' €</td>';
                echo '<td>' . $cmd['statut'] . '</td>';
                echo '<td>' . ($cmd['methode_paiement'] ?? '-') . '</td>';
                echo '<td>' . ($cmd['statut_paiement'] ?? '-') . '</td>';
                echo '<td>' . date('d/m/Y H:i', strtotime($cmd['date_commande'])) . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            echo '</div>';
        }
    } catch (Exception $e) {
        echo '<div class="test-section">';
        echo '<h2>Dernières Commandes</h2>';
        echo '<p class="error">Erreur : ' . $e->getMessage() . '</p>';
        echo '</div>';
    }
    ?>
    
    <div class="test-section">
        <h2>Actions</h2>
        <a href="../view/front/produits.html" class="btn">🛍️ Voir les produits</a>
        <a href="../view/front/panier.html" class="btn">🛒 Voir le panier</a>
        <a href="../view/front/commande.html" class="btn">📝 Formulaire commande</a>
        <a href="../view/front/paiement.html" class="btn">💳 Page de paiement</a>
        <a href="../view/back/commandes.html" class="btn">📊 Back office</a>
    </div>
    
    <div class="test-section">
        <h2>Installation</h2>
        <p>Si des colonnes sont manquantes, exécutez le script SQL :</p>
        <pre>mysql -u root -p peaceconnect < sql/add_payment_fields.sql</pre>
        <p>Ou via phpMyAdmin, exécutez le contenu de <code>sql/add_payment_fields.sql</code></p>
    </div>
    
    <div class="test-section">
        <h2>Documentation</h2>
        <ul>
            <li><a href="../docs/PAIEMENT_GUIDE.md">Guide complet du paiement</a></li>
            <li><a href="../INSTALLATION_PAIEMENT.md">Guide d'installation</a></li>
            <li><a href="../docs/README.md">Documentation générale</a></li>
        </ul>
    </div>
</body>
</html>
