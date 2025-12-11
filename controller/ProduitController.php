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
        
        // Validation du code-barre (optionnel mais doit être valide si fourni)
        if (!empty($data['code_barre']) && strlen(trim($data['code_barre'])) < 3) {
            $errors['code_barre'] = "Le code-barre doit contenir au moins 3 caractères";
        }

        // Validation de la note (0-5)
        if (isset($data['note']) && $data['note'] !== '') {
            if (!is_numeric($data['note']) || intval($data['note']) < 0 || intval($data['note']) > 5) {
                $errors['note'] = "La note doit être un entier entre 0 et 5";
            }
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
            $this->produit->setCodeBarre($data['code_barre'] ?? '');
            $this->produit->setNote($data['note'] ?? 0);
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
            // If request only updates stock (common from admin stock UI), perform a lightweight update
            $isStockOnly = (isset($data['stock']) && count($data) <= 2);
            if ($isStockOnly) {
                $id = intval($data['id']);
                $stock = intval($data['stock']);
                try {
                    if ($this->produit->updateStock($id, $stock)) {
                        echo json_encode(['success' => true, 'message' => 'Stock mis à jour avec succès']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du stock']);
                    }
                } catch (Exception $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                }
                return;
            }

            // Full update path (existing behavior) - validate and update all fields
            // Load existing product so we can preserve fields not included in the payload
            $existingProduit = $this->produit->readOne(intval($data['id']));
            if (!$existingProduit) {
                echo json_encode(['success' => false, 'message' => 'Produit introuvable']);
                return;
            }

            $validation = $this->validateData($data, true);
            if (!$validation['valid']) {
                echo json_encode(['success' => false, 'message' => 'Erreur de validation', 'errors' => $validation['errors']]);
                return;
            }

            // Populate model: preserve existing values when payload doesn't include the field
            $this->produit->setId($data['id']);
            if (isset($data['nom'])) $this->produit->setNom($data['nom']);
            if (array_key_exists('description', $data)) $this->produit->setDescription($data['description']);
            if (isset($data['prix'])) $this->produit->setPrix($data['prix']);
            if (isset($data['stock'])) $this->produit->setStock($data['stock']);
            // Only overwrite code_barre when provided in payload; otherwise keep existing
            if (array_key_exists('code_barre', $data)) {
                $this->produit->setCodeBarre($data['code_barre']);
            }
            if (array_key_exists('note', $data)) $this->produit->setNote($data['note']);
            if (array_key_exists('image', $data)) $this->produit->setImage($data['image']);

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
    
    public function stats() {
        try {
            $stats = $this->produit->getDashboardStats();
            echo json_encode($stats);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function advancedSearch() {
        try {
            $params = [
                'keyword' => isset($_GET['keyword']) ? trim($_GET['keyword']) : null,
                'prix_min' => isset($_GET['prix_min']) && $_GET['prix_min'] !== '' ? $_GET['prix_min'] : null,
                'prix_max' => isset($_GET['prix_max']) && $_GET['prix_max'] !== '' ? $_GET['prix_max'] : null,
                'stock_min' => isset($_GET['stock_min']) && $_GET['stock_min'] !== '' ? $_GET['stock_min'] : null,
                'stock_max' => isset($_GET['stock_max']) && $_GET['stock_max'] !== '' ? $_GET['stock_max'] : null,
                'statut_stock' => isset($_GET['statut_stock']) && $_GET['statut_stock'] !== '' ? $_GET['statut_stock'] : null,
                'sort' => isset($_GET['sort']) ? $_GET['sort'] : 'date_desc',
                'page' => isset($_GET['page']) ? $_GET['page'] : 1,
                'limit' => isset($_GET['limit']) ? $_GET['limit'] : 20,
            ];
            
            $result = $this->produit->advancedSearch($params);
            echo json_encode(['success' => true, 'data' => $result]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Exporter les produits au format CSV
     * Accessible via GET ?action=export
     */
    public function exporterCSV() {
        try {
            // Récupérer tous les produits
            $produits = $this->produit->readAll();
            
            // Nettoyer le tampon et forcer l'envoi des headers CSV
            if (ob_get_length()) {
                ob_end_clean();
            }
            
            // Nom de fichier avec date lisible : produits_03-12-2025_14h30.csv
            $filename = 'produits_' . date('d-m-Y_His') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $out = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // ========== EN-TÊTE ==========
            fputcsv($out, ['LISTE DES PRODUITS'], ';');
            fputcsv($out, ['Date export:', date('d/m/Y H:i:s'), '', 'Total:', count($produits)], ';');
            fputcsv($out, [''], ';');
            
            // Calculer les statistiques
            $totalProduits = count($produits);
            $stockTotal = 0;
            $stockFaible = 0;
            $rupture = 0;
            $valeurTotale = 0;
            
            // ========== EN-TÊTES DU TABLEAU ==========
            $headers = ['ID', 'Nom', 'Description', 'Prix (€)', 'Stock', 'Code Barre', 'Note', 'Image', 'Date Création'];
            fputcsv($out, $headers, ';');
            
            foreach ($produits as $p) {
                // Calcul des stats
                $stock = (int)$p['stock'];
                $stockTotal += $stock;
                
                if ($stock === 0) {
                    $rupture++;
                } elseif ($stock < 10) {
                    $stockFaible++;
                }
                
                $valeurTotale += (float)$p['prix'] * $stock;
                
                $line = [
                    $p['id'],
                    $p['nom'],
                    $p['description'] ?? '',
                    number_format((float)$p['prix'], 2, ',', ''), // Format français avec virgule
                    $p['stock'],
                    $p['code_barre'] ?? '',
                    $p['note'] ?? '0',
                    $p['image'] ?? '',
                    isset($p['created_at']) ? date('d/m/Y H:i', strtotime($p['created_at'])) : ''
                ];
                fputcsv($out, $line, ';');
            }
            
            // ========== RÉSUMÉ STATISTIQUES ==========
            fputcsv($out, [''], ';');
            fputcsv($out, ['RÉSUMÉ'], ';');
            fputcsv($out, ['Total produits:', $totalProduits], ';');
            fputcsv($out, ['Stock total:', $stockTotal . ' unités'], ';');
            fputcsv($out, ['Valeur stock:', number_format($valeurTotale, 2, ',', ' ') . ' €'], ';');
            fputcsv($out, [''], ';');
            fputcsv($out, ['Produits en stock:', ($totalProduits - $rupture)], ';');
            fputcsv($out, ['Produits en rupture:', $rupture], ';');
            fputcsv($out, ['Stock faible (<10):', $stockFaible], ';');
            
            fclose($out);
            exit;
        } catch (Exception $e) {
            // Si erreur, renvoyer JSON
            echo json_encode(['success' => false, 'message' => 'Erreur export CSV: ' . $e->getMessage()]);
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
                } elseif ($action === 'advanced_search') {
                    $this->advancedSearch();
                } elseif ($action === 'stats') {
                    $this->stats();
                } elseif ($action === 'stock_alerts') {
                    $this->getStockAlerts();
                } elseif ($action === 'export') {
                    $this->exporterCSV();
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
    
    /**
     * Récupérer les alertes de stock faible
     */
    public function getStockAlerts() {
        try {
            $seuil = isset($_GET['seuil']) ? (int)$_GET['seuil'] : 10;
            
            $produits = $this->produit->getProduitsStockFaible($seuil);
            $total = $this->produit->countProduitsStockFaible($seuil);
            
            echo json_encode([
                'success' => true,
                'alerts' => $produits,
                'total' => $total,
                'seuil' => $seuil
            ]);
        } catch(Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

$controller = new ProduitController();
$controller->handleRequest();
?>
