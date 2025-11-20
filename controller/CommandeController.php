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
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            $validation = $this->validerDonnees($data);
            if (!$validation['valid']) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validation['errors']
                ]);
                return;
            }
            
            if ($this->commande->creerDepuisPanier($data)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Commande créée avec succès',
                    'numero_commande' => $this->commande->getNumeroCommande()
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
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
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($method) {
            case 'GET':
                if ($action === 'suivre') {
                    $this->suivre();
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
