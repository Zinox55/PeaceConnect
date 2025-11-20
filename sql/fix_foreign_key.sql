-- Script pour corriger la contrainte de clé étrangère
USE peaceconnect;

-- Désactiver la vérification des clés étrangères temporairement
SET FOREIGN_KEY_CHECKS = 0;

-- Supprimer l'ancienne contrainte
ALTER TABLE details_commande 
DROP FOREIGN KEY details_commande_ibfk_2;

-- Ajouter la nouvelle contrainte avec ON DELETE CASCADE
ALTER TABLE details_commande 
ADD CONSTRAINT details_commande_ibfk_2 
FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE;

-- Réactiver la vérification des clés étrangères
SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Contrainte de clé étrangère mise à jour avec succès!' as Message;
