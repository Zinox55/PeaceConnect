-- Ajouter la colonne code_barre à la table produits
ALTER TABLE produits 
ADD COLUMN code_barre VARCHAR(50) UNIQUE AFTER stock;

-- Générer des codes-barres automatiques pour les produits existants
UPDATE produits SET code_barre = CONCAT('EAN', LPAD(id, 10, '0')) WHERE code_barre IS NULL;
