-- ================================================================
-- Script de migration pour le système de paiement PeaceConnect
-- Version 2.0 - Ajout support Stripe, PayPal et améliorations
-- ================================================================

USE peaceconnect;

-- Étape 1 : Modifier la colonne methode_paiement pour ajouter 'stripe'
ALTER TABLE commandes 
MODIFY COLUMN methode_paiement ENUM('card', 'paypal', 'virement', 'stripe') DEFAULT NULL;

-- Étape 2 : Ajouter les nouvelles colonnes si elles n'existent pas
ALTER TABLE commandes 
ADD COLUMN IF NOT EXISTS payment_intent_id VARCHAR(100) NULL DEFAULT NULL AFTER transaction_id,
ADD COLUMN IF NOT EXISTS payment_method_details TEXT NULL DEFAULT NULL AFTER payment_intent_id;

-- Étape 3 : Créer les index s'ils n'existent pas
CREATE INDEX IF NOT EXISTS idx_numero_commande ON commandes(numero_commande);
CREATE INDEX IF NOT EXISTS idx_statut_paiement ON commandes(statut_paiement);
CREATE INDEX IF NOT EXISTS idx_methode_paiement ON commandes(methode_paiement);

-- Étape 4 : Vérifier la structure finale
DESCRIBE commandes;

-- Résultat attendu :
-- +-------------------------+---------------------------------------------------------------+------+-----+-------------------+
-- | Field                   | Type                                                          | Null | Key | Default           |
-- +-------------------------+---------------------------------------------------------------+------+-----+-------------------+
-- | id                      | int(11)                                                       | NO   | PRI | NULL              |
-- | numero_commande         | varchar(50)                                                   | NO   | UNI | NULL              |
-- | nom_client              | varchar(255)                                                  | NO   |     | NULL              |
-- | email_client            | varchar(255)                                                  | NO   |     | NULL              |
-- | telephone_client        | varchar(20)                                                   | YES  |     | NULL              |
-- | adresse_client          | text                                                          | NO   |     | NULL              |
-- | total                   | decimal(10,2)                                                 | NO   |     | NULL              |
-- | statut                  | enum('en_attente','confirmee','livree','annulee')            | YES  |     | en_attente        |
-- | methode_paiement        | enum('card','paypal','virement','stripe')                    | YES  | MUL | NULL              |
-- | statut_paiement         | enum('en_attente','paye','echoue','rembourse')               | YES  | MUL | en_attente        |
-- | date_paiement           | timestamp                                                     | YES  |     | NULL              |
-- | transaction_id          | varchar(100)                                                  | YES  |     | NULL              |
-- | payment_intent_id       | varchar(100)                                                  | YES  |     | NULL              |
-- | payment_method_details  | text                                                          | YES  |     | NULL              |
-- | date_commande           | timestamp                                                     | NO   |     | CURRENT_TIMESTAMP |
-- | date_livraison          | timestamp                                                     | YES  |     | NULL              |
-- +-------------------------+---------------------------------------------------------------+------+-----+-------------------+

-- Étape 5 : Afficher les statistiques
SELECT 
    methode_paiement,
    statut_paiement,
    COUNT(*) as nombre_commandes,
    SUM(total) as montant_total
FROM commandes
GROUP BY methode_paiement, statut_paiement;

-- Migration terminée avec succès !
SELECT 'Migration terminée avec succès !' as Message;
