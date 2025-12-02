<?php
class Commentaire {
    private $conn;
    private $table_name = "commentaires";

    public $id;
    public $article_id;
    public $auteur;
    public $contenu;
    public $date_creation;
    public $statut;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer un commentaire
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET article_id=:article_id, auteur=:auteur, contenu=:contenu, date_creation=:date_creation, statut=:statut";
        $stmt = $this->conn->prepare($query);

        $this->article_id = htmlspecialchars(strip_tags($this->article_id));
        $this->auteur = htmlspecialchars(strip_tags($this->auteur));
        $this->contenu = htmlspecialchars(strip_tags($this->contenu));
        $this->statut = htmlspecialchars(strip_tags($this->statut));

        $stmt->bindParam(":article_id", $this->article_id);
        $stmt->bindParam(":auteur", $this->auteur);
        $stmt->bindParam(":contenu", $this->contenu);
        $stmt->bindParam(":date_creation", $this->date_creation);
        $stmt->bindParam(":statut", $this->statut);

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
}
?>
