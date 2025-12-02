<?php
// stats.php - POINT D'ENTRÉE SIMPLE
require_once '../../controller/StatsController.php';

$controller = new StatsController();
$controller->index();
?>