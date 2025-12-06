-- ================================================================
-- Script de mise à jour sécurisé pour le système de paiement
-- Compatible MySQL 5.7+ et MariaDB 10.2+
-- ================================================================

USE peaceconnect;

-- Vérifier si la base existe
SELECT 'Base de données peaceconnect trouvée' AS Status;

-- ================================================================
-- ÉTAPE 1 : Backup de la table commandes
-- ================================================================
CREATE TABLE IF NOT EXISTS commandes_backup_paiement AS SELECT * FROM commandes;
SELECT 'Backup créé avec succès' AS Status;

-- ================================================================
-- ÉTAPE 2 : Modifier la colonne methode_paiement
-- ================================================================
SET @query = (
    SELECT IF(
        COUNT(*) > 0,
        'SELECT "La colonne methode_paiement existe déjà avec stripe" AS Status',
        'ALTER TABLE commandes MODIFY COLUMN methode_paiement ENUM("card", "paypal", "virement", "stripe") DEFAULT NULL'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'peaceconnect'
      AND TABLE_NAME = 'commandes'
      AND COLUMN_NAME = 'methode_paiement'
      AND COLUMN_TYPE LIKE '%stripe%'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Si la colonne n'existe pas du tout, la créer
SET @query = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE commandes ADD COLUMN methode_paiement ENUM("card", "paypal", "virement", "stripe") DEFAULT NULL AFTER statut',
        'SELECT "Colonne methode_paiement existe" AS Status'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'peaceconnect'
      AND TABLE_NAME = 'commandes'
      AND COLUMN_NAME = 'methode_paiement'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ================================================================
-- ÉTAPE 3 : Ajouter payment_intent_id si n'existe pas
-- ================================================================
SET @query = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE commandes ADD COLUMN payment_intent_id VARCHAR(100) NULL DEFAULT NULL AFTER transaction_id',
        'SELECT "Colonne payment_intent_id existe déjà" AS Status'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'peaceconnect'
      AND TABLE_NAME = 'commandes'
      AND COLUMN_NAME = 'payment_intent_id'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ================================================================
-- ÉTAPE 4 : Ajouter payment_method_details si n'existe pas
-- ================================================================
SET @query = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE commandes ADD COLUMN payment_method_details TEXT NULL DEFAULT NULL AFTER payment_intent_id',
        'SELECT "Colonne payment_method_details existe déjà" AS Status'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'peaceconnect'
      AND TABLE_NAME = 'commandes'
      AND COLUMN_NAME = 'payment_method_details'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ================================================================
-- ÉTAPE 5 : Créer les index s'ils n'existent pas
-- ================================================================

-- Index sur numero_commande
SET @query = (
    SELECT IF(
        COUNT(*) = 0,
        'CREATE INDEX idx_numero_commande ON commandes(numero_commande)',
        'SELECT "Index idx_numero_commande existe déjà" AS Status'
    )
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = 'peaceconnect'
      AND TABLE_NAME = 'commandes'
      AND INDEX_NAME = 'idx_numero_commande'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index sur statut_paiement
SET @query = (
    SELECT IF(
        COUNT(*) = 0,
        'CREATE INDEX idx_statut_paiement ON commandes(statut_paiement)',
        'SELECT "Index idx_statut_paiement existe déjà" AS Status'
    )
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = 'peaceconnect'
      AND TABLE_NAME = 'commandes'
      AND INDEX_NAME = 'idx_statut_paiement'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index sur methode_paiement
SET @query = (
    SELECT IF(
        COUNT(*) = 0,
        'CREATE INDEX idx_methode_paiement ON commandes(methode_paiement)',
        'SELECT "Index idx_methode_paiement existe déjà" AS Status'
    )
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = 'peaceconnect'
      AND TABLE_NAME = 'commandes'
      AND INDEX_NAME = 'idx_methode_paiement'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ================================================================
-- ÉTAPE 6 : Vérification de la structure
-- ================================================================
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'peaceconnect'
  AND TABLE_NAME = 'commandes'
  AND COLUMN_NAME IN (
      'methode_paiement',
      'statut_paiement',
      'transaction_id',
      'payment_intent_id',
      'payment_method_details',
      'date_paiement'
  )
ORDER BY ORDINAL_POSITION;

-- ================================================================
-- ÉTAPE 7 : Afficher les index créés
-- ================================================================
SELECT 
    INDEX_NAME,
    COLUMN_NAME,
    SEQ_IN_INDEX
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'peaceconnect'
  AND TABLE_NAME = 'commandes'
  AND INDEX_NAME IN (
      'idx_numero_commande',
      'idx_statut_paiement',
      'idx_methode_paiement'
  )
ORDER BY INDEX_NAME, SEQ_IN_INDEX;

-- ================================================================
-- ÉTAPE 8 : Statistiques
-- ================================================================
SELECT 
    'Total des commandes' AS Statistique,
    COUNT(*) AS Valeur
FROM commandes

UNION ALL

SELECT 
    CONCAT('Commandes - ', COALESCE(methode_paiement, 'Sans méthode')) AS Statistique,
    COUNT(*) AS Valeur
FROM commandes
GROUP BY methode_paiement

UNION ALL

SELECT 
    CONCAT('Statut - ', statut_paiement) AS Statistique,
    COUNT(*) AS Valeur
FROM commandes
GROUP BY statut_paiement;

-- ================================================================
-- RÉSULTAT FINAL
-- ================================================================
SELECT 
    '✓ Migration terminée avec succès !' AS Message,
    NOW() AS Date_Execution;

-- ================================================================
-- NOTES
-- ================================================================
-- 1. Un backup de la table a été créé : commandes_backup_paiement
-- 2. Pour restaurer en cas de problème :
--    DROP TABLE commandes;
--    RENAME TABLE commandes_backup_paiement TO commandes;
-- 
-- 3. Pour supprimer le backup après vérification :
--    DROP TABLE IF EXISTS commandes_backup_paiement;
-- ================================================================
