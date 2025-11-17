<?php
require_once __DIR__ . '/Database.php';

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
        $this->db = Database::getInstance()->getConnection();
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
}
?>
