<?php
require_once '../../model/SearchModel.php';

class SearchController {
    private $searchModel;
    
    public function __construct() {
        $this->searchModel = new SearchModel();
    }
    
    public function search() {
        try {
            // Récupérer les paramètres de recherche
            $searchTerm = $_GET['q'] ?? '';
            $categorie = $_GET['categorie'] ?? '';
            $dateFilter = $_GET['date'] ?? 'all';
            $lieu = $_GET['lieu'] ?? '';
            
            // Effectuer la recherche
            $events = $this->searchModel->searchEvents($searchTerm, $categorie, $dateFilter, $lieu);
            $categories = $this->searchModel->getCategories();
            $lieux = $this->searchModel->getLieux();
            
            // Préparer les données pour la vue
            $data = [
                'events' => $events,
                'searchTerm' => $searchTerm,
                'selectedCategorie' => $categorie,
                'selectedDateFilter' => $dateFilter,
                'selectedLieu' => $lieu,
                'categories' => $categories,
                'lieux' => $lieux,
                'totalResults' => count($events)
            ];
            
            include '../../view/FrontOffice/search_results.php';
            
        } catch (Exception $e) {
            echo "Erreur lors de la recherche: " . $e->getMessage();
        }
    }
}

// Routage automatique
if (isset($_GET['action']) && $_GET['action'] == 'search') {
    $controller = new SearchController();
    $controller->search();
    exit;
}
?>