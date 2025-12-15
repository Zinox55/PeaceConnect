<?php
class SearchModel {
    private $pdo;
    
    public function __construct() {
        try {
            require_once __DIR__ . '/../config.php';
            $this->pdo = getPDO();
        } catch (Exception $e) {
            throw new Exception("Erreur de connexion : " . $e->getMessage());
        }
    }
    
    /**
     * Recherche d'événements avec filtres
     */
    public function searchEvents($searchTerm = '', $categorie = '', $dateFilter = 'all', $lieu = '') {
        $query = "
            SELECT 
                e.*, 
                (
                    SELECT COUNT(i.id) 
                    FROM inscriptions i 
                    WHERE i.evenement = e.titre
                ) AS nb_inscriptions
            FROM events e
            WHERE 1=1
        ";
        
        $params = [];
        
        // Filtre recherche texte
        if (!empty($searchTerm)) {
            $query .= " AND (e.titre LIKE :search OR e.description LIKE :search)";
            $params[':search'] = '%' . $searchTerm . '%';
        }
        
        // Filtre catégorie
        if (!empty($categorie)) {
            $query .= " AND e.categorie = :categorie";
            $params[':categorie'] = $categorie;
        }
        
        // Filtre date
        if ($dateFilter === 'future') {
            $query .= " AND e.date_event >= NOW()";
        } elseif ($dateFilter === 'past') {
            $query .= " AND e.date_event < NOW()";
        }
        
        // Filtre lieu
        if (!empty($lieu)) {
            $query .= " AND e.lieu LIKE :lieu";
            $params[':lieu'] = '%' . $lieu . '%';
        }
        
        $query .= " ORDER BY e.date_event ASC";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère toutes les catégories distinctes
     */
    public function getCategories() {
        $query = "SELECT DISTINCT categorie FROM events WHERE categorie IS NOT NULL ORDER BY categorie";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Récupère tous les lieux distincts
     */
    public function getLieux() {
        $query = "SELECT DISTINCT lieu FROM events WHERE lieu IS NOT NULL ORDER BY lieu";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>