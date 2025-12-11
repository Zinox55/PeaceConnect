<?php
/**
 * Point d'entrée principal - PeaceConnect
 * Redirige vers la page d'accueil publique
 */

// Obtenir le chemin de base
$baseUrl = '/PeaceConnect';

// Redirection vers la page d'accueil
header('Location: ' . $baseUrl . '/public/index.html');
exit();
?>
