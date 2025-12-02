<?php
include_once __DIR__ . '/CommentaireController.php';

$controller = new CommentaireController();

// Check if it's a POST request (form submission)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $controller->create();
}
?>
