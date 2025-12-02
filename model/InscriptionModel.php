<?php
class InscriptionModel {
    private $pdo;
    
    public function __construct() {
        try {
            require_once __DIR__ . '/../config.php';
            $this->pdo = getPDO();
        } catch (Exception $e) {
            throw new Exception("Erreur de connexion : " . $e->getMessage());
        }
    }
    
    public function createInscription($nom, $email, $telephone, $evenement) {
        $checkQuery = "SELECT id FROM inscriptions WHERE email = :email AND evenement = :evenement";
        $checkStmt = $this->pdo->prepare($checkQuery);
        $checkStmt->execute([':email' => $email, ':evenement' => $evenement]);
        
        if ($checkStmt->fetch()) {
            throw new Exception("Cette adresse email est déjà inscrite à cet événement.");
        }
        
        $query = "INSERT INTO inscriptions (nom, email, telephone, evenement, date_inscription) VALUES (:nom, :email, :telephone, :evenement, NOW())";
        $stmt = $this->pdo->prepare($query);
        $success = $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':evenement' => $evenement
        ]);
        return $success;
    }
    
    public function getAllInscriptions() {
        $query = "SELECT * FROM inscriptions ORDER BY date_inscription DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getInscriptionsByEvent($evenement) {
        $query = "SELECT * FROM inscriptions WHERE evenement = :evenement ORDER BY date_inscription DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':evenement' => $evenement]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalInscriptions() {
        $query = "SELECT COUNT(*) as total FROM inscriptions";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    public function getRecentInscriptions($limit = 10) {
        $query = "SELECT * FROM inscriptions WHERE date_inscription >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY date_inscription DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // NOUVELLES MÉTHODES CRUD
    public function deleteInscription($id) {
        $query = "DELETE FROM inscriptions WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
    
    public function getInscriptionById($id) {
        $query = "SELECT * FROM inscriptions WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateInscription($id, $nom, $email, $telephone, $evenement) {
        $query = "UPDATE inscriptions SET nom = :nom, email = :email, telephone = :telephone, evenement = :evenement WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':evenement' => $evenement,
            ':id' => $id
        ]);
    }
}
?>