<?php
// Model: Commentaire
// Représente la structure et la logique d'accès aux données des commentaires

class Commentaire {
    private $conn;
    private $table_name = "commentaires";

    public $id;
    public $article_id;
    public $auteur;
    public $contenu;
    public $date_creation;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer un commentaire
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET article_id=:article_id, auteur=:auteur, contenu=:contenu, date_creation=:date_creation";
        $stmt = $this->conn->prepare($query);

        $this->article_id = htmlspecialchars(strip_tags($this->article_id));
        $this->auteur = htmlspecialchars(strip_tags($this->auteur));
        $this->contenu = strip_tags($this->contenu); // Garder le contenu lisible

        $stmt->bindParam(":article_id", $this->article_id);
        $stmt->bindParam(":auteur", $this->auteur);
        $stmt->bindParam(":contenu", $this->contenu);
        $stmt->bindParam(":date_creation", $this->date_creation);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lire les commentaires d'un article
    public function readByArticle($articleId) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE article_id = ? ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $articleId);
        $stmt->execute();
        return $stmt;
    }
    
    // Lire tous les commentaires (pour le dashboard)
    public function readAll() {
        $query = "SELECT c.*, a.titre as article_titre FROM " . $this->table_name . " c LEFT JOIN articles a ON c.article_id = a.id ORDER BY c.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Compter tous les commentaires
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Lire un commentaire spécifique
    public function readOne() {
        $query = "SELECT c.*, a.titre as article_titre FROM " . $this->table_name . " c LEFT JOIN articles a ON c.article_id = a.id WHERE c.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->article_id = $row['article_id'];
            $this->auteur = $row['auteur'];
            $this->contenu = $row['contenu'];
            $this->date_creation = $row['date_creation'];
            return $row; // Return full row including article_titre
        }
        return false;
    }

    // Mettre à jour un commentaire
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET auteur = :auteur, contenu = :contenu WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->auteur = htmlspecialchars(strip_tags($this->auteur));
        $this->contenu = strip_tags($this->contenu);
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':auteur', $this->auteur);
        $stmt->bindParam(':contenu', $this->contenu);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Supprimer un commentaire
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
