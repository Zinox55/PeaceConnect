# 🚀 GUIDE DE DÉMARRAGE RAPIDE - PeaceConnect Dashboard

## ⚡ Lancement en 3 Minutes

### 1️⃣ Prérequis
- ✅ XAMPP installé
- ✅ Apache démarré
- ✅ MySQL démarré
- ✅ Base de données `peaceconnect` importée

### 2️⃣ Démarrage
```bash
# Ouvrir le dossier dans le navigateur
http://localhost/PeaceConnect/
```

### 3️⃣ Points d'Entrée

#### 🧪 Page de Test (RECOMMANDÉ)
```
http://localhost/PeaceConnect/test_dashboard.html
```
**Description :** Page avec liens vers toutes les fonctionnalités + checklist

#### 🏠 Dashboard Admin
```
http://localhost/PeaceConnect/view/back/dashboard.html
```
**Description :** Tableau de bord principal avec statistiques

#### 🌐 Site Frontend
```
http://localhost/PeaceConnect/view/front/produits.html
```
**Description :** Site client pour voir les produits

---

## 📊 Navigation Dashboard

### Sidebar (Menu de Gauche)
- 🏠 **Dashboard** → Vue d'ensemble
- 📦 **Produits** → Gérer produits
- 📊 **Stock** → Mettre à jour stocks
- 🛒 **Commandes** → Gérer commandes
- 👥 **Clients** → Voir clients

### Accès Rapide
Depuis le Dashboard, utilisez les 6 boutons d'actions rapides :
1. Ajouter Produit
2. Gérer Produits
3. Gérer Stock
4. Voir Commandes
5. Gérer Clients
6. Voir Site

---

## 🎯 Fonctionnalités par Page

### 📊 Dashboard
**Ce que vous voyez :**
- 8 cartes de statistiques
- Tableau des produits en stock faible
- Boutons d'actions rapides

**Actualisation :** Automatique au chargement

### 📦 Produits
**Actions disponibles :**
- ➕ Ajouter un produit (bouton en haut)
- ✏️ Modifier (cliquer sur Edit)
- 🗑️ Supprimer (cliquer sur Delete)

**Upload d'image :**
1. Cliquer "Ajouter un produit"
2. Remplir le formulaire
3. Cliquer "Choisir une image"
4. Sélectionner l'image
5. Cliquer "Enregistrer"

### 📊 Stock
**Mise à jour :**
1. Modifier la quantité dans l'input
2. Cliquer "Mettre à jour"
3. Confirmer

**Badges de couleur :**
- 🔴 Rouge = Rupture (0)
- 🟡 Jaune = Faible (<10)
- 🟢 Vert = Normal (≥10)

### 🛒 Commandes
**Gestion :**
- 👁️ Voir détails → Cliquer sur l'œil
- ✅ Confirmer → Cliquer sur le check
- 🚚 Livrer → Cliquer sur le camion
- ❌ Annuler → Cliquer sur la croix

**Filtrer :**
- Utiliser le dropdown en haut à droite
- Sélectionner le statut voulu

**Statuts :**
- 🟡 En Attente
- 🔵 Confirmée
- 🟢 Livrée
- 🔴 Annulée

### 👥 Clients
**Recherche :**
1. Taper dans la barre de recherche
2. Recherche par nom, email ou téléphone

**Voir historique :**
1. Cliquer sur une carte client
2. Modal avec toutes ses commandes
3. Total commandes + total dépensé

---

## 🧪 Checklist de Test

### ✅ Tests Produits
- [ ] Ajouter un produit
- [ ] Upload une image
- [ ] Modifier le produit
- [ ] Vérifier l'image s'affiche
- [ ] Supprimer le produit

### ✅ Tests Stock
- [ ] Voir la liste des stocks
- [ ] Modifier une quantité
- [ ] Cliquer "Mettre à jour"
- [ ] Vérifier le badge de couleur change

### ✅ Tests Commandes
- [ ] Voir la liste des commandes
- [ ] Cliquer sur "Voir détails"
- [ ] Changer le statut d'une commande
- [ ] Filtrer par statut

### ✅ Tests Clients
- [ ] Voir la liste des clients
- [ ] Rechercher un client
- [ ] Voir son historique
- [ ] Vérifier les totaux

### ✅ Tests Navigation
- [ ] Cliquer sur chaque lien sidebar
- [ ] Vérifier l'état actif
- [ ] Tester sur mobile
- [ ] Tester le bouton "Voir le site"

---

## 🔧 Dépannage

### Les statistiques ne s'affichent pas
**Solution :** Vérifier que les APIs fonctionnent
```
http://localhost/PeaceConnect/controller/ProduitController.php
http://localhost/PeaceConnect/controller/CommandeController.php
```

