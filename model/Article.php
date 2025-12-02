<?php
class Article {
    private $conn;
    private $table_name = "articles";

    public $id;
    public $titre;
    public $contenu;
    public $auteur;
    public $date_creation;
    public $statut;
    public $image;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer un article
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET titre=:titre, contenu=:contenu, auteur=:auteur, date_creation=:date_creation, statut=:statut, image=:image";
        $stmt = $this->conn->prepare($query);

        // Nettoyage
        $this->titre = htmlspecialchars(strip_tags($this->titre));
        $this->contenu = htmlspecialchars(strip_tags($this->contenu));
        $this->auteur = htmlspecialchars(strip_tags($this->auteur));
        $this->statut = htmlspecialchars(strip_tags($this->statut));
        $this->image = htmlspecialchars(strip_tags($this->image));

        // Binding
        $stmt->bindParam(":titre", $this->titre);
        $stmt->bindParam(":contenu", $this->contenu);
        $stmt->bindParam(":auteur", $this->auteur);
        $stmt->bindParam(":date_creation", $this->date_creation);
        $stmt->bindParam(":statut", $this->statut);
        $stmt->bindParam(":image", $this->image);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lire tous les articles
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lire seulement les articles approuvés avec les stats
    public function readAllApproved() {
        $query = "SELECT a.*, 
                  (SELECT COUNT(*) FROM likes WHERE article_id = a.id) as like_count,
                  (SELECT COUNT(*) FROM commentaires WHERE article_id = a.id) as comment_count
                  FROM " . $this->table_name . " a 
                  WHERE LOWER(a.statut) = 'approuve' 
                  ORDER BY a.date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lire un article
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->titre = $row['titre'];
            $this->contenu = $row['contenu'];
            $this->auteur = $row['auteur'];
            $this->date_creation = $row['date_creation'];
            $this->statut = $row['statut'];
            $this->image = $row['image'];
            return true;
        }
        return false;
    }

    // Mettre à jour un article
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET titre = :titre, contenu = :contenu, auteur = :auteur, statut = :statut, image = :image WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->titre = htmlspecialchars(strip_tags($this->titre));
        $this->contenu = htmlspecialchars(strip_tags($this->contenu));
        $this->auteur = htmlspecialchars(strip_tags($this->auteur));
        $this->statut = htmlspecialchars(strip_tags($this->statut));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':titre', $this->titre);
        $stmt->bindParam(':contenu', $this->contenu);
        $stmt->bindParam(':auteur', $this->auteur);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Supprimer un article
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

    // Compter les articles par statut
    public function countByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE LOWER(statut) = LOWER(:status)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Obtenir les top posts (basé sur les likes et commentaires)
    public function getTopPosts($limit = 3) {
        $query = "SELECT a.*, 
                  (SELECT COUNT(*) FROM likes WHERE article_id = a.id) as like_count,
                  (SELECT COUNT(*) FROM commentaires WHERE article_id = a.id) as comment_count
                  FROM " . $this->table_name . " a 
                  WHERE LOWER(a.statut) = 'approuve'
                  ORDER BY (like_count + comment_count) DESC 
                  LIMIT " . intval($limit);
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
