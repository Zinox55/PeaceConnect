-- Script de test : Vérification de la décrémentation du stock

-- ====================================
-- ÉTAPE 1 : Préparer les données de test
-- ====================================

-- Insérer un produit de test
INSERT INTO produits (nom, description, prix, stock, image) 
VALUES ('Produit Test Stock', 'Test de décrémentation', 25.99, 100, 'test.jpg');

SET @produit_test_id = LAST_INSERT_ID();

SELECT 'PRODUIT CRÉÉ' as Etape, id, nom, stock FROM produits WHERE id = @produit_test_id;

-- ====================================
-- ÉTAPE 2 : Simuler un ajout au panier
-- ====================================

-- Ajouter 5 unités au panier
INSERT INTO panier (produit_id, quantite) VALUES (@produit_test_id, 5);

SELECT 'PANIER CRÉÉ' as Etape, id, produit_id, quantite FROM panier WHERE produit_id = @produit_test_id;

-- ====================================
-- ÉTAPE 3 : Vérifier le stock AVANT commande
-- ====================================

SELECT 'STOCK AVANT COMMANDE' as Etape, id, nom, stock FROM produits WHERE id = @produit_test_id;
-- Résultat attendu : stock = 100

-- ====================================
-- ÉTAPE 4 : Créer une commande (simulé via PHP)
-- ====================================

-- Note : Cette partie est normalement faite via l'interface web
-- Pour tester manuellement, vous devez utiliser l'interface de commande

SELECT 'INFO' as Message, 
'Allez sur http://localhost:8000/view/front/commande.html pour passer la commande' as Action;

-- ====================================
-- ÉTAPE 5 : Vérifier le stock APRÈS commande
-- ====================================

-- Attendez d'avoir passé la commande via l'interface, puis exécutez :

SELECT 'STOCK APRÈS COMMANDE' as Etape, id, nom, stock FROM produits WHERE id = @produit_test_id;
-- Résultat attendu : stock = 95 (100 - 5)

-- Vérifier les détails de la commande
SELECT 'DÉTAILS COMMANDE' as Etape, 
       dc.commande_id, 
       dc.produit_id, 
       dc.quantite, 
       dc.prix_unitaire,
       c.numero_commande,
       c.statut
FROM details_commande dc
JOIN commandes c ON dc.commande_id = c.id
WHERE dc.produit_id = @produit_test_id
ORDER BY c.date_commande DESC
LIMIT 1;

-- ====================================
-- ÉTAPE 6 : Test de stock insuffisant
-- ====================================

-- Réduire le stock à 2
UPDATE produits SET stock = 2 WHERE id = @produit_test_id;

-- Essayer d'ajouter 5 au panier et commander
INSERT INTO panier (produit_id, quantite) VALUES (@produit_test_id, 5);

-- Lorsque vous essayez de commander via l'interface, vous devez avoir l'erreur :
-- "Stock insuffisant pour : Produit Test Stock"

SELECT 'TEST STOCK INSUFFISANT' as Etape, 
       'Le système devrait refuser la commande car stock (2) < quantité demandée (5)' as Description;

-- ====================================
-- ÉTAPE 7 : Nettoyage
-- ====================================

-- Supprimer les données de test
DELETE FROM panier WHERE produit_id = @produit_test_id;
DELETE FROM details_commande WHERE produit_id = @produit_test_id;
DELETE FROM commandes WHERE id IN (
    SELECT DISTINCT commande_id FROM details_commande WHERE produit_id = @produit_test_id
);
DELETE FROM produits WHERE id = @produit_test_id;

SELECT 'NETTOYAGE TERMINÉ' as Etape, 'Données de test supprimées' as Message;

-- ====================================
-- VÉRIFICATIONS FINALES
-- ====================================

-- Statistiques générales
SELECT 
    'STATISTIQUES' as Rapport,
    (SELECT COUNT(*) FROM produits) as Nb_Produits,
    (SELECT COUNT(*) FROM panier) as Articles_Panier,
    (SELECT COUNT(*) FROM commandes) as Nb_Commandes,
    (SELECT SUM(quantite) FROM panier) as Total_Articles_Panier,
    (SELECT SUM(total) FROM commandes) as CA_Total;

-- Vérifier l'intégrité du stock
SELECT 
    'CONTRÔLE STOCK' as Rapport,
    id,
    nom,
    stock,
    CASE 
        WHEN stock < 0 THEN '⚠️ ANOMALIE : Stock négatif'
        WHEN stock = 0 THEN '⚠️ Rupture de stock'
        WHEN stock < 10 THEN '⚠️ Stock faible'
        ELSE '✅ Stock OK'
    END as Statut
FROM produits
ORDER BY stock ASC;