### Les images ne s'affichent pas
**Solutions :**
1. Vérifier que le dossier `view/assets/img/produits/` existe
2. Vérifier les permissions du dossier
3. Vérifier le chemin dans la base de données

### Erreur "404 Not Found"
**Solution :** Vérifier que XAMPP est démarré et que vous êtes dans le bon dossier
```
E:\xampp\htdocs\PeaceConnect\
```

### La base de données ne répond pas
**Solution :**
1. Ouvrir phpMyAdmin
2. Vérifier que la base `peaceconnect` existe
3. Vérifier les tables (produits, commandes, etc.)

---

## 📱 URLs Importantes

### Backend (Admin)
```
Dashboard:  http://localhost/PeaceConnect/view/back/dashboard.html
Produits:   http://localhost/PeaceConnect/view/back/produits.html
Stock:      http://localhost/PeaceConnect/view/back/stock.html
Commandes:  http://localhost/PeaceConnect/view/back/commandes.html
Clients:    http://localhost/PeaceConnect/view/back/clients.html
```

### Frontend (Client)
```
Produits:   http://localhost/PeaceConnect/view/front/produits.html
Panier:     http://localhost/PeaceConnect/view/front/panier.html
Commande:   http://localhost/PeaceConnect/view/front/commande.html
Suivi:      http://localhost/PeaceConnect/view/front/suivi.html
```

### APIs
```
Produits:   http://localhost/PeaceConnect/controller/ProduitController.php
Commandes:  http://localhost/PeaceConnect/controller/CommandeController.php
Panier:     http://localhost/PeaceConnect/controller/PanierController.php
Upload:     http://localhost/PeaceConnect/controller/UploadController.php
```

### Tests
```
Test Dashboard: http://localhost/PeaceConnect/test_dashboard.html
Test Images:    http://localhost/PeaceConnect/test_images.php
```

---

## 🎨 Personnalisation

### Changer les couleurs
Modifier dans chaque fichier HTML la section `<style>` :
```css
/* Couleur principale */
#5F9E7F → Votre couleur

/* Badges */
.badge-stock-success { background: #1cc88a; } → Votre couleur
```

### Changer le logo
Remplacer le fichier :
```
view/assets/img/logo.png
```

### Modifier le titre
Dans chaque page, changer :
```html
<title>Gestion Produits - PeaceConnect Admin</title>
```

---

## 📚 Documentation Complète

Pour plus de détails, consulter :
- `DASHBOARD_README.md` - Documentation technique complète
- `RÉSUMÉ_COMPLET.md` - Récapitulatif du projet
- `GUIDE_COMPLET.md` - Guide utilisateur détaillé

---

## 💡 Conseils d'Utilisation

### Pour les Administrateurs
1. **Commencer par le Dashboard** pour avoir une vue d'ensemble
2. **Gérer les stocks** régulièrement pour éviter les ruptures
3. **Traiter les commandes en attente** en priorité
4. **Surveiller les clients** récurrents

### Pour le Développement
1. **Toujours tester** dans XAMPP d'abord
2. **Faire des backups** de la base de données
3. **Vérifier la console** pour les erreurs JavaScript
4. **Tester sur mobile** aussi

### Bonnes Pratiques
- ✅ Uploader des images optimisées (<5 MB)
- ✅ Utiliser des noms de produits clairs
- ✅ Mettre à jour les stocks après chaque vente
- ✅ Traiter les commandes rapidement
- ✅ Vérifier les statistiques régulièrement

---

## 🎯 Raccourcis Clavier

### Navigation
- `Alt + D` → Dashboard (dans la sidebar)
- `Alt + P` → Produits
- `Alt + S` → Stock
- `Alt + C` → Commandes
- `Alt + L` → Clients

### Actions
- `Ctrl + S` → Sauvegarder (dans les modals)
- `Esc` → Fermer modal
- `Enter` → Confirmer action

---

## 📊 Indicateurs de Performance

### À Surveiller
- 🟢 Stock normal : >10 unités
- 🟡 Stock faible : 1-9 unités
- 🔴 Rupture : 0 unité
- 🔵 Commandes en attente : traiter sous 24h

### Objectifs
- ✅ 0 rupture de stock
- ✅ Toutes commandes traitées en <24h
- ✅ Tous clients satisfaits
- ✅ Croissance mensuelle positive

---

## 🎉 Félicitations !

Vous êtes maintenant prêt à utiliser le **Dashboard PeaceConnect** !

Pour toute question, consultez la documentation ou vérifiez les fichiers de test.

**Bon travail ! 🕊️**

---

*Guide créé le 2025 - Version 1.0.0*
