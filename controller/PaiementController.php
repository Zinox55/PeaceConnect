<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config.php';

/**
 * Contrôleur Paiement
 * Support pour Stripe, PayPal et virement bancaire
 */
class PaiementController {
    private $db;
    private $stripeSecretKey;
    private $paypalClientId;
    private $paypalClientSecret;
    
    public function __construct() {
        $this->db = config::getConnexion();
        
        // Charger la configuration de paiement
        if (file_exists(__DIR__ . '/../config/config_paiement.php')) {
            $configPaiement = require __DIR__ . '/../config/config_paiement.php';
            $this->stripeSecretKey = $configPaiement['stripe']['secret_key'] ?? '';
            $this->paypalClientId = $configPaiement['paypal']['client_id'] ?? '';
            $this->paypalClientSecret = $configPaiement['paypal']['client_secret'] ?? '';
        }
    }
    
    /**
     * Créer une session de paiement Stripe
     */
    public function creerSessionStripe() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['numero_commande'])) {
                echo json_encode(['success' => false, 'message' => 'Numéro de commande requis']);
                return;
            }
            
            // Récupérer les infos de la commande
            $query = "SELECT * FROM commandes WHERE numero_commande = :numero";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':numero', $data['numero_commande']);
            $stmt->execute();
            $commande = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$commande) {
                echo json_encode(['success' => false, 'message' => 'Commande non trouvée']);
                return;
            }
            
            // Simuler la création d'une session Stripe
            // En production, utilisez la bibliothèque Stripe PHP
            $sessionId = 'cs_test_' . bin2hex(random_bytes(16));
            
            echo json_encode([
                'success' => true,
                'session_id' => $sessionId,
                'message' => 'Session Stripe créée'
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Créer un paiement PayPal
     */
    public function creerPaiementPayPal() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['numero_commande'])) {
                echo json_encode(['success' => false, 'message' => 'Numéro de commande requis']);
                return;
            }
            
            // Récupérer les infos de la commande
            $query = "SELECT * FROM commandes WHERE numero_commande = :numero";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':numero', $data['numero_commande']);
            $stmt->execute();
            $commande = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$commande) {
                echo json_encode(['success' => false, 'message' => 'Commande non trouvée']);
                return;
            }
            
            // En production, créez une commande PayPal via l'API
            $orderId = 'PAYPAL' . strtoupper(bin2hex(random_bytes(8)));
            
            echo json_encode([
                'success' => true,
                'order_id' => $orderId,
                'message' => 'Commande PayPal créée'
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Créer une commande avec paiement
     */
    public function creerCommandeAvecPaiement() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            // Validation des données
            if (!isset($data['client']) || !isset($data['articles']) || !isset($data['total']) || !isset($data['methode_paiement'])) {
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                return;
            }
            
            $client = $data['client'];
            $articles = $data['articles'];
            $total = $data['total'];
            $methodePaiement = $data['methode_paiement'];
            
            // Valider les données client
            if (empty($client['nom']) || empty($client['email']) || empty($client['adresse'])) {
                echo json_encode(['success' => false, 'message' => 'Informations client incomplètes']);
                return;
            }
            
            // Valider la méthode de paiement
            $methodesValides = ['card', 'paypal', 'virement', 'stripe'];
            if (!in_array($methodePaiement, $methodesValides)) {
                echo json_encode(['success' => false, 'message' => 'Méthode de paiement invalide']);
                return;
            }
            
            // Générer un numéro de commande unique
            $numeroCommande = 'CMD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            
            // Transaction ID selon la méthode
            $transactionId = null;
            $paymentIntentId = null;
            $statutPaiement = 'en_attente';
            
            switch ($methodePaiement) {
                case 'card':
                    $transactionId = 'CARD-' . strtoupper(bin2hex(random_bytes(8)));
                    $statutPaiement = 'paye';
                    break;
                case 'stripe':
                    $paymentIntentId = 'pi_' . bin2hex(random_bytes(12));
                    $transactionId = 'STRIPE-' . strtoupper(bin2hex(random_bytes(8)));
                    $statutPaiement = 'paye';
                    break;
                case 'paypal':
                    $transactionId = 'PAYPAL-' . strtoupper(bin2hex(random_bytes(8)));
                    $statutPaiement = 'paye';
                    break;
                case 'virement':
                    $transactionId = 'VIREMENT-' . strtoupper(bin2hex(random_bytes(8)));
                    $statutPaiement = 'en_attente';
                    break;
            }
            
            // Détails de la méthode de paiement
            $paymentMethodDetails = json_encode([
                'method' => $methodePaiement,
                'timestamp' => date('Y-m-d H:i:s'),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            
            // Début de la transaction
            $this->db->beginTransaction();
            
            // 1. Créer la commande
            $queryCommande = "INSERT INTO commandes 
                (numero_commande, nom_client, email_client, telephone_client, adresse_client, 
                 total, statut, methode_paiement, statut_paiement, date_paiement, 
                 transaction_id, payment_intent_id, payment_method_details)
                VALUES 
                (:numero, :nom, :email, :telephone, :adresse, 
                 :total, :statut, :methode, :statut_paiement, :date_paiement,
                 :transaction_id, :payment_intent_id, :payment_method_details)";
            
            $stmtCommande = $this->db->prepare($queryCommande);
            $statut = ($statutPaiement === 'paye') ? 'confirmee' : 'en_attente';
            $datePaiement = ($statutPaiement === 'paye') ? date('Y-m-d H:i:s') : null;
            
            $stmtCommande->bindParam(':numero', $numeroCommande);
            $stmtCommande->bindParam(':nom', $client['nom']);
            $stmtCommande->bindParam(':email', $client['email']);
            $stmtCommande->bindParam(':telephone', $client['telephone']);
            $stmtCommande->bindParam(':adresse', $client['adresse']);
            $stmtCommande->bindParam(':total', $total);
            $stmtCommande->bindParam(':statut', $statut);
            $stmtCommande->bindParam(':methode', $methodePaiement);
            $stmtCommande->bindParam(':statut_paiement', $statutPaiement);
            $stmtCommande->bindParam(':date_paiement', $datePaiement);
            $stmtCommande->bindParam(':transaction_id', $transactionId);
            $stmtCommande->bindParam(':payment_intent_id', $paymentIntentId);
            $stmtCommande->bindParam(':payment_method_details', $paymentMethodDetails);
            
            if (!$stmtCommande->execute()) {
                throw new Exception('Erreur lors de la création de la commande');
            }
            
            $commandeId = $this->db->lastInsertId();
            
            // 2. Ajouter les détails de commande
            $queryDetails = "INSERT INTO details_commande 
                (commande_id, produit_id, quantite, prix_unitaire)
                VALUES (:commande_id, :produit_id, :quantite, :prix)";
            
            $stmtDetails = $this->db->prepare($queryDetails);
            
            foreach ($articles as $article) {
                $stmtDetails->bindParam(':commande_id', $commandeId);
                $stmtDetails->bindParam(':produit_id', $article['id']);
                $stmtDetails->bindParam(':quantite', $article['quantite']);
                $stmtDetails->bindParam(':prix', $article['prix']);
                
                if (!$stmtDetails->execute()) {
                    throw new Exception('Erreur lors de l\'ajout des articles');
                }
                
                // 3. Mettre à jour le stock
                $queryStock = "UPDATE produits SET stock = stock - :quantite WHERE id = :id AND stock >= :quantite_check";
                $stmtStock = $this->db->prepare($queryStock);
                $stmtStock->bindParam(':quantite', $article['quantite'], PDO::PARAM_INT);
                $stmtStock->bindParam(':quantite_check', $article['quantite'], PDO::PARAM_INT);
                $stmtStock->bindParam(':id', $article['id'], PDO::PARAM_INT);
                
                if (!$stmtStock->execute() || $stmtStock->rowCount() === 0) {
                    throw new Exception('Stock insuffisant pour ' . ($article['nom'] ?? 'le produit'));
                }
            }
            
            // 4. Vider le panier
            $queryVidePanier = "DELETE FROM panier";
            $this->db->exec($queryVidePanier);
            
            // Valider la transaction
            $this->db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'numero_commande' => $numeroCommande,
                'transaction_id' => $transactionId,
                'statut_paiement' => $statutPaiement,
                'commande_id' => $commandeId
            ]);
            
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Confirmer un paiement
     */
    public function confirmerPaiement() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['numero_commande']) || !isset($data['methode_paiement'])) {
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                return;
            }
            
            $numeroCommande = $data['numero_commande'];
            $methodePaiement = $data['methode_paiement'];
            $transactionId = $data['transaction_id'] ?? null;
            $paymentIntentId = $data['payment_intent_id'] ?? null;
            $statutPaiement = $data['statut_paiement'] ?? 'paye';
            $paymentMethodDetails = isset($data['payment_method_details']) ? 
                json_encode($data['payment_method_details']) : null;
            
            // Valider la méthode de paiement
            $methodesValides = ['card', 'paypal', 'virement', 'stripe'];
            if (!in_array($methodePaiement, $methodesValides)) {
                echo json_encode(['success' => false, 'message' => 'Méthode de paiement invalide']);
                return;
            }
            
            // Vérifier que la commande existe
            $checkQuery = "SELECT id FROM commandes WHERE numero_commande = :numero";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->bindParam(':numero', $numeroCommande);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() === 0) {
                echo json_encode(['success' => false, 'message' => 'Commande non trouvée']);
                return;
            }
            
            // Mettre à jour la commande
            $query = "UPDATE commandes 
                      SET methode_paiement = :methode,
                          statut_paiement = :statut_paiement,
                          date_paiement = NOW(),
                          transaction_id = :transaction_id,
                          payment_intent_id = :payment_intent_id,
                          payment_method_details = :payment_method_details,
                          statut = CASE 
                              WHEN :statut_paiement_check = 'paye' THEN 'confirmee'
                              ELSE statut
                          END
                      WHERE numero_commande = :numero";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':methode', $methodePaiement);
            $stmt->bindParam(':statut_paiement', $statutPaiement);
            $stmt->bindParam(':statut_paiement_check', $statutPaiement);
            $stmt->bindParam(':transaction_id', $transactionId);
            $stmt->bindParam(':payment_intent_id', $paymentIntentId);
            $stmt->bindParam(':payment_method_details', $paymentMethodDetails);
            $stmt->bindParam(':numero', $numeroCommande);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Paiement confirmé',
                    'transaction_id' => $transactionId
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Obtenir les informations de paiement d'une commande
     */
    public function getInfosPaiement() {
        try {
            $numeroCommande = isset($_GET['numero']) ? trim($_GET['numero']) : '';
            
            if (empty($numeroCommande)) {
                echo json_encode(['success' => false, 'message' => 'Numéro de commande requis']);
                return;
            }
            
            $query = "SELECT methode_paiement, statut_paiement, date_paiement, transaction_id
                      FROM commandes
                      WHERE numero_commande = :numero";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':numero', $numeroCommande);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Commande non trouvée']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Rembourser un paiement
     */
    public function rembourser() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['numero_commande'])) {
                echo json_encode(['success' => false, 'message' => 'Numéro de commande requis']);
                return;
            }
            
            $numeroCommande = $data['numero_commande'];
            
            $query = "UPDATE commandes 
                      SET statut_paiement = 'rembourse',
                          statut = 'annulee'
                      WHERE numero_commande = :numero";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':numero', $numeroCommande);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Remboursement effectué']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors du remboursement']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Vérifier le statut d'un paiement
     */
    public function verifierStatut() {
        try {
            $numeroCommande = isset($_GET['numero']) ? trim($_GET['numero']) : '';
            
            if (empty($numeroCommande)) {
                echo json_encode(['success' => false, 'message' => 'Numéro de commande requis']);
                return;
            }
            
            $query = "SELECT statut_paiement, methode_paiement, transaction_id, date_paiement
                      FROM commandes
                      WHERE numero_commande = :numero";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':numero', $numeroCommande);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'statut' => $result['statut_paiement'],
                    'methode' => $result['methode_paiement'],
                    'transaction_id' => $result['transaction_id'],
                    'date_paiement' => $result['date_paiement']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Commande non trouvée']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($method) {
            case 'GET':
                if ($action === 'infos') {
                    $this->getInfosPaiement();
                } elseif ($action === 'statut') {
                    $this->verifierStatut();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Action non supportée']);
                }
                break;
            case 'POST':
                if ($action === 'creer') {
                    $this->creerCommandeAvecPaiement();
                } elseif ($action === 'confirmer') {
                    $this->confirmerPaiement();
                } elseif ($action === 'rembourser') {
                    $this->rembourser();
                } elseif ($action === 'stripe-session') {
                    $this->creerSessionStripe();
                } elseif ($action === 'paypal-order') {
                    $this->creerPaiementPayPal();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Action non supportée']);
                }
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Méthode non supportée']);
                break;
        }
    }
}

$controller = new PaiementController();
$controller->handleRequest();
?>
