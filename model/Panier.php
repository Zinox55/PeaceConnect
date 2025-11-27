<?php
require_once __DIR__ . '/../config.php';

/**
 * Classe Panier - Gestion du panier
 */
class Panier {
    private $id;
    private $produit_id;
    private $quantite;
    private $db;
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getProduitId() { return $this->produit_id; }
    public function getQuantite() { return $this->quantite; }
    
    // Setters
    public function setId($id) { $this->id = intval($id); }
    public function setProduitId($produit_id) { $this->produit_id = intval($produit_id); }
    public function setQuantite($quantite) { 
        if ($quantite < 1) throw new Exception("Quantité invalide");
        $this->quantite = intval($quantite); 
    }
    
    // Ajouter au panier
    public function ajouter() {
        try {
            // Vérifier si le produit existe déjà
            $query = "SELECT id, quantite FROM panier WHERE produit_id = :produit_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':produit_id', $this->produit_id);
            $stmt->execute();
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Mettre à jour la quantité
                $nouvelle_quantite = $row['quantite'] + $this->quantite;
                $query = "UPDATE panier SET quantite = :quantite WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':quantite', $nouvelle_quantite);
                $stmt->bindParam(':id', $row['id']);
                return $stmt->execute();
            } else {
                // Insérer nouveau
                $query = "INSERT INTO panier (produit_id, quantite) VALUES (:produit_id, :quantite)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':produit_id', $this->produit_id);
                $stmt->bindParam(':quantite', $this->quantite);
                return $stmt->execute();
            }
        } catch (PDOException $e) {
            throw new Exception("Erreur ajout panier: " . $e->getMessage());
        }
    }
    
    // Lire tout le panier avec détails produits
    public function lireTout() {
        try {
            $query = "SELECT p.id as panier_id, pr.id, pr.nom, pr.description, pr.prix, 
                      pr.image, p.quantite, (pr.prix * p.quantite) as sous_total
                      FROM panier p
                      INNER JOIN produits pr ON p.produit_id = pr.id
                      ORDER BY p.date_ajout DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture panier: " . $e->getMessage());
        }
    }
    
    // Mettre à jour la quantité
    public function mettreAJourQuantite($panier_id, $quantite) {
        try {
            if ($quantite < 1) throw new Exception("Quantité invalide");
            
            $query = "UPDATE panier SET quantite = :quantite WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':quantite', $quantite, PDO::PARAM_INT);
            $stmt->bindParam(':id', $panier_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur mise à jour: " . $e->getMessage());
        }
    }
    
    // Supprimer du panier
    public function supprimer($panier_id) {
        try {
            $query = "DELETE FROM panier WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $panier_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur suppression: " . $e->getMessage());
        }
    }
    
    // Vider le panier
    public function vider() {
        try {
            $query = "DELETE FROM panier";
            $stmt = $this->db->prepare($query);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur vidage panier: " . $e->getMessage());
        }
    }
    
    // Calculer le total
    public function calculerTotal() {
        try {
            $query = "SELECT SUM(pr.prix * p.quantite) as total
                      FROM panier p
                      INNER JOIN produits pr ON p.produit_id = pr.id";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur calcul total: " . $e->getMessage());
        }
    }
    
    // Compter les articles
    public function compterArticles() {
        try {
            $query = "SELECT SUM(quantite) as total FROM panier";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur comptage: " . $e->getMessage());
        }
    }
    
    // ========================================
    // NOUVELLES MÉTHODES AVEC JOINTURES
    // ========================================
    
    /**
     * Lire le panier avec vérification du stock
     * Jointure : panier → produits (avec CASE pour disponibilité)
     */
    public function lireToutAvecStock() {
        try {
            $query = "SELECT 
                        p.id AS panier_id,
                        p.quantite AS quantite_demandee,
                        p.date_ajout,
                        pr.id AS produit_id,
                        pr.nom,
                        pr.description,
                        pr.prix,
                        pr.stock AS stock_disponible,
                        pr.image,
                        CASE 
                            WHEN pr.stock >= p.quantite THEN 'Disponible'
                            WHEN pr.stock > 0 THEN 'Stock insuffisant'
                            ELSE 'Rupture de stock'
                        END AS disponibilite,
                        (p.quantite * pr.prix) AS sous_total
                      FROM panier p
                      INNER JOIN produits pr ON p.produit_id = pr.id
                      ORDER BY p.date_ajout DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture panier avec stock: " . $e->getMessage());
        }
    }
    
    /**
     * Vérifier si tous les articles du panier sont disponibles
     * Jointure avec agrégation
     */
    public function verifierDisponibilite() {
        try {
            $query = "SELECT 
                        COUNT(*) AS total_articles,
                        SUM(CASE WHEN pr.stock >= p.quantite THEN 1 ELSE 0 END) AS articles_disponibles,
                        SUM(CASE WHEN pr.stock < p.quantite THEN 1 ELSE 0 END) AS articles_indisponibles
                      FROM panier p
                      INNER JOIN produits pr ON p.produit_id = pr.id";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur vérification disponibilité: " . $e->getMessage());
        }
    }
    
    /**
     * Obtenir les détails complets du panier avec toutes les infos
     * Jointure enrichie
     */
    public function getDetailsComplets() {
        try {
            $query = "SELECT 
                        p.id AS panier_id,
                        p.quantite,
                        p.date_ajout,
                        pr.id AS produit_id,
                        pr.nom,
                        pr.description,
                        pr.prix,
                        pr.stock,
                        pr.image,
                        (p.quantite * pr.prix) AS sous_total,
                        CASE 
                            WHEN pr.stock = 0 THEN 'danger'
                            WHEN pr.stock < p.quantite THEN 'warning'
                            ELSE 'success'
                        END AS alerte_stock
                      FROM panier p
                      INNER JOIN produits pr ON p.produit_id = pr.id
                      ORDER BY p.date_ajout DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur détails complets: " . $e->getMessage());
        }
    }
}
?>
