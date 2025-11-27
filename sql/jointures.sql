-- ========================================
-- FICHIER DE JOINTURES - PeaceConnect
-- Toutes les requêtes SQL avec jointures
-- ========================================

USE peaceconnect;

-- ========================================
-- 1. PANIER AVEC PRODUITS
-- ========================================

-- Afficher le panier complet avec les informations des produits
SELECT 
    p.id AS panier_id,
    p.quantite,
    p.date_ajout,
    pr.id AS produit_id,
    pr.nom AS produit_nom,
    pr.description,
    pr.prix,
    pr.stock,
    pr.image,
    (p.quantite * pr.prix) AS sous_total
FROM panier p
INNER JOIN produits pr ON p.produit_id = pr.id
ORDER BY p.date_ajout DESC;

-- Panier avec vérification du stock
SELECT 
    p.id AS panier_id,
    p.quantite AS quantite_demandee,
    pr.nom AS produit_nom,
    pr.prix,
    pr.stock AS stock_disponible,
    CASE 
        WHEN pr.stock >= p.quantite THEN 'Disponible'
        WHEN pr.stock > 0 THEN 'Stock insuffisant'
        ELSE 'Rupture de stock'
    END AS disponibilite,
    (p.quantite * pr.prix) AS sous_total
FROM panier p
INNER JOIN produits pr ON p.produit_id = pr.id;

-- ========================================
-- 2. COMMANDES COMPLÈTES (Triple jointure)
-- ========================================

-- Toutes les commandes avec leurs produits
SELECT 
    c.id AS commande_id,
    c.numero_commande,
    c.nom_client,
    c.email_client,
    c.telephone_client,
    c.adresse_client,
    c.total AS total_commande,
    c.statut,
    c.date_commande,
    pr.id AS produit_id,
    pr.nom AS produit_nom,
    pr.description AS produit_description,
    pr.image AS produit_image,
    dc.quantite,
    dc.prix_unitaire,
    (dc.quantite * dc.prix_unitaire) AS sous_total
FROM commandes c
INNER JOIN details_commande dc ON c.id = dc.commande_id
INNER JOIN produits pr ON dc.produit_id = pr.id
ORDER BY c.date_commande DESC, pr.nom;

-- Commande spécifique par numéro avec ses détails
SELECT 
    c.id AS commande_id,
    c.numero_commande,
    c.nom_client,
    c.email_client,
    c.telephone_client,
    c.adresse_client,
    c.total,
    c.statut,
    c.date_commande,
    dc.id AS detail_id,
    dc.quantite,
    dc.prix_unitaire,
    pr.nom AS produit_nom,
    pr.image AS produit_image,
    (dc.quantite * dc.prix_unitaire) AS sous_total
FROM commandes c
LEFT JOIN details_commande dc ON c.id = dc.commande_id
LEFT JOIN produits pr ON dc.produit_id = pr.id
WHERE c.numero_commande = 'CMD-2024-000001';

-- ========================================
-- 3. STATISTIQUES ET AGRÉGATIONS
-- ========================================

-- Nombre de produits par commande
SELECT 
    c.id,
    c.numero_commande,
    c.nom_client,
    c.statut,
    c.total,
    c.date_commande,
    COUNT(dc.id) AS nombre_produits,
    SUM(dc.quantite) AS quantite_totale
FROM commandes c
LEFT JOIN details_commande dc ON c.id = dc.commande_id
GROUP BY c.id, c.numero_commande, c.nom_client, c.statut, c.total, c.date_commande
ORDER BY c.date_commande DESC;

-- Statistiques de vente par produit
SELECT 
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
GROUP BY pr.id, pr.nom, pr.prix, pr.stock, pr.image
ORDER BY quantite_vendue DESC;

-- Produits les plus populaires dans le panier
SELECT 
    pr.id,
    pr.nom,
    pr.prix,
    pr.image,
    pr.stock,
    COUNT(p.id) AS nombre_ajouts_panier,
    SUM(p.quantite) AS quantite_totale_panier
FROM produits pr
LEFT JOIN panier p ON pr.id = p.produit_id
GROUP BY pr.id, pr.nom, pr.prix, pr.image, pr.stock
HAVING nombre_ajouts_panier > 0
ORDER BY nombre_ajouts_panier DESC;

-- ========================================
-- 4. COMMANDES PAR STATUT
-- ========================================

-- Commandes en attente avec leurs produits
SELECT 
    c.numero_commande,
    c.nom_client,
    c.email_client,
    c.telephone_client,
    c.date_commande,
    c.total,
    pr.nom AS produit,
    pr.image,
    dc.quantite,
    dc.prix_unitaire
