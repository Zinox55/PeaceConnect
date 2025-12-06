-- Ajouter les champs de paiement à la table commandes
ALTER TABLE commandes 
ADD COLUMN methode_paiement ENUM('card', 'paypal', 'virement') DEFAULT NULL AFTER statut,
ADD COLUMN statut_paiement ENUM('en_attente', 'paye', 'echoue', 'rembourse') DEFAULT 'en_attente' AFTER methode_paiement,
ADD COLUMN date_paiement TIMESTAMP NULL DEFAULT NULL AFTER statut_paiement,
ADD COLUMN transaction_id VARCHAR(100) NULL DEFAULT NULL AFTER date_paiement;

-- Index pour améliorer les performances
CREATE INDEX idx_statut_paiement ON commandes(statut_paiement);
CREATE INDEX idx_methode_paiement ON commandes(methode_paiement);
