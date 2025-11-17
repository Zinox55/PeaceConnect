# 🧪 Guide de Test du Panier

## Étape 1 : Préparation

### 1.1 Vérifier la base de données
```bash
mysql -u root -p peaceconnect
```

```sql
-- Vérifier que les tables existent
SHOW TABLES;

-- Vérifier les produits disponibles
SELECT id, nom, prix, stock FROM produits;

-- Vérifier le panier actuel (doit être vide au début)
SELECT * FROM panier;
```

### 1.2 Lancer le serveur
```bash
cd "c:\Users\user\Desktop\2A\PeaceConnect - Copie - Copie"
php -S localhost:8000
```

## Étape 2 : Test d'Ajout au Panier

### 2.1 Ajouter des produits depuis le BackOffice
1. Ouvrir : `http://localhost:8000/view/back/produits.html`
2. Ajouter au moins 3 produits avec :
   - Nom (min 3 caractères)
   - Prix (ex: 19.99)
   - Stock (ex: 50)
   - Image (optionnel)

### 2.2 Tester l'ajout au panier
1. Ouvrir : `http://localhost:8000/view/front/produits.html`
2. Cliquer sur "Ajouter au panier" pour le premier produit
3. **Vérifier** :
   - ✅ Notification verte "Produit ajouté au panier"
   - ✅ Le compteur du panier augmente (header)

### 2.3 Vérifier dans la base de données
```sql
-- Le produit doit apparaître dans la table panier
SELECT p.id, pr.nom, p.quantite, p.date_ajout 
FROM panier p 
JOIN produits pr ON p.produit_id = pr.id;
```

**Résultat attendu :**
```
+----+---------------+----------+---------------------+
| id | nom           | quantite | date_ajout          |
+----+---------------+----------+---------------------+
|  1 | Produit Test  |        1 | 2025-11-17 14:30:00 |
+----+---------------+----------+---------------------+
```

## Étape 3 : Test de la Page Panier

### 3.1 Ouvrir la page panier
1. Cliquer sur "Panier" dans le header OU
2. Aller directement : `http://localhost:8000/view/front/panier.html`

### 3.2 Vérifier l'affichage
**Le panier doit afficher :**
- ✅ Nombre de produits en haut
- ✅ Image du produit
- ✅ Nom du produit
- ✅ Prix unitaire
- ✅ Champ quantité (modifiable)
- ✅ Sous-total (prix × quantité)
- ✅ Bouton supprimer (🗑️)
- ✅ Total général en bas
- ✅ Bouton "Vider le panier"
- ✅ Bouton "Passer commande"

### 3.3 Test : Ajouter le même produit 2 fois
1. Retourner sur `produits.html`
2. Cliquer à nouveau sur "Ajouter au panier" pour le **même produit**
3. Retourner sur `panier.html`

**Vérification en BDD :**
```sql
SELECT produit_id, quantite FROM panier;
```

**Résultat attendu :**
- ✅ Le produit n'est **pas dupliqué**
- ✅ La quantité est passée de 1 à 2
- ✅ Le sous-total a doublé

## Étape 4 : Test de Modification de Quantité

### 4.1 Modifier la quantité
1. Dans `panier.html`, changer la quantité (ex: 5)
2. Cliquer ailleurs ou appuyer sur Entrée

**Vérifier :**
- ✅ Le sous-total se met à jour automatiquement
- ✅ Le total général se recalcule
- ✅ Pas de notification (mise à jour silencieuse)

**Vérification en BDD :**
```sql
SELECT produit_id, quantite FROM panier WHERE produit_id = 1;
```

**Résultat attendu :** quantité = 5

### 4.2 Tester une quantité invalide
1. Mettre quantité = 0 ou -1
2. Le système doit :
   - ✅ Refuser la valeur
   - ✅ Remettre la valeur à 1

## Étape 5 : Test de Suppression

### 5.1 Supprimer un produit
1. Cliquer sur l'icône 🗑️ d'un produit
2. **Vérifier :**
   - ✅ Notification "Produit retiré du panier"
   - ✅ La ligne disparaît immédiatement
   - ✅ Le total se recalcule
   - ✅ Le compteur diminue

**Vérification en BDD :**
```sql
SELECT COUNT(*) as nb_produits FROM panier;
```

### 5.2 Vider complètement le panier
1. Ajouter plusieurs produits
2. Cliquer sur "Vider le panier"
3. **Vérifier :**
   - ✅ Tous les produits disparaissent
   - ✅ Message "Votre panier est vide"
   - ✅ Bouton "Découvrir nos produits"
   - ✅ Compteur = 0

**Vérification en BDD :**
```sql
SELECT COUNT(*) as nb_produits FROM panier;
-- Doit retourner 0
```

## Étape 6 : Test de Persistance

