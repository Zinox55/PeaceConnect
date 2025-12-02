<?php
require_once '../../model/StatsModel.php';

class StatsController {
    private $statsModel;
    
    public function __construct() {
        $this->statsModel = new StatsModel();
    }
    
    public function index() {
        try {
            $data = [
                'topEvents' => $this->statsModel->getTopEvents(),
                'inscriptionsByMonth' => $this->statsModel->getInscriptionsByMonth(),
                'generalStats' => $this->statsModel->getGeneralStats(),
                'recentActivity' => $this->statsModel->getRecentActivity()
            ];
            
            include '../../view/backoffice/stats_dashboard.php';
            
        } catch (Exception $e) {
            echo "Erreur: " . $e->getMessage();
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'stats') {
    $controller = new StatsController();
    $controller->index();
    exit;
}
?>