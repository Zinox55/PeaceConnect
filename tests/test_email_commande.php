<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email Commande - PeaceConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .header {
            background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .test-box {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success {
            border-left: 4px solid #28a745;
            background: #d4edda;
        }
        .error {
            border-left: 4px solid #dc3545;
            background: #f8d7da;
        }
        .warning {
            border-left: 4px solid #ffc107;
            background: #fff3cd;
        }
        .info {
            border-left: 4px solid #17a2b8;
            background: #d1ecf1;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #1cc88a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn:hover {
            background: #17a673;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 0.9rem;
        }
        input {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            width: 100%;
            font-size: 1rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-envelope"></i> Test Email de Commande</h1>
        <p>Vérifiez que les emails sont bien envoyés</p>
    </div>

    <?php
    require_once __DIR__ . '/config.php';
    
    $tests = [];
    
    // Test 1 : Vérifier PHPMailer
    $tests['phpmailer'] = [
        'name' => 'PHPMailer installé',
        'status' => file_exists(__DIR__ . '/vendor/autoload.php') ? 'success' : 'error',
        'message' => file_exists(__DIR__ . '/vendor/autoload.php') 
            ? 'PHPMailer est installé' 
            : 'PHPMailer n\'est pas installé. Exécutez: composer require phpmailer/phpmailer'
    ];
    
    // Test 2 : Vérifier la configuration
    $configFile = __DIR__ . '/config/config_mail.php';
    $tests['config'] = [
        'name' => 'Configuration email',
        'status' => file_exists($configFile) ? 'success' : 'error',
        'message' => file_exists($configFile) 
            ? 'Fichier de configuration trouvé' 
            : 'Fichier config/config_mail.php manquant'
    ];
    
    if (file_exists($configFile)) {
        $config = require $configFile;
        $tests['config']['details'] = [
            'SMTP Host' => $config['smtp']['host'],
            'SMTP Port' => $config['smtp']['port'],
            'Username' => $config['smtp']['username'],
            'From Email' => $config['from']['email'],
            'Order Confirmation' => $config['notifications']['order_confirmation_enabled'] ? 'Activé' : 'Désactivé'
        ];
    }
    
    // Test 3 : Vérifier le modèle Mailer
    $tests['mailer'] = [
        'name' => 'Modèle Mailer',
        'status' => file_exists(__DIR__ . '/model/Mailer.php') ? 'success' : 'error',
        'message' => file_exists(__DIR__ . '/model/Mailer.php') 
            ? 'Modèle Mailer trouvé' 
            : 'Fichier model/Mailer.php manquant'
    ];
    
    // Test 4 : Vérifier les dernières commandes
    try {
        $db = config::getConnexion();
        $query = "SELECT id, numero_commande, nom_client, email_client, date_commande 
                  FROM commandes 
                  ORDER BY date_commande DESC 
                  LIMIT 5";
        $stmt = $db->query($query);
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $tests['commandes'] = [
            'name' => 'Dernières commandes',
            'status' => 'info',
            'message' => count($commandes) . ' commande(s) trouvée(s)',
            'data' => $commandes
        ];
    } catch (Exception $e) {
        $tests['commandes'] = [
            'name' => 'Dernières commandes',
            'status' => 'error',
            'message' => 'Erreur: ' . $e->getMessage()
        ];
    }
    
    // Afficher les résultats
    foreach ($tests as $key => $test) {
        echo '<div class="test-box ' . $test['status'] . '">';
        echo '<h3><i class="fas fa-' . ($test['status'] === 'success' ? 'check-circle' : ($test['status'] === 'error' ? 'times-circle' : 'info-circle')) . '"></i> ' . $test['name'] . '</h3>';
        echo '<p>' . $test['message'] . '</p>';
        
        if (isset($test['details'])) {
            echo '<pre>' . print_r($test['details'], true) . '</pre>';
        }
        
        if (isset($test['data'])) {
            echo '<table style="width: 100%; border-collapse: collapse; margin-top: 10px;">';
            echo '<tr style="background: #f8f9fa;"><th style="padding: 8px; text-align: left;">ID</th><th style="padding: 8px; text-align: left;">Numéro</th><th style="padding: 8px; text-align: left;">Client</th><th style="padding: 8px; text-align: left;">Email</th><th style="padding: 8px; text-align: left;">Date</th></tr>';
            foreach ($test['data'] as $cmd) {
                echo '<tr style="border-bottom: 1px solid #eee;">';
                echo '<td style="padding: 8px;">' . $cmd['id'] . '</td>';
                echo '<td style="padding: 8px;">' . $cmd['numero_commande'] . '</td>';
                echo '<td style="padding: 8px;">' . $cmd['nom_client'] . '</td>';
                echo '<td style="padding: 8px;">' . $cmd['email_client'] . '</td>';
                echo '<td style="padding: 8px;">' . date('d/m/Y H:i', strtotime($cmd['date_commande'])) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        
        echo '</div>';
    }
    ?>

    <div class="test-box">
        <h3><i class="fas fa-paper-plane"></i> Envoyer un email de test</h3>
        <form method="POST" action="">
            <label>Email destinataire:</label>
            <input type="email" name="test_email" placeholder="votre@email.com" required>
            
            <label>Numéro de commande (optionnel):</label>
            <input type="text" name="numero_commande" placeholder="CMD-2025-123456">
            
            <button type="submit" name="send_test" class="btn">
                <i class="fas fa-envelope"></i> Envoyer un email de test
            </button>
        </form>
        
        <?php
        if (isset($_POST['send_test'])) {
            try {
                require_once __DIR__ . '/vendor/autoload.php';
                require_once __DIR__ . '/model/Mailer.php';
                
                $mailer = new Mailer();
                $testEmail = $_POST['test_email'];
                
                // Créer des données de test
                $testData = [
                    'commande' => [
                        'id' => 'TEST-001',
                        'numero_commande' => $_POST['numero_commande'] ?: 'CMD-TEST-' . time(),
                        'total' => 99.99,
                        'statut' => 'confirmee',
                        'date_commande' => date('Y-m-d H:i:s')
                    ],
                    'details' => [
                        [
                            'produit_nom' => 'Produit Test 1',
                            'quantite' => 2,
                            'prix_unitaire' => 29.99
                        ],
                        [
                            'produit_nom' => 'Produit Test 2',
                            'quantite' => 1,
                            'prix_unitaire' => 40.01
                        ]
                    ],
                    'client' => [
                        'nom' => 'Client Test',
                        'email' => $testEmail
                    ]
                ];
                
                $result = $mailer->sendTemplate($testEmail, 'order_confirmation', $testData);
                
                if ($result) {
                    echo '<div class="test-box success" style="margin-top: 15px;">';
                    echo '<p><i class="fas fa-check-circle"></i> <strong>Email envoyé avec succès à ' . htmlspecialchars($testEmail) . ' !</strong></p>';
                    echo '<p>Vérifiez votre boîte de réception (et les spams).</p>';
                    echo '</div>';
                } else {
                    echo '<div class="test-box error" style="margin-top: 15px;">';
                    echo '<p><i class="fas fa-times-circle"></i> <strong>Erreur lors de l\'envoi</strong></p>';
                    echo '<p>Vérifiez les logs dans logs/emails_' . date('Y-m') . '.log</p>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div class="test-box error" style="margin-top: 15px;">';
                echo '<p><i class="fas fa-times-circle"></i> <strong>Erreur:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
        }
        ?>
    </div>

    <div class="test-box info">
        <h3><i class="fas fa-info-circle"></i> Comment ça fonctionne ?</h3>
        <ol>
            <li><strong>Lors de la création d'une commande</strong>, un email est automatiquement envoyé au client ET à l'admin</li>
            <li>L'email contient tous les détails de la commande (produits, quantités, prix)</li>
            <li>Le client reçoit son numéro de commande pour le suivi</li>
            <li>Les logs sont enregistrés dans <code>logs/emails_YYYY-MM.log</code></li>
        </ol>
        
        <h4 style="margin-top: 20px;">Configuration Gmail :</h4>
        <ol>
            <li>Activez la vérification en 2 étapes sur votre compte Gmail</li>
            <li>Générez un "Mot de passe d'application" : <a href="https://myaccount.google.com/apppasswords" target="_blank">https://myaccount.google.com/apppasswords</a></li>
            <li>Utilisez ce mot de passe dans <code>config/config_mail.php</code></li>
        </ol>
    </div>

    <div class="test-box">
        <h3><i class="fas fa-link"></i> Liens utiles</h3>
        <a href="view/front/produits.html" class="btn"><i class="fas fa-shopping-bag"></i> Tester une commande</a>
        <a href="view/back/dashboard.html" class="btn"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="docs/MAILING_SETUP.md" class="btn"><i class="fas fa-book"></i> Documentation</a>
    </div>
</body>
</html>
