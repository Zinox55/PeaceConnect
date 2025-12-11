-- Ajouter la colonne note (rating) à la table produits
ALTER TABLE produits 
ADD COLUMN note TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER code_barre;

-- Initialiser une note par défaut
UPDATE produits SET note = 0 WHERE note IS NULL;
