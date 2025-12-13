<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Vérifier si Composer est installé
$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    echo json_encode([
        'success' => false,
        'message' => 'PHPMailer non installé. Exécutez: composer require phpmailer/phpmailer',
        'install_command' => 'composer require phpmailer/phpmailer'
    ]);
    exit;
}

require_once $autoloadPath;
require_once __DIR__ . '/../model/Mailer.php';
require_once __DIR__ . '/../model/Produit.php';

/**
 * Contrôleur Email - Gestion des envois d'emails
 */
class EmailController {
    private $mailer;
    private $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/config_mail.php';
        
        try {
            $this->mailer = new Mailer();
        } catch (Exception $e) {
            throw new Exception("Erreur initialisation mailer: " . $e->getMessage());
        }
    }
    
    /**
     * Envoyer les alertes de stock par email
     */
    public function sendStockAlerts() {
        try {
            if (!$this->config['notifications']['stock_alert_enabled']) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Les notifications de stock sont désactivées'
                ]);
                return;
            }
            
            $produit = new Produit();
            $seuil = $this->config['notifications']['stock_alert_threshold'];
            $produits = $produit->getProduitsStockFaible($seuil);
            
            if (empty($produits)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Aucune alerte de stock à envoyer',
                    'alerts_count' => 0
                ]);
                return;
            }
            
            $adminEmail = $this->config['admin']['email'];
            $sent = $this->mailer->sendStockAlertToAdmin($produits, $adminEmail);
            
            if ($sent) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Alerte de stock envoyée avec succès',
                    'alerts_count' => count($produits),
                    'sent_to' => $adminEmail
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi de l\'email'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Envoyer email de confirmation de commande
     */
    public function sendOrderConfirmation() {
        try {
            $numero = $_GET['numero'] ?? '';
            
            if (empty($numero)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Numéro de commande requis'
                ]);
                return;
            }
            
            // Récupérer les détails de la commande
            require_once __DIR__ . '/../config.php';
            $db = config::getConnexion();
            
            $query = "SELECT * FROM commandes WHERE numero_commande = :numero";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':numero', $numero);
            $stmt->execute();
            $commande = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$commande) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Commande non trouvée'
                ]);
                return;
            }
            
            // Récupérer les articles
            $queryArticles = "SELECT dc.*, p.nom, p.prix 
                             FROM details_commande dc
                             INNER JOIN produits p ON dc.produit_id = p.id
                             WHERE dc.commande_id = :commande_id";
            $stmtArticles = $db->prepare($queryArticles);
            $stmtArticles->bindParam(':commande_id', $commande['id'], PDO::PARAM_INT);
            $stmtArticles->execute();
            $articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);
            
            // Créer le contenu de l'email
            $emailBody = $this->createOrderConfirmationEmail($commande, $articles);
            
            $sent = $this->mailer->send(
                $commande['email_client'],
                '✅ Confirmation de commande #' . $numero . ' - PeaceConnect',
                $emailBody,
                'Confirmation de votre commande PeaceConnect'
            );
            
            if ($sent) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Email de confirmation envoyé',
                    'sent_to' => $commande['email_client']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi de l\'email'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Créer le contenu HTML de l'email de confirmation
     */
    private function createOrderConfirmationEmail($commande, $articles) {
        $html = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #5F9E7F; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: white; padding: 30px; border: 1px solid #e0e0e0; }
                .order-number { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center; }
                .order-number h2 { color: #5F9E7F; margin: 0; }
                .info-section { margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px; }
                .info-section h3 { color: #5F9E7F; margin-top: 0; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background: #5F9E7F; color: white; padding: 10px; text-align: left; }
                td { padding: 10px; border-bottom: 1px solid #e0e0e0; }
                .total { background: #e8f5e9; padding: 15px; border-radius: 8px; text-align: right; font-size: 1.2rem; font-weight: bold; }
                .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; border-radius: 0 0 8px 8px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✅ Commande Confirmée !</h1>
                    <p>Merci pour votre confiance</p>
                </div>
                
                <div class="content">
                    <p>Bonjour <strong>' . htmlspecialchars($commande['nom_client']) . '</strong>,</p>
                    <p>Nous avons bien reçu votre commande et nous vous en remercions.</p>
                    
                    <div class="order-number">
                        <p style="margin: 0; color: #666;">Numéro de commande</p>
                        <h2>' . htmlspecialchars($commande['numero_commande']) . '</h2>
                    </div>
                    
                    <div class="info-section">
                        <h3>📋 Informations de livraison</h3>
                        <p><strong>Nom:</strong> ' . htmlspecialchars($commande['nom_client']) . '</p>
                        <p><strong>Email:</strong> ' . htmlspecialchars($commande['email_client']) . '</p>
                        <p><strong>Téléphone:</strong> ' . htmlspecialchars($commande['telephone_client'] ?? 'Non renseigné') . '</p>
                        <p><strong>Adresse:</strong><br>' . nl2br(htmlspecialchars($commande['adresse_client'])) . '</p>
                    </div>
                    
                    <h3 style="color: #5F9E7F;">📦 Articles commandés</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th style="text-align: center;">Qté</th>
                                <th style="text-align: right;">Prix</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        foreach ($articles as $article) {
            $sousTotal = $article['prix_unitaire'] * $article['quantite'];
            $html .= '
                            <tr>
                                <td>' . htmlspecialchars($article['nom']) . '</td>
                                <td style="text-align: center;">' . $article['quantite'] . '</td>
                                <td style="text-align: right;">' . number_format($article['prix_unitaire'], 2) . ' €</td>
                                <td style="text-align: right;"><strong>' . number_format($sousTotal, 2) . ' €</strong></td>
                            </tr>';
        }
        
        $html .= '
                        </tbody>
                    </table>
                    
                    <div class="total">
                        <span style="color: #5F9E7F;">TOTAL: ' . number_format($commande['total'], 2) . ' €</span>
                    </div>
                    
                    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #ffc107;">
                        <p style="margin: 0; color: #856404;">
                            <strong>💡 Conseil:</strong> Conservez ce numéro de commande pour suivre l\'état de votre livraison.
                        </p>
                    </div>
                    
                    <div style="margin-top: 30px; text-align: center;">
                        <p>Vous pouvez suivre votre commande en utilisant le numéro ci-dessus.</p>
                        <p style="color: #999; font-size: 0.9rem;">Un email supplémentaire vous sera envoyé lors de l\'expédition.</p>
                    </div>
                </div>
                
                <div class="footer">
                    <p><strong>PeaceConnect</strong><br>
                    43 Rue de la Paix, Paris 75001<br>
                    +33 (0)1 23 45 67 89<br>
                    info@peaceconnect.org</p>
                    <p style="font-size: 0.85rem; color: #999;">© ' . date('Y') . ' PeaceConnect - Tous droits réservés</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Tester la configuration email
     */
    public function testEmail() {
        try {
            $testEmail = $_GET['email'] ?? $this->config['admin']['email'];
            
            $testBody = "
                <h2>Test de configuration email</h2>
                <p>Si vous recevez cet email, la configuration fonctionne correctement!</p>
                <p><strong>Date:</strong> " . date('d/m/Y H:i:s') . "</p>
                <p><strong>Serveur:</strong> {$this->config['smtp']['host']}</p>
            ";
            
            $sent = $this->mailer->send(
                $testEmail,
                '✅ Test Email - PeaceConnect',
                $testBody,
                'Test de configuration email PeaceConnect'
            );
            
            if ($sent) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Email de test envoyé avec succès',
                    'sent_to' => $testEmail
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi du test'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Obtenir la configuration email (sans le mot de passe)
     */
    public function getConfig() {
        $safeConfig = $this->config;
        $safeConfig['smtp']['password'] = '***'; // Masquer le mot de passe
        
        echo json_encode([
            'success' => true,
            'config' => $safeConfig
        ]);
    }
    
    /**
     * Gérer les requêtes
     */
    public function handleRequest() {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'confirmation':
                $this->sendOrderConfirmation();
                break;
            
            case 'send_stock_alerts':
                $this->sendStockAlerts();
                break;
                
            case 'test':
                $this->testEmail();
                break;
                
            case 'config':
                $this->getConfig();
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Action non reconnue',
                    'available_actions' => [
                        'confirmation' => 'Envoyer email de confirmation de commande',
                        'send_stock_alerts' => 'Envoyer les alertes de stock',
                        'test' => 'Tester la configuration email',
                        'config' => 'Obtenir la configuration'
                    ]
                ]);
                break;
        }
    }
}

try {
    $controller = new EmailController();
    $controller->handleRequest();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>
