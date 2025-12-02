<?php
class Like {
    private $conn;
    private $table_name = "likes";

    public $id;
    public $article_id;
    public $ip_address; // Simple tracking by IP since no auth

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ajouter un like
    public function addLike() {
        // Check if already liked
        if($this->isLiked()) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " SET article_id=:article_id, ip_address=:ip_address";
        $stmt = $this->conn->prepare($query);

        $this->article_id = htmlspecialchars(strip_tags($this->article_id));
        $this->ip_address = htmlspecialchars(strip_tags($this->ip_address));

        $stmt->bindParam(":article_id", $this->article_id);
        $stmt->bindParam(":ip_address", $this->ip_address);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Vérifier si déjà liké
    public function isLiked() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE article_id = ? AND ip_address = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->article_id);
        $stmt->bindParam(2, $this->ip_address);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // Compter les likes d'un article
    public function countLikes($articleId) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE article_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $articleId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>
