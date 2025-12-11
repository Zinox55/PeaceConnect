-- Script de correction des anciennes images
-- Date: 2025-12-03
-- Ce script met à jour les chemins d'images des produits existants

USE peaceconnect;

-- 1. Voir les produits avec leurs images actuelles
SELECT id, nom, image, 
       CASE 
           WHEN image IS NULL OR image = '' THEN '❌ Pas d\'image'
           WHEN image LIKE 'produit_%' THEN '✅ Image récente'
           ELSE '⚠️ Ancienne image'
       END AS statut
FROM produits
ORDER BY id;

-- 2. Mettre à jour les produits initiaux avec les bonnes images
-- Ces produits ont été créés lors de l'installation initiale

-- Nourriture pour les Affamés
UPDATE produits 
SET image = 'téléchargement.jpeg'
WHERE nom = 'Nourriture pour les Affamés' 
  AND (image IS NULL OR image = '' OR image NOT LIKE 'produit_%');

-- Éducation pour les Enfants
UPDATE produits 
SET image = 'enfants-classe.jpg.jpeg'
WHERE nom = 'Éducation pour les Enfants'
  AND (image IS NULL OR image = '' OR image NOT LIKE 'produit_%');

-- Soins de Santé
UPDATE produits 
SET image = 'téléchargement (2).jpeg'
WHERE nom = 'Soins de Santé'
  AND (image IS NULL OR image = '' OR image NOT LIKE 'produit_%');

-- Eau Pure
UPDATE produits 
SET image = 'téléchargement (1).jpeg'
WHERE nom = 'Eau Pure'
  AND (image IS NULL OR image = '' OR image NOT LIKE 'produit_%');

-- Soutien aux Moyens de Subsistance
UPDATE produits 
SET image = 'téléchargement (3).jpeg'
WHERE nom = 'Soutien aux Moyens de Subsistance'
  AND (image IS NULL OR image = '' OR image NOT LIKE 'produit_%');

-- Logement Digne
UPDATE produits 
SET image = 'téléchargement (4).jpeg'
WHERE nom = 'Logement Digne'
  AND (image IS NULL OR image = '' OR image NOT LIKE 'produit_%');

-- 3. Vérifier les résultats
SELECT id, nom, image, 
       CASE 
           WHEN image IS NULL OR image = '' THEN '❌ Pas d\'image'
           WHEN image LIKE 'produit_%' THEN '✅ Image uploadée'
           ELSE '✅ Image fixe'
       END AS statut
FROM produits
ORDER BY id;

-- 4. Afficher un résumé
SELECT 
    CASE 
        WHEN image IS NULL OR image = '' THEN 'Sans image'
        WHEN image LIKE 'produit_%' THEN 'Image uploadée'
        ELSE 'Image fixe'
    END AS type_image,
    COUNT(*) AS nombre
FROM produits
GROUP BY type_image;
