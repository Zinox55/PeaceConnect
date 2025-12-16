<?php
// Route: Commentaires
// Gestion des requêtes HTTP pour les commentaires

include_once __DIR__ . '/CommentaireController.php';

$controller = new CommentaireController();

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'create') {
        $controller->create();
    } elseif ($_POST['action'] == 'update') {
        $controller->update();
    }
} elseif (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete' && isset($_GET['id'])) {
        $controller->delete($_GET['id']);
    }
} else {
    // Default behavior for backward compatibility
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $controller->create();
    }
}
?>