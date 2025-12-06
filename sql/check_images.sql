-- Script de vérification des images des produits
-- Date: 2025-12-03

USE peaceconnect;

-- 1. Voir tous les produits avec leur statut d'image
SELECT 
    id,
    nom,
    image,
    CASE 
        WHEN image IS NULL THEN '❌ NULL'
        WHEN image = '' THEN '❌ VIDE'
        WHEN image LIKE 'produit_%' THEN '✅ Nouvelle image (dans /produits/)'
        ELSE '⚠️ Ancienne image (dans /img/)'
    END AS statut_image,
    stock,
    prix
FROM produits
ORDER BY id;

-- 2. Compter les produits par type d'image
SELECT 
    CASE 
        WHEN image IS NULL THEN 'NULL'
        WHEN image = '' THEN 'VIDE'
        WHEN image LIKE 'produit_%' THEN 'Nouvelle'
        ELSE 'Ancienne'
    END AS type_image,
    COUNT(*) AS nombre
FROM produits
GROUP BY type_image;

-- 3. Voir les produits dans le panier avec leurs images
SELECT 
    p.id AS panier_id,
    pr.id AS produit_id,
    pr.nom,
    pr.image,
    CASE 
        WHEN pr.image IS NULL THEN '❌ NULL'
        WHEN pr.image = '' THEN '❌ VIDE'
        WHEN pr.image LIKE 'produit_%' THEN '✅ OK'
        ELSE '⚠️ Ancienne'
    END AS statut,
    p.quantite
FROM panier p
INNER JOIN produits pr ON p.produit_id = pr.id;

-- 4. Mettre à jour les produits avec images anciennes vers le logo par défaut (OPTIONNEL)
-- Décommentez si vous voulez forcer l'utilisation du logo pour les anciennes images
/*
UPDATE produits 
SET image = 'logo.png' 
WHERE image IS NOT NULL 
  AND image != '' 
  AND image NOT LIKE 'produit_%'
  AND image NOT IN ('logo.png');
*/

-- 5. Voir les produits sans image
SELECT id, nom, prix, stock
FROM produits
WHERE image IS NULL OR image = '';
