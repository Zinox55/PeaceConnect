<?php
/**
 * Router API pour l'application PeaceConnect
 * Gère toutes les requêtes API vers les contrôleurs
 * 
 * Usage: api.php?controller=produit&method=readAll
 */

// Démarrage de la session
session_start();

// En-têtes CORS et JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Gestion de la requête OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inclusion de la configuration de la base de données
require_once __DIR__ . '/config.php';

// Gestion des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 0); // Désactivé en production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/api_errors.log');

/**
 * Fonction pour envoyer une réponse d'erreur JSON
 */
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

try {
    // Récupération du contrôleur et de la méthode
    $controller = isset($_GET['controller']) ? strtolower(trim($_GET['controller'])) : '';
    $method = isset($_GET['method']) ? trim($_GET['method']) : '';
    
    // Validation du contrôleur
    if (empty($controller)) {
        sendError('Contrôleur non spécifié', 400);
    }
    
    // Mapping des contrôleurs
    $controllerMap = [
        'produit' => [
            'file' => 'ProduitController.php',
            'class' => 'ProduitController',
            'defaultMethod' => 'readAll'
        ],
        'panier' => [
            'file' => 'PanierController.php',
            'class' => 'PanierController',
            'defaultMethod' => 'getAll'
        ],
        'commande' => [
            'file' => 'CommandeController.php',
            'class' => 'CommandeController',
            'defaultMethod' => 'readAll'
        ],
        'paiement' => [
            'file' => 'PaiementController.php',
            'class' => 'PaiementController',
            'defaultMethod' => 'traiter'
        ],
        'statistiques' => [
            'file' => 'StatistiquesController.php',
            'class' => 'StatistiquesController',
            'defaultMethod' => 'getAll'
        ],
        'email' => [
            'file' => 'EmailController.php',
            'class' => 'EmailController',
            'defaultMethod' => 'send'
        ],
        'upload' => [
            'file' => 'UploadController.php',
            'class' => 'UploadController',
            'defaultMethod' => 'upload'
        ]
    ];
    
    // Vérification si le contrôleur existe
    if (!isset($controllerMap[$controller])) {
        sendError('Contrôleur non trouvé: ' . $controller, 404);
    }
    
    $controllerInfo = $controllerMap[$controller];
    $controllerFile = __DIR__ . '/controller/' . $controllerInfo['file'];
    
    // Vérification si le fichier du contrôleur existe
    if (!file_exists($controllerFile)) {
        sendError('Fichier du contrôleur non trouvé', 500);
    }
    
    // Inclusion du contrôleur
    require_once $controllerFile;
    
    // Vérification si la classe existe
    if (!class_exists($controllerInfo['class'])) {
        sendError('Classe du contrôleur non trouvée', 500);
    }
    
    // Instanciation du contrôleur
    $controllerInstance = new $controllerInfo['class']();
    
    // Utilisation de la méthode par défaut si non spécifiée
    if (empty($method)) {
        $method = $controllerInfo['defaultMethod'];
    }
    
    // Vérification si la méthode existe
    if (!method_exists($controllerInstance, $method)) {
        sendError('Méthode non trouvée: ' . $method, 404);
    }
    
    // Appel de la méthode
    $controllerInstance->$method();
    
} catch (PDOException $e) {
    // Erreur de base de données
    error_log('Erreur PDO: ' . $e->getMessage());
    sendError('Erreur de base de données', 500);
    
} catch (Exception $e) {
    // Erreur générale
    error_log('Erreur: ' . $e->getMessage());
    sendError('Une erreur s\'est produite: ' . $e->getMessage(), 500);
}
?>
