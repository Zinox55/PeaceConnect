<?php
require_once __DIR__ . '/Database.php';

/**
 * Classe Produit - Modèle pour la gestion des produits
 */
class Produit {
    private $id;
    private $nom;
    private $description;
    private $prix;
    private $stock;
    private $image;
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getPrix() { return $this->prix; }
    public function getStock() { return $this->stock; }
    public function getImage() { return $this->image; }
    
    // Setters avec validation
    public function setId($id) { 
        $this->id = filter_var($id, FILTER_VALIDATE_INT);
    }
    
    public function setNom($nom) { 
        if (empty(trim($nom)) || strlen(trim($nom)) < 3) {
            throw new Exception("Le nom doit contenir au moins 3 caractères");
        }
        $this->nom = htmlspecialchars(strip_tags(trim($nom)));
    }
    
    public function setDescription($description) { 
        $this->description = htmlspecialchars(strip_tags(trim($description)));
    }
    
    public function setPrix($prix) { 
        if (!is_numeric($prix) || $prix < 0) {
            throw new Exception("Le prix doit être un nombre positif");
        }
        $this->prix = floatval($prix);
    }
    
    public function setStock($stock) { 
        if (!is_numeric($stock) || $stock < 0) {
            throw new Exception("Le stock doit être un nombre positif");
        }
        $this->stock = intval($stock);
    }
    
    public function setImage($image) { 
        $this->image = htmlspecialchars(strip_tags(trim($image)));
    }
    
    // CREATE
    public function create() {
        try {
            $query = "INSERT INTO produits (nom, description, prix, stock, image) 
                      VALUES (:nom, :description, :prix, :stock, :image)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':prix', $this->prix);
            $stmt->bindParam(':stock', $this->stock);
            $stmt->bindParam(':image', $this->image);
            
            if ($stmt->execute()) {
                $this->id = $this->db->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erreur création: " . $e->getMessage());
        }
    }
    
    // READ ALL
    public function readAll() {
        try {
            $query = "SELECT * FROM produits ORDER BY date_creation DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture: " . $e->getMessage());
        }
    }
    
    // READ ONE
    public function readOne($id) {
        try {
            $query = "SELECT * FROM produits WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $this->id = $row['id'];
                $this->nom = $row['nom'];
                $this->description = $row['description'];
                $this->prix = $row['prix'];
                $this->stock = $row['stock'];
                $this->image = $row['image'];
                return $row;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture: " . $e->getMessage());
        }
    }
    
    // UPDATE
    public function update() {
        try {
            $query = "UPDATE produits 
                      SET nom = :nom, description = :description, prix = :prix, 
                          stock = :stock, image = :image 
                      WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':nom', $this->nom);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':prix', $this->prix);
            $stmt->bindParam(':stock', $this->stock);
            $stmt->bindParam(':image', $this->image);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur mise à jour: " . $e->getMessage());
        }
    }
    
    // DELETE
    public function delete($id) {
        try {
            $query = "DELETE FROM produits WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur suppression: " . $e->getMessage());
        }
    }
    
    // SEARCH
    public function search($keyword) {
        try {
            $query = "SELECT * FROM produits 
                      WHERE nom LIKE :keyword OR description LIKE :keyword 
                      ORDER BY date_creation DESC";
            $stmt = $this->db->prepare($query);
            $keyword = "%{$keyword}%";
            $stmt->bindParam(':keyword', $keyword);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur recherche: " . $e->getMessage());
        }
    }
}
?>
