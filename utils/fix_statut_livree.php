<?php
/**
 * Script de diagnostic et correction pour le statut "livrée"
 * Ouvrez ce fichier dans votre navigateur pour diagnostiquer le problème
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fix Statut Livrée</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #5F9E7F; }
        .test { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .btn { display: inline-block; padding: 10px 20px; background: #5F9E7F; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        .btn:hover { background: #4a7c5f; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #5F9E7F; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Diagnostic : Statut "Livrée"</h1>
        
        <?php
        require_once 'config.php';
        
        try {
            $db = config::getConnexion();
            echo '<div class="test success">✅ Connexion à la base de données réussie</div>';
            
            // Test 1 : Vérifier la structure de la table
            echo '<h2>📋 Test 1 : Structure de la table commandes</h2>';
            $query = "SHOW COLUMNS FROM commandes";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $hasDateLivraison = false;
            $hasStatutLivree = false;
            
            echo '<table>';
            echo '<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Default</th></tr>';
            foreach ($columns as $col) {
                echo '<tr>';
                echo '<td><strong>' . htmlspecialchars($col['Field']) . '</strong></td>';
                echo '<td>' . htmlspecialchars($col['Type']) . '</td>';
                echo '<td>' . htmlspecialchars($col['Null']) . '</td>';
                echo '<td>' . htmlspecialchars($col['Default'] ?? 'NULL') . '</td>';
                echo '</tr>';
                
                if ($col['Field'] === 'date_livraison') {
                    $hasDateLivraison = true;
                }
                if ($col['Field'] === 'statut' && strpos($col['Type'], 'livree') !== false) {
                    $hasStatutLivree = true;
                }
            }
            echo '</table>';
            
            if (!$hasDateLivraison) {
                echo '<div class="test error">❌ La colonne <code>date_livraison</code> n\'existe pas !</div>';
                echo '<div class="test warning">⚠️ Cette colonne est nécessaire pour enregistrer la date de livraison.</div>';
                echo '<h3>Solution :</h3>';
                echo '<pre>ALTER TABLE commandes 
ADD COLUMN date_livraison TIMESTAMP NULL DEFAULT NULL 
AFTER date_commande;</pre>';
                
                if (isset($_GET['fix']) && $_GET['fix'] === 'add_column') {
                    try {
                        $db->exec("ALTER TABLE commandes ADD COLUMN date_livraison TIMESTAMP NULL DEFAULT NULL AFTER date_commande");
                        echo '<div class="test success">✅ Colonne <code>date_livraison</code> ajoutée avec succès !</div>';
                        echo '<meta http-equiv="refresh" content="2">';
                    } catch (Exception $e) {
                        echo '<div class="test error">❌ Erreur : ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                } else {
                    echo '<a href="?fix=add_column" class="btn">🔧 Ajouter la colonne automatiquement</a>';
                }
            } else {
                echo '<div class="test success">✅ La colonne <code>date_livraison</code> existe</div>';
            }
            
            if (!$hasStatutLivree) {
                echo '<div class="test error">❌ Le statut "livree" n\'est pas dans l\'ENUM !</div>';
            } else {
                echo '<div class="test success">✅ Le statut "livree" est disponible</div>';
            }
            
            // Test 2 : Tester le changement de statut
            echo '<h2>🧪 Test 2 : Test de changement de statut</h2>';
            
            // Récupérer une commande de test
            $query = "SELECT id, numero_commande, statut FROM commandes LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $commande = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($commande) {
                echo '<div class="test info">';
                echo '<strong>Commande de test :</strong><br>';
                echo 'ID: ' . $commande['id'] . '<br>';
                echo 'Numéro: ' . $commande['numero_commande'] . '<br>';
                echo 'Statut actuel: <strong>' . $commande['statut'] . '</strong>';
                echo '</div>';
                
                if (isset($_GET['test']) && $_GET['test'] === 'change_statut') {
                    try {
                        $query = "UPDATE commandes SET statut = 'livree', date_livraison = NOW() WHERE id = :id";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':id', $commande['id'], PDO::PARAM_INT);
                        $stmt->execute();
                        
                        echo '<div class="test success">✅ Statut changé à "livree" avec succès !</div>';
                        echo '<meta http-equiv="refresh" content="2">';
                    } catch (Exception $e) {
                        echo '<div class="test error">❌ Erreur : ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                } else {
                    echo '<a href="?test=change_statut" class="btn">🧪 Tester le changement de statut</a>';
                }
            } else {
                echo '<div class="test warning">⚠️ Aucune commande dans la base de données</div>';
            }
            
            // Test 3 : Voir toutes les commandes
            echo '<h2>📊 Test 3 : Liste des commandes</h2>';
            $query = "SELECT id, numero_commande, nom_client, statut, date_commande, date_livraison FROM commandes ORDER BY id DESC LIMIT 10";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($commandes) > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>N° Commande</th><th>Client</th><th>Statut</th><th>Date Commande</th><th>Date Livraison</th></tr>';
                foreach ($commandes as $cmd) {
                    $statutClass = '';
                    switch ($cmd['statut']) {
                        case 'livree': $statutClass = 'style="background: #d4edda; color: #155724;"'; break;
                        case 'confirmee': $statutClass = 'style="background: #d1ecf1; color: #0c5460;"'; break;
                        case 'annulee': $statutClass = 'style="background: #f8d7da; color: #721c24;"'; break;
                        default: $statutClass = 'style="background: #fff3cd; color: #856404;"';
                    }
                    
                    echo '<tr>';
                    echo '<td>' . $cmd['id'] . '</td>';
                    echo '<td>' . htmlspecialchars($cmd['numero_commande']) . '</td>';
                    echo '<td>' . htmlspecialchars($cmd['nom_client']) . '</td>';
                    echo '<td ' . $statutClass . '><strong>' . $cmd['statut'] . '</strong></td>';
                    echo '<td>' . date('d/m/Y H:i', strtotime($cmd['date_commande'])) . '</td>';
                    echo '<td>' . ($cmd['date_livraison'] ? date('d/m/Y H:i', strtotime($cmd['date_livraison'])) : '-') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<div class="test warning">⚠️ Aucune commande trouvée</div>';
            }
            
            // Test 4 : Vérifier le contrôleur
            echo '<h2>🔍 Test 4 : Vérification des fichiers</h2>';
            
            $files = [
                'controller/CommandeController.php' => 'Contrôleur des commandes',
                'model/Commande.php' => 'Modèle Commande',
                'view/back/produits.html' => 'Back office produits',
                'view/back/dashboard.html' => 'Dashboard'
            ];
            
            foreach ($files as $file => $desc) {
                if (file_exists($file)) {
                    echo '<div class="test success">✅ ' . $desc . ' : <code>' . $file . '</code></div>';
                } else {
                    echo '<div class="test error">❌ ' . $desc . ' introuvable : <code>' . $file . '</code></div>';
                }
            }
            
            // Résumé
            echo '<h2>📝 Résumé</h2>';
            if ($hasDateLivraison && $hasStatutLivree) {
                echo '<div class="test success">';
                echo '<h3>✅ Tout semble correct !</h3>';
                echo '<p>La base de données est correctement configurée. Si le problème persiste :</p>';
                echo '<ol>';
                echo '<li>Videz le cache du navigateur (Ctrl+Shift+Delete)</li>';
                echo '<li>Ouvrez la console (F12) et vérifiez les logs</li>';
                echo '<li>Testez le changement de statut ci-dessus</li>';
                echo '<li>Vérifiez les erreurs JavaScript dans la console</li>';
                echo '</ol>';
                echo '</div>';
            } else {
                echo '<div class="test error">';
                echo '<h3>❌ Configuration incomplète</h3>';
                echo '<p>Cliquez sur les boutons ci-dessus pour corriger automatiquement.</p>';
                echo '</div>';
            }
            
        } catch (Exception $e) {
            echo '<div class="test error">❌ Erreur : ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
            <h3>💡 Aide supplémentaire</h3>
            <p><strong>Si le problème persiste après avoir corrigé la base de données :</strong></p>
            <ol>
                <li>Ouvrez le back office</li>
                <li>Appuyez sur <kbd>F12</kbd> pour ouvrir la console</li>
                <li>Cliquez sur "Marquer livrée"</li>
                <li>Regardez les logs qui commencent par 🔄 📤 📥</li>
                <li>Copiez les messages d'erreur</li>
            </ol>
        </div>
    </div>
</body>
</html>
