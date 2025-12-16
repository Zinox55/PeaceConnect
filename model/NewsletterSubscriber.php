<?php
// Model: NewsletterSubscriber
// Représente la structure et la logique d'accès aux données des abonnés newsletter

class NewsletterSubscriber {
    private $conn;
    private $table_name = "newsletter_subscribers";

    public $id;
    public $email;
    public $nom;
    public $date_inscription;
    public $statut;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ajouter un abonné
    public function subscribe() {
        $query = "INSERT INTO " . $this->table_name . " SET email=:email, nom=:nom, statut=:statut";
        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->statut = 'actif';

        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":statut", $this->statut);

        try {
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            // Email déjà existant
            if($e->getCode() == 23000) {
                return 'duplicate';
            }
            return false;
        }
    }

    // Désabonner
    public function unsubscribe($email) {
        $query = "UPDATE " . $this->table_name . " SET statut='inactif' WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Récupérer tous les abonnés actifs
    public function getAllActiveSubscribers() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE statut = 'actif' ORDER BY date_inscription DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Compter les abonnés actifs
    public function countActiveSubscribers() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE statut = 'actif'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Vérifier si un email existe
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }
}
?>
