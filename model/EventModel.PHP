<?php
class EventModel {
    private $pdo;
    
    public function __construct() {
        try {
            require_once __DIR__ . '/../config.php';
            $this->pdo = getPDO();
        } catch (Exception $e) {
            throw new Exception("Erreur de connexion : " . $e->getMessage());
        }
    }
    
    public function createEvent($titre, $description, $date_event, $lieu, $image = null, $categorie = null) {
        // CORRECTION : Vérifier si catégorie est valide
        if ($categorie === "" || $categorie === null) {
            $categorie = null;
        } else {
            $categorie = (int)$categorie; // Convertir en entier
        }
        
        $query = "INSERT INTO events (titre, description, date_event, lieu, image, categorie, date_creation) VALUES (:titre, :description, :date_event, :lieu, :image, :categorie, NOW())";
        $stmt = $this->pdo->prepare($query);
        $success = $stmt->execute([
            ':titre' => $titre,
            ':description' => $description,
            ':date_event' => $date_event,
            ':lieu' => $lieu,
            ':image' => $image,
            ':categorie' => $categorie
        ]);
        return $success;
    }
    
    public function getAllEvents() {
        $query = "SELECT * FROM events ORDER BY date_event ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllEventsWithCategory() {
        $query = "SELECT e.*, c.nom as nom_categorie 
                  FROM events e 
                  LEFT JOIN categorie c ON e.categorie = c.idCategorie 
                  ORDER BY e.date_event ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getEventById($id) {
        $query = "SELECT e.*, c.nom as nom_categorie 
                  FROM events e 
                  LEFT JOIN categorie c ON e.categorie = c.idCategorie 
                  WHERE e.id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateEvent($id, $titre, $description, $date_event, $lieu, $image = null, $categorie = null) {
        // CORRECTION : Vérifier si catégorie est valide
        if ($categorie === "" || $categorie === null) {
            $categorie = null;
        } else {
            $categorie = (int)$categorie; // Convertir en entier
        }
        
        if ($image) {
            $query = "UPDATE events SET titre = :titre, description = :description, date_event = :date_event, lieu = :lieu, image = :image, categorie = :categorie WHERE id = :id";
            $params = [
                ':titre' => $titre,
                ':description' => $description,
                ':date_event' => $date_event,
                ':lieu' => $lieu,
                ':image' => $image,
                ':categorie' => $categorie,
                ':id' => $id
            ];
        } else {
            $query = "UPDATE events SET titre = :titre, description = :description, date_event = :date_event, lieu = :lieu, categorie = :categorie WHERE id = :id";
            $params = [
                ':titre' => $titre,
                ':description' => $description,
                ':date_event' => $date_event,
                ':lieu' => $lieu,
                ':categorie' => $categorie,
                ':id' => $id
            ];
        }
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);
    }
    
    public function deleteEvent($id) {
        $query = "DELETE FROM events WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
    
    public function getTotalEvents() {
        $query = "SELECT COUNT(*) as total FROM events";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    public function getUpcomingEvents($limit = 6) {
        $query = "SELECT * FROM events WHERE date_event >= CURDATE() ORDER BY date_event ASC LIMIT :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // NOUVELLE MÉTHODE : Récupérer toutes les catégories
    public function getAllCategories() {
        $query = "SELECT * FROM categorie ORDER BY nom";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>