FROM commandes c
INNER JOIN details_commande dc ON c.id = dc.commande_id
INNER JOIN produits pr ON dc.produit_id = pr.id
WHERE c.statut = 'en_attente'
ORDER BY c.date_commande DESC;

-- Commandes confirmées
SELECT 
    c.numero_commande,
    c.nom_client,
    c.date_commande,
    COUNT(dc.id) AS nb_produits,
    c.total
FROM commandes c
LEFT JOIN details_commande dc ON c.id = dc.commande_id
WHERE c.statut = 'confirmee'
GROUP BY c.id, c.numero_commande, c.nom_client, c.date_commande, c.total
ORDER BY c.date_commande DESC;

-- Commandes livrées
SELECT 
    c.numero_commande,
    c.nom_client,
    c.date_commande,
    c.total,
    COUNT(dc.id) AS nb_produits
FROM commandes c
LEFT JOIN details_commande dc ON c.id = dc.commande_id
WHERE c.statut = 'livree'
GROUP BY c.id, c.numero_commande, c.nom_client, c.date_commande, c.total
ORDER BY c.date_commande DESC;

-- ========================================
-- 5. RAPPORTS ET ANALYSES
-- ========================================

-- Chiffre d'affaires total par statut
SELECT 
    c.statut,
    COUNT(c.id) AS nombre_commandes,
    SUM(c.total) AS chiffre_affaires,
    AVG(c.total) AS panier_moyen
FROM commandes c
GROUP BY c.statut
ORDER BY chiffre_affaires DESC;

-- Top 5 des produits les plus vendus
SELECT 
    pr.id,
    pr.nom,
    pr.image,
    SUM(dc.quantite) AS quantite_vendue,
    SUM(dc.quantite * dc.prix_unitaire) AS chiffre_affaires
FROM produits pr
INNER JOIN details_commande dc ON pr.id = dc.produit_id
GROUP BY pr.id, pr.nom, pr.image
ORDER BY quantite_vendue DESC
LIMIT 5;

-- Produits jamais commandés
SELECT 
    pr.id,
    pr.nom,
    pr.prix,
    pr.stock,
    pr.image
FROM produits pr
LEFT JOIN details_commande dc ON pr.id = dc.produit_id
WHERE dc.id IS NULL
ORDER BY pr.nom;

-- ========================================
-- 6. RAPPORT COMPLET (UNION)
-- ========================================

-- Vue d'ensemble : Produits dans le panier ET dans les commandes
SELECT 
    'Panier' AS source,
    pr.nom AS produit,
    pr.image,
    p.quantite,
    pr.prix AS prix_unitaire,
    (p.quantite * pr.prix) AS total,
    NULL AS numero_commande,
    NULL AS client,
    NULL AS statut,
    p.date_ajout AS date
FROM panier p
INNER JOIN produits pr ON p.produit_id = pr.id

UNION ALL

SELECT 
    'Commande' AS source,
    pr.nom AS produit,
    pr.image,
    dc.quantite,
    dc.prix_unitaire,
    (dc.quantite * dc.prix_unitaire) AS total,
    c.numero_commande,
    c.nom_client AS client,
    c.statut,
    c.date_commande AS date
FROM details_commande dc
INNER JOIN produits pr ON dc.produit_id = pr.id
INNER JOIN commandes c ON dc.commande_id = c.id
ORDER BY date DESC;

-- ========================================
-- 7. VUES UTILES
-- ========================================

-- Vue : Panier complet
CREATE OR REPLACE VIEW vue_panier_complet AS
SELECT 
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
        WHEN pr.stock >= p.quantite THEN 'Disponible'
        WHEN pr.stock > 0 THEN 'Stock insuffisant'
        ELSE 'Rupture'
    END AS disponibilite
FROM panier p
INNER JOIN produits pr ON p.produit_id = pr.id;

-- Vue : Commandes détaillées
CREATE OR REPLACE VIEW vue_commandes_details AS
SELECT 
    c.id AS commande_id,
    c.numero_commande,
    c.nom_client,
    c.email_client,
    c.telephone_client,
    c.adresse_client,
    c.total AS total_commande,
    c.statut,
    c.date_commande,
    dc.id AS detail_id,
    dc.quantite,
    dc.prix_unitaire,
    pr.id AS produit_id,
    pr.nom AS produit_nom,
    pr.image AS produit_image,
    (dc.quantite * dc.prix_unitaire) AS sous_total
FROM commandes c
LEFT JOIN details_commande dc ON c.id = dc.commande_id
LEFT JOIN produits pr ON dc.produit_id = pr.id;

-- Vue : Statistiques produits
CREATE OR REPLACE VIEW vue_statistiques_produits AS
SELECT 
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
GROUP BY pr.id, pr.nom, pr.prix, pr.stock, pr.image;

-- ========================================
-- FIN DU FICHIER
-- ========================================
