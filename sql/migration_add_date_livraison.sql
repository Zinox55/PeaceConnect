-- Migration: Ajouter la colonne date_livraison à la table commandes
-- Date: 2025-12-03

USE peaceconnect;

-- Ajouter la colonne date_livraison si elle n'existe pas
ALTER TABLE commandes 
ADD COLUMN IF NOT EXISTS date_livraison TIMESTAMP NULL DEFAULT NULL 
AFTER date_commande;

-- Mettre à jour les commandes déjà livrées avec la date de commande comme date de livraison
UPDATE commandes 
SET date_livraison = date_commande 
WHERE statut = 'livree' AND date_livraison IS NULL;
