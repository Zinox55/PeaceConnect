<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../model/Panier.php';

/**
 * Contrôleur Panier
 */
class PanierController {
    private $panier;
    
    public function __construct() {
        $this->panier = new Panier();
    }
    
    public function ajouter() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['produit_id']) || intval($data['produit_id']) <= 0) {
                echo json_encode(['success' => false, 'message' => 'Produit ID invalide']);
                return;
            }
            
            $this->panier->setProduitId($data['produit_id']);
            $this->panier->setQuantite($data['quantite'] ?? 1);
            
            if ($this->panier->ajouter()) {
                echo json_encode(['success' => true, 'message' => 'Produit ajouté au panier']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function lire() {
        try {
            $items = $this->panier->lireTout();
            $total = $this->panier->calculerTotal();
            $count = $this->panier->compterArticles();
            
            // Convertir les prix en float pour éviter les problèmes JavaScript
            foreach ($items as &$item) {
                $item['prix'] = floatval($item['prix']);
                $item['quantite'] = intval($item['quantite']);
                $item['sous_total'] = floatval($item['sous_total']);
            }
            
            echo json_encode([
                'success' => true, 
                'data' => $items, 
                'total' => floatval($total),
                'count' => intval($count)
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function mettreAJour() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['panier_id']) || !isset($data['quantite'])) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                return;
            }
            
            if ($this->panier->mettreAJourQuantite($data['panier_id'], $data['quantite'])) {
                echo json_encode(['success' => true, 'message' => 'Quantité mise à jour']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function supprimer() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = isset($data['panier_id']) ? intval($data['panier_id']) : 0;
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID invalide']);
                return;
            }
            
            if ($this->panier->supprimer($id)) {
                echo json_encode(['success' => true, 'message' => 'Produit retiré du panier']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function vider() {
        try {
            if ($this->panier->vider()) {
                echo json_encode(['success' => true, 'message' => 'Panier vidé']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors du vidage']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function compter() {
        try {
            $count = $this->panier->compterArticles();
            echo json_encode(['success' => true, 'count' => $count]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($method) {
            case 'GET':
                if ($action === 'count') {
                    $this->compter();
                } else {
                    $this->lire();
                }
                break;
            case 'POST':
                $this->ajouter();
                break;
            case 'PUT':
                $this->mettreAJour();
                break;
            case 'DELETE':
                if ($action === 'vider') {
                    $this->vider();
                } else {
                    $this->supprimer();
                }
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Méthode non supportée']);
                break;
        }
    }
}

$controller = new PanierController();
$controller->handleRequest();
?>