### 6.1 Tester la persistance des données
1. Ajouter 2-3 produits au panier
2. **Fermer complètement le navigateur**
3. Rouvrir et aller sur `panier.html`

**Résultat attendu :**
- ✅ Les produits sont toujours là (stockés en BDD, pas en localStorage)

### 6.2 Tester avec un autre navigateur
1. Ajouter des produits sur Chrome
2. Ouvrir Firefox et aller sur `panier.html`

**Résultat attendu :**
- ✅ Le panier est vide (session différente)
- ⚠️ C'est normal : le panier actuel n'utilise pas de sessions PHP

## Étape 7 : Test du Workflow Complet

### 7.1 Scénario complet
```
1. Ajouter 3 produits différents au panier
2. Modifier la quantité du 2ème produit (× 3)
3. Supprimer le 3ème produit
4. Vérifier que le total est correct
5. Cliquer sur "Passer commande"
6. Remplir le formulaire de commande
7. Valider la commande
8. Vérifier que le panier est vidé automatiquement
```

### 7.2 Calcul attendu
```
Produit 1 : 19.99 € × 1 = 19.99 €
Produit 2 : 29.99 € × 3 = 89.97 €
Produit 3 : (supprimé)
─────────────────────────────────
TOTAL              = 109.96 €
```

## Étape 8 : Test d'Erreurs

### 8.1 Tester avec un produit inexistant
```javascript
// Dans la console du navigateur
fetch('http://localhost:8000/controller/PanierController.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({produit_id: 9999, quantite: 1})
}).then(r => r.json()).then(console.log);
```

**Résultat attendu :**
```json
{
  "success": false,
  "message": "Erreur lors de l'ajout"
}
```

### 8.2 Tester sans connexion à la base
1. Arrêter MySQL
2. Essayer d'ajouter au panier

**Résultat attendu :**
- ✅ Notification rouge d'erreur
- ✅ Message "Erreur de connexion"

## Étape 9 : Vérifications Finales

### 9.1 Console du navigateur
Ouvrir les DevTools (F12) → Console

**Aucune erreur JavaScript ne doit apparaître**

### 9.2 Requêtes réseau
Onglet Network (Réseau) → Recharger la page

**Vérifier les appels API :**
- ✅ `PanierController.php` → Status 200
- ✅ Réponse JSON valide
- ✅ Content-Type: application/json

### 9.3 Responsive Design
Tester sur différentes tailles :
- 📱 Mobile (375px)
- 📱 Tablette (768px)
- 💻 Desktop (1920px)

**Le panier doit s'adapter correctement**

## 🐛 Problèmes Courants et Solutions

### Problème 1 : "Le produit n'apparaît pas dans le panier"
**Solution :**
```sql
-- Vérifier que le produit existe
SELECT * FROM produits WHERE id = 1;

-- Vérifier la table panier
SELECT * FROM panier;

-- Vérifier les erreurs PHP
tail -f /path/to/php-error.log
```

### Problème 2 : "Erreur 500 lors de l'ajout"
**Solution :**
1. Vérifier `model/Database.php` (connexion)
2. Vérifier les permissions de la base
3. Activer l'affichage des erreurs :
```php
// En haut de PanierController.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Problème 3 : "Le total ne se met pas à jour"
**Solution :**
1. Vider le cache du navigateur (Ctrl + F5)
2. Vérifier la console JavaScript
3. Tester manuellement :
```javascript
// Dans la console
loadPanier();
```

### Problème 4 : "Headers déjà envoyés"
**Solution :**
- Supprimer tous les espaces avant `<?php` et après `?>`
- Vérifier l'encodage des fichiers (UTF-8 sans BOM)

## ✅ Checklist Finale

- [ ] Les produits s'affichent correctement
- [ ] "Ajouter au panier" fonctionne
- [ ] Les notifications s'affichent
- [ ] Le compteur se met à jour
- [ ] La page panier affiche les produits
- [ ] Modifier la quantité fonctionne
- [ ] Supprimer un produit fonctionne
- [ ] Vider le panier fonctionne
- [ ] Le total est correct
- [ ] Passer commande vide le panier
- [ ] Aucune erreur en console
- [ ] Responsive sur mobile

## 📊 Statistiques Attendues

Après ces tests, vous devriez avoir :
```sql
-- Statistiques du panier
SELECT 
    COUNT(*) as nb_lignes,
    SUM(quantite) as total_articles,
    (SELECT SUM(pr.prix * p.quantite) FROM panier p JOIN produits pr ON p.produit_id = pr.id) as valeur_panier
FROM panier;
```

---

**✨ Si tous les tests passent : Votre système de panier est fonctionnel !**

Pour tout problème, vérifiez :
1. Les logs PHP
2. La console JavaScript
3. L'onglet Network des DevTools
4. La structure de la base de données
