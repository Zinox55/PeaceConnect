<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../model/Commande.php';


/**
 * Contrôleur Commande
 */
class CommandeController {
    private $commande;
    
    public function __construct() {
        $this->commande = new Commande();
    }

    private function logError($message) {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $file = $logDir . '/commande_errors.log';
        $entry = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
        @file_put_contents($file, $entry, FILE_APPEND | LOCK_EX);
    }
    
    private function validerDonnees($data) {
        $errors = [];
        
        if (empty($data['nom']) || strlen(trim($data['nom'])) < 3) {
            $errors['nom'] = "Le nom doit contenir au moins 3 caractères";
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email invalide";
        }
        
        if (empty($data['telephone']) || strlen(trim($data['telephone'])) < 8) {
            $errors['telephone'] = "Numéro de téléphone invalide";
        }
        
        if (empty($data['adresse']) || strlen(trim($data['adresse'])) < 10) {
            $errors['adresse'] = "L'adresse doit contenir au moins 10 caractères";
        }
        
        return ['valid' => empty($errors), 'errors' => $errors];
    }
    
    public function creer() {
        // Capture any stray output (warnings, notices) so JSON stays valid and we can log details
        ob_start();
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            $validation = $this->validerDonnees($data);
            if (!$validation['valid']) {
                $out = trim(ob_get_clean());
                $this->logError('Validation error on creer: ' . json_encode($validation['errors']) . '\nOutput:\n' . $out);
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validation['errors']
                ]);
                return;
            }

            if ($this->commande->creerDepuisPanier($data)) {
                $numeroCommande = $this->commande->getNumeroCommande();
                $commandeId = $this->commande->getLastInsertId();

                // Envoyer l'email de confirmation
                $emailSent = $this->envoyerEmailConfirmation($commandeId, $data);

                $out = trim(ob_get_clean());
                if (!empty($out)) {
                    $this->logError('Output on successful creer: ' . $out);
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Commande créée avec succès',
                    'numero_commande' => $numeroCommande,
                    'email_sent' => $emailSent
                ]);
            } else {
                $out = trim(ob_get_clean());
                $this->logError('Unknown error creating order. Output: ' . $out);
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
            }
        } catch (PDOException $e) {
            $out = trim(ob_get_clean());
            $this->logError('PDOException in creer: ' . $e->getMessage() . '\nOutput:\n' . $out);
            echo json_encode(['success' => false, 'message' => 'Erreur base de données', 'detail' => $e->getMessage()]);
            return;
        } catch (Exception $e) {
            $out = trim(ob_get_clean());
            $this->logError('Exception in creer: ' . $e->getMessage() . '\nOutput:\n' . $out);
            echo json_encode(['success' => false, 'message' => $e->getMessage(), 'server_output' => $out]);
            return;
        }
    }
    
    /**
     * Envoyer l'email de confirmation de commande
     */
    private function envoyerEmailConfirmation($commandeId, $clientData) {
        try {
            // Vérifier si PHPMailer est disponible
            $autoloadPath = __DIR__ . '/vendor/autoload.php';
            if (!file_exists($autoloadPath)) {
                error_log("PHPMailer non installé - Email non envoyé");
                return false;
            }
            
            require_once $autoloadPath;
            require_once __DIR__ . '/../model/Mailer.php';
            
            $mailer = new Mailer();
            
            // Récupérer les détails de la commande
            $commande = $this->commande->lireUne($commandeId);
            $details = $this->commande->lireDetailsCommande($commandeId);
            
            $emailData = [
                'commande' => $commande,
                'details' => $details,
                'client' => $clientData
            ];
            
            // Envoyer l'email au client
            $clientEmailSent = $mailer->sendTemplate($clientData['email'], 'order_confirmation', $emailData);
            
            // Envoyer une copie à l'admin
            $adminEmailSent = $mailer->sendTemplate('hamdounidhiaeddine@gmail.com', 'order_confirmation', $emailData);
            
            return $clientEmailSent || $adminEmailSent;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email: " . $e->getMessage());
            return false;
        }
    }
    
    public function lireTout() {
        try {
            $commandes = $this->commande->lireTout();
            echo json_encode(['success' => true, 'data' => $commandes]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Récupérer la dernière commande créée
     */
    public function getDerniere() {
        try {
            require_once __DIR__ . '/../config.php';
            $db = config::getConnexion();
            
            $query = "SELECT * FROM commandes ORDER BY id DESC LIMIT 1";
            $stmt = $db->query($query);
            $commande = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($commande) {
                echo json_encode([
                    'success' => true,
                    'commande' => $commande
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Aucune commande trouvée'
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
     * Exporter les commandes au format CSV optimisé
     * Accessible via GET ?action=export
     */
    public function exporterCSV() {
        try {
            // Récupérer les données
            if (method_exists($this->commande, 'lireToutAvecDetails')) {
                $rows = $this->commande->lireToutAvecDetails();
            } else {
                $rows = $this->commande->lireTout();
            }

            // Nettoyer le tampon
            if (ob_get_length()) {
                ob_end_clean();
            }

            // Nom de fichier
            $filename = 'Commandes_' . date('Ymd_His') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');

            $out = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

            // ========== EN-TÊTE ==========
            fputcsv($out, ['LISTE DES COMMANDES'], ';');
            fputcsv($out, ['Date export:', date('d/m/Y H:i:s'), '', 'Total:', count($rows)], ';');
            fputcsv($out, [''], ';');
            
            // ========== STATISTIQUES ==========
            $totalRevenu = 0;
            $statsStatuts = [
                'en_attente' => 0,
                'confirmee' => 0,
                'livree' => 0,
                'annulee' => 0
            ];
            
            foreach ($rows as $r) {
                $totalRevenu += isset($r['total']) ? floatval($r['total']) : 0;
                $statut = isset($r['statut']) ? $r['statut'] : 'en_attente';
                if (isset($statsStatuts[$statut])) {
                    $statsStatuts[$statut]++;
                }
            }
            
            // ========== EN-TÊTES DU TABLEAU ==========
            $headers = [
                'ID',
                'Numéro Commande',
                'Nom Client',
                'Email',
                'Téléphone',
                'Adresse',
                'Total (€)',
                'Statut',
                'Méthode Paiement',
                'Statut Paiement',
                'Date Commande',
                'Date Livraison',
                'Nb Articles',
                'Quantité'
            ];
            fputcsv($out, $headers, ';');

            // Données
            foreach ($rows as $r) {
                // Formater le statut en français
                $statutFr = [
                    'en_attente' => 'En Attente',
                    'confirmee' => 'Confirmée',
                    'livree' => 'Livrée',
                    'annulee' => 'Annulée'
                ];
                $statut = isset($r['statut']) ? ($statutFr[$r['statut']] ?? $r['statut']) : 'En Attente';
                
                // Formater la méthode de paiement
                $methodePaiement = '';
                if (isset($r['methode_paiement'])) {
                    $methodesMap = [
                        'card' => 'Carte Bancaire',
                        'paypal' => 'PayPal',
                        'stripe' => 'Stripe',
                        'virement' => 'Virement Bancaire'
                    ];
                    $methodePaiement = $methodesMap[$r['methode_paiement']] ?? $r['methode_paiement'];
                }
                
                // Formater le statut de paiement
                $statutPaiement = '';
                if (isset($r['statut_paiement'])) {
                    $statutsMap = [
                        'paye' => 'Payé',
                        'en_attente' => 'En attente',
                        'echoue' => 'Échoué',
                        'rembourse' => 'Remboursé'
                    ];
                    $statutPaiement = $statutsMap[$r['statut_paiement']] ?? $r['statut_paiement'];
                }
                
                // Formater les dates
                $dateCommande = '';
                if (isset($r['date_commande']) && !empty($r['date_commande'])) {
                    $dateCommande = date('d/m/Y H:i', strtotime($r['date_commande']));
                }
                
                $dateLivraison = '';
                if (isset($r['date_livraison']) && !empty($r['date_livraison'])) {
                    $dateLivraison = date('d/m/Y H:i', strtotime($r['date_livraison']));
                }
                
                $line = [
                    isset($r['id']) ? $r['id'] : '',
                    isset($r['numero_commande']) ? $r['numero_commande'] : '',
                    isset($r['nom_client']) ? $r['nom_client'] : '',
                    isset($r['email_client']) ? $r['email_client'] : '',
                    isset($r['telephone_client']) ? $r['telephone_client'] : '',
                    isset($r['adresse_client']) ? str_replace(["\r\n", "\n", "\r"], ' ', $r['adresse_client']) : '',
                    isset($r['total']) ? number_format((float)$r['total'], 2, ',', '') : '',
                    $statut,
                    $methodePaiement,
                    $statutPaiement,
                    $dateCommande,
                    $dateLivraison,
                    isset($r['nombre_produits']) ? $r['nombre_produits'] : (isset($r['nb_articles']) ? $r['nb_articles'] : ''),
                    isset($r['quantite_totale']) ? $r['quantite_totale'] : ''
                ];
                fputcsv($out, $line, ';');
            }
            
            // ========== RÉSUMÉ FINAL ==========
            fputcsv($out, [''], ';');
            fputcsv($out, ['RÉSUMÉ'], ';');
            fputcsv($out, ['Commandes en attente:', $statsStatuts['en_attente']], ';');
            fputcsv($out, ['Commandes confirmées:', $statsStatuts['confirmee']], ';');
            fputcsv($out, ['Commandes livrées:', $statsStatuts['livree']], ';');
            fputcsv($out, ['Commandes annulées:', $statsStatuts['annulee']], ';');
            fputcsv($out, [''], ';');
            fputcsv($out, ['Revenu total:', number_format($totalRevenu, 2, ',', ' ') . ' €'], ';');
            
            fclose($out);
            exit;
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function suivre() {
        try {
            $numero = isset($_GET['numero']) ? trim($_GET['numero']) : '';
            
            if (empty($numero)) {
                echo json_encode(['success' => false, 'message' => 'Numéro de commande requis']);
                return;
            }
            
            $commande = $this->commande->lireParNumero($numero);
            
            if ($commande) {
                $details = $this->commande->lireDetails($commande['id']);
                echo json_encode([
                    'success' => true,
                    'commande' => $commande,
                    'details' => $details
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Commande non trouvée']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function mettreAJourStatut() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['commande_id']) || !isset($data['statut'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                return;
            }
            
            if ($this->commande->mettreAJourStatut($data['commande_id'], $data['statut'])) {
                echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function getDetails() {
        try {
            // Support pour ID ou numéro de commande
            if (isset($_GET['numero'])) {
                $numero = trim($_GET['numero']);
                $commande = $this->commande->lireParNumero($numero);
                
                if (!$commande) {
                    echo json_encode(['success' => false, 'message' => 'Commande non trouvée']);
                    return;
                }
                
                $id = $commande['id'];
            } else {
                $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            }
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID invalide']);
                return;
            }
            
            // Récupérer la commande
            $commande = $this->commande->lireUne($id);
            
            // Récupérer les articles
            $articles = $this->commande->lireDetails($id);
            
            echo json_encode([
                'success' => true,
                'commande' => $commande,
                'articles' => $articles
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($method) {
            case 'GET':
                if ($action === 'suivre') {
                    $this->suivre();
                } elseif ($action === 'export') {
                    $this->exporterCSV();
                } elseif ($action === 'details') {
                    $this->getDetails();
                } elseif ($action === 'derniere') {
                    $this->getDerniere();
                } else {
                    $this->lireTout();
                }
                break;
            case 'POST':
                $this->creer();
                break;
            case 'PUT':
                $this->mettreAJourStatut();
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Méthode non supportée']);
                break;
        }
    }
}

$controller = new CommandeController();
$controller->handleRequest();
?>
