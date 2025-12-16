<?php
// Route: Newsletter
// Gestion des requêtes HTTP pour la newsletter

include_once __DIR__ . '/NewsletterController.php';

$controller = new NewsletterController();

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'subscribe') {
        $controller->subscribe();
    }
} elseif (isset($_GET['action'])) {
    if ($_GET['action'] == 'unsubscribe' && isset($_GET['email'])) {
        $controller->unsubscribe();
    }
}
?>