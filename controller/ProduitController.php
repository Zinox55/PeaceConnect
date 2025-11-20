<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../model/Produit.php';

/**
 * Contrôleur Produit - Gestion CRUD
 */
class ProduitController {
    private $produit;
    
    public function __construct() {
        $this->produit = new Produit();
    }
    
    private function validateData($data, $isUpdate = false) {
        $errors = [];
        
        if (empty($data['nom']) || strlen(trim($data['nom'])) < 3) {
            $errors['nom'] = "Le nom doit contenir au moins 3 caractères";
        }
        
        if (!isset($data['prix']) || !is_numeric($data['prix']) || $data['prix'] < 0) {
            $errors['prix'] = "Le prix doit être un nombre positif";
        }
        
        if (!isset($data['stock']) || !is_numeric($data['stock']) || $data['stock'] < 0) {
            $errors['stock'] = "Le stock doit être un nombre positif";
        }
        
        return ['valid' => empty($errors), 'errors' => $errors];
    }
    
    public function create() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $validation = $this->validateData($data);
            
            if (!$validation['valid']) {
                echo json_encode(['success' => false, 'message' => 'Erreur de validation', 'errors' => $validation['errors']]);
                return;
            }
            
            $this->produit->setNom($data['nom']);
            $this->produit->setDescription($data['description'] ?? '');
            $this->produit->setPrix($data['prix']);
            $this->produit->setStock($data['stock']);
            $this->produit->setImage($data['image'] ?? '');
            
            if ($this->produit->create()) {
                echo json_encode(['success' => true, 'message' => 'Produit créé avec succès', 'id' => $this->produit->getId()]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function readAll() {
        try {
            $produits = $this->produit->readAll();
            echo json_encode(['success' => true, 'data' => $produits]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function readOne() {
        try {
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID invalide']);
                return;
            }
            
            $produit = $this->produit->readOne($id);
            
            if ($produit) {
                echo json_encode(['success' => true, 'data' => $produit]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function update() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['id']) || intval($data['id']) <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID invalide']);
                return;
            }
            
            $validation = $this->validateData($data, true);
            if (!$validation['valid']) {
                echo json_encode(['success' => false, 'message' => 'Erreur de validation', 'errors' => $validation['errors']]);
                return;
            }
            
            $this->produit->setId($data['id']);
            $this->produit->setNom($data['nom']);
            $this->produit->setDescription($data['description'] ?? '');
            $this->produit->setPrix($data['prix']);
            $this->produit->setStock($data['stock']);
            $this->produit->setImage($data['image'] ?? '');
            
            if ($this->produit->update()) {
                echo json_encode(['success' => true, 'message' => 'Produit mis à jour avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function delete() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = isset($data['id']) ? intval($data['id']) : 0;
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID invalide']);
                return;
            }
            
            if ($this->produit->delete($id)) {
                echo json_encode(['success' => true, 'message' => 'Produit supprimé avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
            }
        } catch (PDOException $e) {
            // Gestion spécifique des erreurs de contrainte de clé étrangère
            if ($e->getCode() == 23000) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Impossible de supprimer ce produit car il est utilisé dans des commandes existantes.'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function search() {
        try {
            $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
            
            if (empty($keyword)) {
                echo json_encode(['success' => false, 'message' => 'Mot-clé requis']);
                return;
            }
            
            $produits = $this->produit->search($keyword);
            echo json_encode(['success' => true, 'data' => $produits]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        switch ($method) {
            case 'GET':
                if ($action === 'readOne') {
                    $this->readOne();
                } elseif ($action === 'search') {
                    $this->search();
                } else {
                    $this->readAll();
                }
                break;
            case 'POST':
                $this->create();
                break;
            case 'PUT':
                $this->update();
                break;
            case 'DELETE':
                $this->delete();
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Méthode non supportée']);
                break;
        }
    }
}

$controller = new ProduitController();
$controller->handleRequest();
?>
