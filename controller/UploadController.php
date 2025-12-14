<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

/**
 * Contrôleur Upload Image
 */
class UploadController {
    private $uploadDir = __DIR__ . '/../view/BackOffice/assets/img/produits/';
    private $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $maxSize = 5 * 1024 * 1024; // 5MB
    
    public function __construct() {
        // Créer le dossier s'il n'existe pas
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }
    
    public function upload() {
        try {
            // Vérifier si un fichier a été uploadé
            if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
                echo json_encode(['success' => false, 'message' => 'Aucun fichier sélectionné']);
                return;
            }
            
            $file = $_FILES['image'];
            
            // Vérifier les erreurs
            if ($file['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload']);
                return;
            }
            
            // Vérifier la taille
            if ($file['size'] > $this->maxSize) {
                echo json_encode(['success' => false, 'message' => 'Fichier trop volumineux (max 5MB)']);
                return;
            }
            
            // Vérifier le type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $this->allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé (JPG, JPEG, PNG, GIF, WEBP uniquement)']);
                return;
            }
            
            // Générer un nom unique
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Vérifier l'extension
            if (!in_array($extension, $this->allowedExtensions)) {
                echo json_encode(['success' => false, 'message' => 'Extension de fichier non autorisée (jpg, jpeg, png, gif, webp uniquement)']);
                return;
            }
            $filename = 'produit_' . time() . '_' . uniqid() . '.' . $extension;
            $filepath = $this->uploadDir . $filename;
            
            // Déplacer le fichier
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Image uploadée avec succès',
                    'filename' => $filename,
                    'path' => 'view/BackOffice/assets/img/produits/' . $filename
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la sauvegarde']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method === 'POST') {
            $this->upload();
        } else {
            echo json_encode(['success' => false, 'message' => 'Méthode non supportée']);
        }
    }
}

$controller = new UploadController();
$controller->handleRequest();
?>
