<?php
require_once __DIR__ . '/../config.php';

/**
 * Classe Commande - Gestion des commandes
 */
class Commande {
    private $id;
    private $numero_commande;
    private $nom_client;
    private $email_client;
    private $telephone_client;
    private $adresse_client;
    private $total;
    private $statut;
    private $db;
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getNumeroCommande() { return $this->numero_commande; }
    public function getNomClient() { return $this->nom_client; }
    public function getEmailClient() { return $this->email_client; }
    public function getTelephoneClient() { return $this->telephone_client; }
    public function getAdresseClient() { return $this->adresse_client; }
    public function getTotal() { return $this->total; }
    public function getStatut() { return $this->statut; }
    
    // Setters
    public function setId($id) { $this->id = intval($id); }
    public function setNumeroCommande($numero) { $this->numero_commande = htmlspecialchars($numero); }
    public function setNomClient($nom) {
        if (empty(trim($nom))) throw new Exception("Le nom est obligatoire");
        $this->nom_client = htmlspecialchars(strip_tags(trim($nom)));
    }
    public function setEmailClient($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email invalide");
        }
        $this->email_client = htmlspecialchars($email);
    }
    public function setTelephoneClient($telephone) {
        $this->telephone_client = htmlspecialchars(strip_tags(trim($telephone)));
    }
    public function setAdresseClient($adresse) {
        if (empty(trim($adresse))) throw new Exception("L'adresse est obligatoire");
        $this->adresse_client = htmlspecialchars(strip_tags(trim($adresse)));
    }
    public function setTotal($total) { $this->total = floatval($total); }
    public function setStatut($statut) { $this->statut = $statut; }
    
    // Générer un numéro de commande unique
    private function genererNumeroCommande() {
        $annee = date('Y');
        $numero = 'CMD-' . $annee . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        // Vérifier l'unicité
        $query = "SELECT id FROM commandes WHERE numero_commande = :numero";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':numero', $numero);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $this->genererNumeroCommande(); // Récursif si existe déjà
        }
        
        return $numero;
    }
    
    // Créer une commande à partir du panier
    public function creerDepuisPanier($data) {
        try {
            $this->db->beginTransaction();
            
            // Récupérer les articles du panier
            $query = "SELECT p.produit_id, pr.nom, pr.prix, p.quantite, pr.stock
                      FROM panier p
                      INNER JOIN produits pr ON p.produit_id = pr.id";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($articles)) {
                throw new Exception("Le panier est vide");
            }
            
            // Vérifier le stock
            foreach ($articles as $article) {
                if ($article['stock'] < $article['quantite']) {
                    throw new Exception("Stock insuffisant pour : " . $article['nom']);
                }
            }
            
            // Calculer le total
            $total = 0;
            foreach ($articles as $article) {
                $total += $article['prix'] * $article['quantite'];
            }
            
            // Créer la commande
            $this->setNomClient($data['nom']);
            $this->setEmailClient($data['email']);
            $this->setTelephoneClient($data['telephone'] ?? '');
            $this->setAdresseClient($data['adresse']);
            $this->setTotal($total);
            $this->numero_commande = $this->genererNumeroCommande();
            
            $query = "INSERT INTO commandes 
                      (numero_commande, nom_client, email_client, telephone_client, adresse_client, total, statut)
                      VALUES (:numero, :nom, :email, :telephone, :adresse, :total, 'en_attente')";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':numero', $this->numero_commande);
            $stmt->bindParam(':nom', $this->nom_client);
            $stmt->bindParam(':email', $this->email_client);
            $stmt->bindParam(':telephone', $this->telephone_client);
            $stmt->bindParam(':adresse', $this->adresse_client);
            $stmt->bindParam(':total', $this->total);
            $stmt->execute();
            
            $this->id = $this->db->lastInsertId();
            
            // Créer les détails de commande et mettre à jour le stock
            foreach ($articles as $article) {
                $query = "INSERT INTO details_commande 
                          (commande_id, produit_id, quantite, prix_unitaire)
                          VALUES (:commande_id, :produit_id, :quantite, :prix)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':commande_id', $this->id, PDO::PARAM_INT);
                $stmt->bindParam(':produit_id', $article['produit_id'], PDO::PARAM_INT);
                $stmt->bindParam(':quantite', $article['quantite'], PDO::PARAM_INT);
                $stmt->bindParam(':prix', $article['prix']);
                $stmt->execute();
                
                // Décrémenter le stock
                $query = "UPDATE produits SET stock = stock - :quantite WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':quantite', $article['quantite'], PDO::PARAM_INT);
                $stmt->bindParam(':id', $article['produit_id'], PDO::PARAM_INT);
                $stmt->execute();
            }
            
            // Vider le panier
            $query = "DELETE FROM panier";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    // Lire toutes les commandes
    public function lireTout() {
        try {
            $query = "SELECT * FROM commandes ORDER BY date_commande DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture: " . $e->getMessage());
        }
    }
    
    // Lire une commande par numéro
    public function lireParNumero($numero_commande) {
        try {
            $query = "SELECT c.*, 
                      (SELECT COUNT(*) FROM details_commande WHERE commande_id = c.id) as nb_articles
                      FROM commandes c
                      WHERE c.numero_commande = :numero
                      LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':numero', $numero_commande);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture: " . $e->getMessage());
        }
    }
    
    // Lire les détails d'une commande
    public function lireDetails($commande_id) {
        try {
            $query = "SELECT dc.*, pr.nom, pr.image
                      FROM details_commande dc
                      INNER JOIN produits pr ON dc.produit_id = pr.id
                      WHERE dc.commande_id = :commande_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':commande_id', $commande_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture détails: " . $e->getMessage());
        }
    }
    
    // Mettre à jour le statut
    public function mettreAJourStatut($commande_id, $statut) {
        try {
            $statuts_valides = ['en_attente', 'confirmee', 'livree', 'annulee'];
            if (!in_array($statut, $statuts_valides)) {
                throw new Exception("Statut invalide");
            }
            
            $query = "UPDATE commandes SET statut = :statut WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':statut', $statut);
            $stmt->bindParam(':id', $commande_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur mise à jour: " . $e->getMessage());
        }
    }
    
    // ========================================
    // NOUVELLES MÉTHODES AVEC JOINTURES
    // ========================================
    
    /**
     * Lire toutes les commandes avec le nombre de produits
     * Jointure : commandes ← details_commande (avec COUNT)
     */
    public function lireToutAvecDetails() {
        try {
            $query = "SELECT 
                        c.id,
                        c.numero_commande,
                        c.nom_client,
                        c.email_client,
                        c.telephone_client,
                        c.adresse_client,
                        c.statut,
                        c.total,
                        c.date_commande,
                        COUNT(dc.id) AS nombre_produits,
                        SUM(dc.quantite) AS quantite_totale
                      FROM commandes c
                      LEFT JOIN details_commande dc ON c.id = dc.commande_id
                      GROUP BY c.id, c.numero_commande, c.nom_client, c.email_client, 
                               c.telephone_client, c.adresse_client, c.statut, c.total, c.date_commande
                      ORDER BY c.date_commande DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture: " . $e->getMessage());
        }
    }
    
    /**
     * Lire une commande complète avec tous ses produits
     * Triple jointure : commandes → details_commande → produits
     */
    public function lireCommandeComplete($numero_commande) {
        try {
            // Informations de la commande
            $query = "SELECT * FROM commandes WHERE numero_commande = :numero LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':numero', $numero_commande);
            $stmt->execute();
            $commande = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$commande) {
                return null;
            }
            
            // Détails avec produits (triple jointure)
            $query = "SELECT 
                        dc.id AS detail_id,
                        dc.quantite,
                        dc.prix_unitaire,
                        pr.id AS produit_id,
                        pr.nom AS produit_nom,
                        pr.description,
                        pr.image,
                        (dc.quantite * dc.prix_unitaire) AS sous_total
                      FROM details_commande dc
                      INNER JOIN produits pr ON dc.produit_id = pr.id
                      WHERE dc.commande_id = :commande_id
                      ORDER BY pr.nom";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':commande_id', $commande['id'], PDO::PARAM_INT);
            $stmt->execute();
            $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $commande['details'] = $details;
            $commande['nombre_produits'] = count($details);
            
            return $commande;
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture complète: " . $e->getMessage());
        }
    }
    
    /**
     * Lire les commandes par statut avec leurs produits
     * Triple jointure avec filtrage
     */
    public function lireParStatut($statut) {
        try {
            $query = "SELECT 
                        c.id AS commande_id,
                        c.numero_commande,
                        c.nom_client,
                        c.email_client,
                        c.telephone_client,
                        c.date_commande,
                        c.total,
                        pr.id AS produit_id,
                        pr.nom AS produit_nom,
                        pr.image AS produit_image,
                        dc.quantite,
                        dc.prix_unitaire
                      FROM commandes c
                      INNER JOIN details_commande dc ON c.id = dc.commande_id
                      INNER JOIN produits pr ON dc.produit_id = pr.id
                      WHERE c.statut = :statut
                      ORDER BY c.date_commande DESC, pr.nom";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':statut', $statut);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture par statut: " . $e->getMessage());
        }
    }
    
    /**
     * Obtenir les statistiques globales des commandes
     * Agrégation avec GROUP BY sur statut
     */
    public function getStatistiquesGlobales() {
        try {
            $query = "SELECT 
                        c.statut,
                        COUNT(c.id) AS nombre_commandes,
                        SUM(c.total) AS chiffre_affaires,
                        AVG(c.total) AS panier_moyen,
                        MIN(c.total) AS commande_min,
                        MAX(c.total) AS commande_max
                      FROM commandes c
                      GROUP BY c.statut
                      ORDER BY chiffre_affaires DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur statistiques: " . $e->getMessage());
        }
    }
    
    /**
     * Obtenir le résumé d'une commande pour le dashboard
     * Jointure optimisée
     */
    public function getResume($commande_id) {
        try {
            $query = "SELECT 
                        c.id,
                        c.numero_commande,
                        c.nom_client,
                        c.email_client,
                        c.statut,
                        c.total,
                        c.date_commande,
                        COUNT(dc.id) AS nb_produits,
                        SUM(dc.quantite) AS quantite_totale
                      FROM commandes c
                      LEFT JOIN details_commande dc ON c.id = dc.commande_id
                      WHERE c.id = :commande_id
                      GROUP BY c.id, c.numero_commande, c.nom_client, c.email_client, 
                               c.statut, c.total, c.date_commande";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':commande_id', $commande_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur résumé: " . $e->getMessage());
        }
    }
    
    /**
     * Obtenir toutes les commandes d'un client
     * Jointure avec filtrage sur email
     */
    public function lireParClient($email_client) {
        try {
            $query = "SELECT 
                        c.id,
                        c.numero_commande,
                        c.total,
                        c.statut,
                        c.date_commande,
                        COUNT(dc.id) AS nombre_produits
                      FROM commandes c
                      LEFT JOIN details_commande dc ON c.id = dc.commande_id
                      WHERE c.email_client = :email
                      GROUP BY c.id, c.numero_commande, c.total, c.statut, c.date_commande
                      ORDER BY c.date_commande DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email_client);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture par client: " . $e->getMessage());
        }
    }
}
?>
