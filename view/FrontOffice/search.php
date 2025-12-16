<?php
session_start();
if (!isset($_SESSION['e'])) {
    header('Location: signin.php');
    exit();
}

require_once '../../controller/SearchController.php';

$controller = new SearchController();
$controller->search();
?>
