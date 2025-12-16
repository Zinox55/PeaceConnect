<?php
// Route: Articles
// Gestion des requêtes HTTP pour les articles

include_once __DIR__ . '/ArticleController.php';

$controller = new ArticleController();

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
}
?>