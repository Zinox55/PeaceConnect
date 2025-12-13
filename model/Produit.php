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
    private $code_barre;
    private $note;
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
    public function getCodeBarre() { return $this->code_barre; }
    public function getNote() { return $this->note; }
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
    
    public function setCodeBarre($code_barre) { 
        $code_barre = ($code_barre === null) ? '' : trim((string)$code_barre);
        if ($code_barre === '') {
            // Use NULL in DB for empty barcode to avoid duplicate-empty-string unique constraint
            $this->code_barre = null;
            return;
        }

        $code_barre = htmlspecialchars(strip_tags($code_barre));
        if (strlen($code_barre) < 3) {
            throw new Exception("Le code-barre doit contenir au moins 3 caractères");
        }

        $this->code_barre = $code_barre;
    }

    public function setNote($note) {
        if ($note === '' || $note === null) {
            $this->note = 0;
            return;
        }
        if (!is_numeric($note) || $note < 0 || $note > 5) {
            throw new Exception("La note doit être un entier entre 0 et 5");
        }
        $this->note = intval($note);
    }
    
    public function setImage($image) { 
        $this->image = htmlspecialchars(strip_tags(trim($image)));
    }
    
    // CREATE
    public function create() {
        try {
            // Générer un code-barre automatique si non fourni
            if (empty($this->code_barre)) {
                $this->code_barre = 'EAN' . strtoupper(uniqid());
            }
            
            $query = "INSERT INTO produits (nom, description, prix, stock, code_barre, note, image) 
                      VALUES (:nom, :description, :prix, :stock, :code_barre, :note, :image)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nom', $this->nom, PDO::PARAM_STR);
            $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
            $stmt->bindValue(':prix', $this->prix);
            $stmt->bindValue(':stock', $this->stock, PDO::PARAM_INT);
            if ($this->code_barre === null) {
                $stmt->bindValue(':code_barre', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':code_barre', $this->code_barre, PDO::PARAM_STR);
            }
            $stmt->bindValue(':note', $this->note, PDO::PARAM_INT);
            $stmt->bindValue(':image', $this->image, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                $this->id = $this->db->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'Duplicate entry') !== false && strpos($msg, 'code_barre') !== false) {
                throw new Exception("Erreur création: code-barre dupliqué. Choisissez un code-barre unique ou laissez vide pour générer automatiquement.");
            }
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
                $this->code_barre = $row['code_barre'] ?? null;
                $this->note = isset($row['note']) ? intval($row['note']) : 0;
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
                          stock = :stock, code_barre = :code_barre, note = :note, image = :image 
                      WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nom', $this->nom, PDO::PARAM_STR);
            $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
            $stmt->bindValue(':prix', $this->prix);
            $stmt->bindValue(':stock', $this->stock, PDO::PARAM_INT);
            if ($this->code_barre === null) {
                $stmt->bindValue(':code_barre', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':code_barre', $this->code_barre, PDO::PARAM_STR);
            }
            $stmt->bindValue(':note', $this->note, PDO::PARAM_INT);
            $stmt->bindValue(':image', $this->image, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Handle duplicate code_barre (empty or duplicate) with clearer message
            $msg = $e->getMessage();
            if (strpos($msg, 'Duplicate entry') !== false && strpos($msg, 'code_barre') !== false) {
                throw new Exception("Erreur mise à jour: code-barre dupliqué. Assurez-vous que le champ `code_barre` est unique ou laissez-le vide.");
            }
            // Auto-recover for missing 'note' column (older DB schema)
            if (strpos($msg, "Unknown column 'note'") !== false || strpos($msg, "Unknown column\"note\"") !== false) {
                // Attempt to add the column and retry once
                try {
                    $alter = "ALTER TABLE produits ADD COLUMN note TINYINT UNSIGNED NOT NULL DEFAULT 0";
                    $this->db->exec($alter);
                    // retry the update
                    $stmt = $this->db->prepare(
                        "UPDATE produits 
                         SET nom = :nom, description = :description, prix = :prix, 
                             stock = :stock, code_barre = :code_barre, note = :note, image = :image 
                         WHERE id = :id"
                    );
                    $stmt->bindValue(':nom', $this->nom, PDO::PARAM_STR);
                    $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
                    $stmt->bindValue(':prix', $this->prix);
                    $stmt->bindValue(':stock', $this->stock, PDO::PARAM_INT);
                    if ($this->code_barre === null) {
                        $stmt->bindValue(':code_barre', null, PDO::PARAM_NULL);
                    } else {
                        $stmt->bindValue(':code_barre', $this->code_barre, PDO::PARAM_STR);
                    }
                    $stmt->bindValue(':note', $this->note, PDO::PARAM_INT);
                    $stmt->bindValue(':image', $this->image, PDO::PARAM_STR);
                    $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
                    return $stmt->execute();
                } catch (PDOException $e2) {
                    throw new Exception("Erreur mise à jour (après tentative de migration): " . $e2->getMessage());
                }
            }

            throw new Exception("Erreur mise à jour: " . $e->getMessage());
        }
    }

    /**
     * Mettre à jour uniquement le stock d'un produit (utilisé par le dashboard pour éviter
     * d'exécuter la requête UPDATE complète lorsqu'on ne change que la quantité en stock).
     * Cela évite des erreurs SQL si d'autres champs manquent dans la payload.
     * @param int $id
     * @param int $stock
     * @return bool
     */
    public function updateStock($id, $stock) {
        try {
            $query = "UPDATE produits SET stock = :stock WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':stock', (int)$stock, PDO::PARAM_INT);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur mise à jour stock: " . $e->getMessage());
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
    
    /**
     * RECHERCHE AVANCÉE avec filtres multiples
     * @param array $params Paramètres de recherche:
     *   - keyword: recherche dans nom et description
     *   - prix_min, prix_max: fourchette de prix
     *   - stock_min, stock_max: fourchette de stock
     *   - statut_stock: 'rupture'|'faible'|'ok'
     *   - sort: 'date_desc'|'date_asc'|'prix_asc'|'prix_desc'|'stock_asc'|'stock_desc'|'nom_asc'|'nom_desc'
     *   - page: numéro de page (défaut: 1)
     *   - limit: nombre d'éléments par page (défaut: 20, max: 100)
     * @return array Résultats avec pagination
     */
    public function advancedSearch($params = []) {
        try {
            // Construction de la requête SQL
            $sql = "SELECT * FROM produits WHERE 1=1";
            $binds = [];
            $countSql = "SELECT COUNT(*) as total FROM produits WHERE 1=1";
            
            // Filtre par mot-clé
            if (!empty($params['keyword'])) {
                $condition = " AND (nom LIKE :keyword1 OR description LIKE :keyword2)";
                $sql .= $condition;
                $countSql .= $condition;
                $binds[':keyword1'] = '%' . $params['keyword'] . '%';
                $binds[':keyword2'] = '%' . $params['keyword'] . '%';
            }
            
            // Filtre par prix minimum
            if (isset($params['prix_min']) && is_numeric($params['prix_min']) && $params['prix_min'] !== '') {
                $condition = " AND prix >= :prix_min";
                $sql .= $condition;
                $countSql .= $condition;
                $binds[':prix_min'] = $params['prix_min'];
            }
            
            // Filtre par prix maximum
            if (isset($params['prix_max']) && is_numeric($params['prix_max']) && $params['prix_max'] !== '') {
                $condition = " AND prix <= :prix_max";
                $sql .= $condition;
                $countSql .= $condition;
                $binds[':prix_max'] = $params['prix_max'];
            }
            
            // Filtre par stock minimum
            if (isset($params['stock_min']) && is_numeric($params['stock_min']) && $params['stock_min'] !== '') {
                $condition = " AND stock >= :stock_min";
                $sql .= $condition;
                $countSql .= $condition;
                $binds[':stock_min'] = (int)$params['stock_min'];
            }
            
            // Filtre par stock maximum
            if (isset($params['stock_max']) && is_numeric($params['stock_max']) && $params['stock_max'] !== '') {
                $condition = " AND stock <= :stock_max";
                $sql .= $condition;
                $countSql .= $condition;
                $binds[':stock_max'] = (int)$params['stock_max'];
            }
            
            // Filtre par statut de stock
            if (!empty($params['statut_stock'])) {
                if ($params['statut_stock'] === 'rupture') {
                    $condition = " AND stock = 0";
                    $sql .= $condition;
                    $countSql .= $condition;
                } elseif ($params['statut_stock'] === 'faible') {
                    $condition = " AND stock > 0 AND stock < 10";
                    $sql .= $condition;
                    $countSql .= $condition;
                } elseif ($params['statut_stock'] === 'ok') {
                    $condition = " AND stock >= 10";
                    $sql .= $condition;
                    $countSql .= $condition;
                }
            }
            
            // Compter le total avant pagination
            $stmtCount = $this->db->prepare($countSql);
            foreach ($binds as $key => $val) {
                if (is_int($val)) {
                    $stmtCount->bindValue($key, $val, PDO::PARAM_INT);
                } else {
                    $stmtCount->bindValue($key, $val, PDO::PARAM_STR);
                }
            }
            $stmtCount->execute();
            $total = (int)$stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Tri
            $sort = $params['sort'] ?? 'date_desc';
            $orderMap = [
                'date_desc' => 'date_creation DESC',
                'date_asc' => 'date_creation ASC',
                'prix_asc' => 'prix ASC',
                'prix_desc' => 'prix DESC',
                'stock_asc' => 'stock ASC',
                'stock_desc' => 'stock DESC',
                'nom_asc' => 'nom ASC',
                'nom_desc' => 'nom DESC'
            ];
            $sql .= " ORDER BY " . ($orderMap[$sort] ?? $orderMap['date_desc']);
            
            // Pagination
            $page = max(1, intval($params['page'] ?? 1));
            $limit = min(100, max(1, intval($params['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;
            $sql .= " LIMIT :limit OFFSET :offset";
            
            // Exécution de la requête
            $stmt = $this->db->prepare($sql);
            foreach ($binds as $key => $val) {
                if (is_int($val)) {
                    $stmt->bindValue($key, $val, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $val, PDO::PARAM_STR);
                }
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'items' => $items,
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => (int)ceil($total / $limit),
                'filters_applied' => [
                    'keyword' => $params['keyword'] ?? null,
                    'prix_min' => $params['prix_min'] ?? null,
                    'prix_max' => $params['prix_max'] ?? null,
                    'stock_min' => $params['stock_min'] ?? null,
                    'stock_max' => $params['stock_max'] ?? null,
                    'statut_stock' => $params['statut_stock'] ?? null,
                    'sort' => $sort
                ]
            ];
        } catch (PDOException $e) {
            throw new Exception("Erreur recherche avancée: " . $e->getMessage());
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

    /**
     * Récupérer les produits avec stock faible (alerte)
     * @param int $seuil Seuil de stock (par défaut 10)
     * @return array Produits en alerte
     */
    public function getProduitsStockFaible($seuil = 10) {
        try {
            $query = "SELECT 
                        id,
                        nom,
                        description,
                        prix,
                        stock,
                        image,
                        CASE 
                            WHEN stock = 0 THEN 'Rupture de stock'
                            WHEN stock <= :seuil1 THEN 'Stock critique'
                            ELSE 'Stock normal'
                        END AS etat_stock,
                        CASE 
                            WHEN stock = 0 THEN 'danger'
                            WHEN stock <= :seuil2 THEN 'warning'
                            ELSE 'success'
                        END AS type_alerte
                      FROM produits
                      WHERE stock <= :seuil3
                      ORDER BY stock ASC, nom ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':seuil1', $seuil, PDO::PARAM_INT);
            $stmt->bindValue(':seuil2', $seuil, PDO::PARAM_INT);
            $stmt->bindValue(':seuil3', $seuil, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lecture stock faible: " . $e->getMessage());
        }
    }

    /**
     * Compter le nombre de produits en alerte de stock
     * @param int $seuil Seuil de stock
     * @return int Nombre de produits en alerte
     */
    public function countProduitsStockFaible($seuil = 10) {
        try {
            $query = "SELECT COUNT(*) as total FROM produits WHERE stock <= :seuil";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':seuil', $seuil, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            throw new Exception("Erreur comptage stock faible: " . $e->getMessage());
        }
    }
}
?>
