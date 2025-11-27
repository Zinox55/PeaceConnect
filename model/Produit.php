<?php
require_once __DIR__ . '/../config.php';

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
        $this->db = config::getConnexion();
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
            // Propager l'exception PDO pour que le contrôleur puisse la gérer
            throw $e;
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
    
    // ========================================
    // NOUVELLES MÉTHODES AVEC JOINTURES
    // ========================================
    
    /**
     * Obtenir les statistiques de vente d'un produit
     * Jointure : produits ← details_commande ← commandes
     */
    public function getStatistiques($produit_id) {
        try {
            $query = "SELECT 
                        pr.id,
                        pr.nom,
                        pr.prix,
                        pr.stock,
                        pr.image,
                        COALESCE(COUNT(DISTINCT dc.commande_id), 0) AS nombre_commandes,
                        COALESCE(SUM(dc.quantite), 0) AS quantite_vendue,
                        COALESCE(SUM(dc.quantite * dc.prix_unitaire), 0) AS chiffre_affaires,
                        CASE 
                            WHEN pr.stock = 0 THEN 'Rupture'
                            WHEN pr.stock < 10 THEN 'Stock faible'
                            ELSE 'En stock'
                        END AS etat_stock
                      FROM produits pr
                      LEFT JOIN details_commande dc ON pr.id = dc.produit_id
                      WHERE pr.id = :produit_id
                      GROUP BY pr.id, pr.nom, pr.prix, pr.stock, pr.image";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':produit_id', $produit_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur statistiques: " . $e->getMessage());
        }
    }
    
    /**
     * Obtenir les produits les plus vendus
     * Jointure : produits ← details_commande
     */
    public function getTopVentes($limit = 5) {
        try {
            $query = "SELECT 
                        pr.id,
                        pr.nom,
                        pr.prix,
                        pr.image,
                        pr.stock,
                        SUM(dc.quantite) AS quantite_vendue,
                        SUM(dc.quantite * dc.prix_unitaire) AS chiffre_affaires
                      FROM produits pr
                      INNER JOIN details_commande dc ON pr.id = dc.produit_id
                      GROUP BY pr.id, pr.nom, pr.prix, pr.image, pr.stock
                      ORDER BY quantite_vendue DESC
                      LIMIT :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur top ventes: " . $e->getMessage());
        }
    }
    
    /**
     * Obtenir les produits jamais commandés
     * Jointure : produits LEFT JOIN details_commande (avec WHERE IS NULL)
     */
    public function getNonCommandes() {
        try {
            $query = "SELECT 
                        pr.id,
                        pr.nom,
                        pr.prix,
                        pr.stock,
                        pr.image,
                        pr.date_creation
                      FROM produits pr
                      LEFT JOIN details_commande dc ON pr.id = dc.produit_id
                      WHERE dc.id IS NULL
                      ORDER BY pr.nom";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur produits non commandés: " . $e->getMessage());
        }
    }
    
    /**
     * Obtenir tous les produits avec leurs statistiques
     * Jointure complexe avec agrégations
     */
    public function getAllAvecStatistiques() {
        try {
            $query = "SELECT 
                        pr.id,
                        pr.nom,
                        pr.description,
                        pr.prix,
                        pr.stock,
                        pr.image,
                        pr.date_creation,
                        COALESCE(COUNT(DISTINCT dc.commande_id), 0) AS nombre_commandes,
                        COALESCE(SUM(dc.quantite), 0) AS quantite_vendue,
                        COALESCE(SUM(dc.quantite * dc.prix_unitaire), 0) AS chiffre_affaires,
                        CASE 
                            WHEN pr.stock = 0 THEN 'Rupture'
                            WHEN pr.stock < 10 THEN 'Stock faible'
                            ELSE 'En stock'
                        END AS etat_stock
                      FROM produits pr
                      LEFT JOIN details_commande dc ON pr.id = dc.produit_id
                      GROUP BY pr.id, pr.nom, pr.description, pr.prix, pr.stock, pr.image, pr.date_creation
                      ORDER BY pr.date_creation DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture avec stats: " . $e->getMessage());
        }
    }

    /**
     * Statistiques pour le dashboard produits
     * - Répartition par état de stock (pie/doughnut)
     * - Ventes mensuelles (quantité) sur les 6 derniers mois (line)
     */
    public function getDashboardStats() {
        try {
            // Répartition par état de stock
            $sqlRepartition = "SELECT 
                    CASE 
                        WHEN stock = 0 THEN 'Rupture'
                        WHEN stock < 10 THEN 'Stock faible'
                        ELSE 'En stock'
                    END AS label,
                    COUNT(*) AS count
                FROM produits
                GROUP BY label";
            $stmt1 = $this->db->prepare($sqlRepartition);
            $stmt1->execute();
            $repartition = $stmt1->fetchAll(PDO::FETCH_ASSOC);

            // Ventes mensuelles sur 6 derniers mois
            $sqlMonthly = "SELECT 
                    DATE_FORMAT(c.date_commande, '%Y-%m') AS mois,
                    COALESCE(SUM(dc.quantite), 0) AS quantite
                FROM commandes c
                LEFT JOIN details_commande dc ON c.id = dc.commande_id
                WHERE c.date_commande >= DATE_ADD(LAST_DAY(CURDATE()), INTERVAL -5 MONTH)
                GROUP BY mois
                ORDER BY mois";
            $stmt2 = $this->db->prepare($sqlMonthly);
            $stmt2->execute();
            $monthlyRaw = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            // Normaliser pour inclure tous les 6 mois
            $months = [];
            for ($i = 5; $i >= 0; $i--) {
                $months[] = date('Y-m', strtotime("-{$i} months"));
            }
            $monthlyMap = [];
            foreach ($monthlyRaw as $row) {
                $monthlyMap[$row['mois']] = (int)$row['quantite'];
            }
            $monthly = [];
            foreach ($months as $m) {
                $monthly[] = [
                    'mois' => $m,
                    'quantite' => isset($monthlyMap[$m]) ? $monthlyMap[$m] : 0
                ];
            }

            return [
                'success' => true,
                'repartition' => $repartition,
                'monthly' => $monthly
            ];
        } catch (PDOException $e) {
            throw new Exception("Erreur statistiques dashboard: " . $e->getMessage());
        }
    }
}
?>
