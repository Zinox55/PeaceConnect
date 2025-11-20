# 📋 GUIDE COMPLET - PeaceConnect Dashboard

## 🎯 Vue d'ensemble du projet

**PeaceConnect** est une plateforme de commerce solidaire permettant la gestion de produits, commandes et stock.

---

## 🔑 URLs d'accès

### Frontend (Site Public)
- **Page d'accueil** : `http://localhost/PeaceConnect/view/front/index.html`
- **Produits** : `http://localhost/PeaceConnect/view/front/produits.html`
- **Panier** : `http://localhost/PeaceConnect/view/front/panier.html`
- **Commande** : `http://localhost/PeaceConnect/view/front/commande.html`
- **Suivi** : `http://localhost/PeaceConnect/view/front/suivi.html`

### Backend (Administration)
- **Dashboard** : `http://localhost/PeaceConnect/view/back/dashboard.html`
- **Gestion Produits** : `http://localhost/PeaceConnect/view/back/produits.html`
- **Gestion Stock** : `http://localhost/PeaceConnect/view/back/stock.html`

### Tests & Debug
- **Test Images** : `http://localhost/PeaceConnect/test_images.php`

---

## ✅ FONCTIONNALITÉS BACKEND (Dashboard Admin)

### 📊 Dashboard Principal (`dashboard.html`)
✅ **Statistiques en temps réel**
- Total produits
- Stock total
- Produits avec stock faible (< 10)
- Produits en rupture (= 0)

✅ **Actions rapides**
- Ajouter produit
- Gérer produits
- Gérer stock
- Voir le site

✅ **Tableau des alertes**
- Affiche les produits avec stock faible
- Tri automatique par stock croissant
- Actions rapides par produit

---

### 📦 Gestion Produits (`produits.html`)

#### ✅ Liste des produits
- [x] Affichage en tableau avec pagination
- [x] Colonnes : ID, Image, Nom, Description, Prix, Stock, Actions
- [x] Badges colorés pour le stock (Vert/Jaune/Rouge)
- [x] Affichage des images (50x50px)

#### ✅ Ajouter un produit
- [x] Modal avec formulaire complet
- [x] Champs : Nom, Description, Prix, Stock, Image
- [x] Upload d'image avec prévisualisation
- [x] Validation en temps réel des champs
- [x] Messages d'erreur clairs
- [x] Formats supportés : JPG, JPEG, PNG, GIF, WEBP (max 5MB)

#### ✅ Modifier un produit
- [x] Chargement automatique des données
- [x] Modification de tous les champs
- [x] Conservation de l'image existante si non changée
- [x] Upload nouvelle image optionnel
- [x] Aperçu de l'image actuelle

#### ✅ Supprimer un produit
- [x] Confirmation avant suppression
- [x] Gestion des contraintes de clé étrangère
- [x] Message d'erreur si produit utilisé dans commandes
- [x] Suppression en cascade si autorisé

#### ✅ Validation des données
- **Nom** : minimum 3 caractères
- **Prix** : nombre positif, max 2 décimales
- **Stock** : entier positif
- **Image** : formats autorisés, taille max 5MB

---

### 📊 Gestion Stock (`stock.html`)
- [x] Vue dédiée au suivi du stock
- [x] Affichage des niveaux de stock
- [x] Alertes visuelles (couleurs)
- [x] Liens vers gestion produits

---

## ✅ FONCTIONNALITÉS FRONTEND (Site Public)

### 🏠 Page d'accueil (`index.html`)
- [x] Section Hero
- [x] Vision & Mission
- [x] Statistiques
- [x] Navigation vers produits

### 🛍️ Page Produits (`produits.html`)
- [x] Grille responsive (3 colonnes → 2 → 1)
- [x] Affichage des produits avec images
- [x] Prix et descriptions
- [x] Boutons "Ajouter au panier" arrondis
- [x] Gestion des ruptures de stock
- [x] Notation et avis (simulés)

#### Chemins d'images gérés
- Images uploadées (`produit_*.jpeg`) → `view/assets/img/produits/`
- Images de base → `view/assets/img/`
- Fallback sur logo si image manquante

### 🛒 Panier (`panier.html`)
- [x] Affichage des articles
- [x] Modification quantités
- [x] Calcul total automatique
- [x] Bouton vers commande
- [x] Suppression d'articles

### 📝 Commande (`commande.html`)
- [x] Formulaire client complet
- [x] Validation des champs
- [x] Création de commande
- [x] Génération numéro unique
- [x] Redirection vers suivi

### 🔍 Suivi (`suivi.html`)
- [x] Recherche par numéro de commande
- [x] Affichage statut
- [x] Détails de la commande
- [x] Informations client

---

## 🗄️ STRUCTURE BASE DE DONNÉES

### Table `produits`
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- nom (VARCHAR 255)
- description (TEXT)
- prix (DECIMAL 10,2)
- stock (INT)
- image (VARCHAR 255)
- date_creation (TIMESTAMP)
- date_modification (TIMESTAMP)
```

### Table `panier`
```sql
- id (INT, PRIMARY KEY)
- produit_id (INT, FOREIGN KEY → produits.id)
- quantite (INT)
- date_ajout (TIMESTAMP)
```

### Table `commandes`
```sql
- id (INT, PRIMARY KEY)
- numero_commande (VARCHAR 50, UNIQUE)
- nom_client (VARCHAR 255)
- email_client (VARCHAR 255)
- telephone_client (VARCHAR 20)
- adresse_client (TEXT)
- total (DECIMAL 10,2)
- statut (ENUM: en_attente, confirmee, livree, annulee)
- date_commande (TIMESTAMP)
```

### Table `details_commande`
```sql
- id (INT, PRIMARY KEY)
- commande_id (INT, FOREIGN KEY → commandes.id)
- produit_id (INT, FOREIGN KEY → produits.id)
- quantite (INT)
- prix_unitaire (DECIMAL 10,2)
```

---

## 📁 STRUCTURE FICHIERS

```
PeaceConnect/
├── controller/
│   ├── ProduitController.php      ✅ CRUD produits
│   ├── PanierController.php       ✅ Gestion panier
│   ├── CommandeController.php     ✅ Gestion commandes
│   └── UploadController.php       ✅ Upload images
├── model/
│   ├── Produit.php               ✅ Modèle produit
│   ├── Panier.php                ✅ Modèle panier
│   ├── Commande.php              ✅ Modèle commande
│   └── Database.php              ✅ Connexion DB
├── view/
│   ├── back/
│   │   ├── dashboard.html        ✅ Dashboard admin
│   │   ├── produits.html         ✅ Gestion produits
│   │   └── stock.html            ✅ Gestion stock
│   ├── front/
│   │   ├── index.html            ✅ Accueil
│   │   ├── produits.html         ✅ Liste produits
│   │   ├── panier.html           ✅ Panier
│   │   ├── commande.html         ✅ Commande
│   │   └── suivi.html            ✅ Suivi commande
│   └── assets/
│       ├── css/
│       │   ├── style-front.css   ✅ Styles frontend
│       │   ├── style-back.css    ✅ Styles backend
│       │   └── sb-admin-2.min.css
│       ├── img/
│       │   ├── produits/         ✅ Images uploadées
│       │   └── *.jpeg            ✅ Images de base
│       └── js/
│           ├── produit-validation.js  ✅ Backend JS
│           ├── produit-front.js       ✅ Frontend JS
│           ├── panier.js              ✅ Panier JS
│           ├── commande.js            ✅ Commande JS
│           └── suivi.js               ✅ Suivi JS
├── config.php                    ✅ Configuration DB
├── database.sql                  ✅ Structure DB
└── test_images.php              ✅ Test images

```

---

## 🔧 API ENDPOINTS

### ProduitController.php
- `GET /` → Lire tous les produits
- `GET /?action=readOne&id=X` → Lire un produit
- `GET /?action=search&keyword=X` → Rechercher
- `POST /` → Créer un produit
- `PUT /` → Modifier un produit
- `DELETE /` → Supprimer un produit

### PanierController.php
- `GET /` → Lire le panier
- `GET /?action=count` → Nombre d'articles
- `POST /` → Ajouter au panier
- `PUT /` → Modifier quantité
- `DELETE /` → Supprimer article

### CommandeController.php
- `POST /` → Créer une commande
- `GET /?action=readOne&numero=X` → Lire une commande

### UploadController.php
- `POST /` → Upload d'image
  - Retourne : `{success: true, filename: "produit_xxx.jpeg", path: "view/assets/img/produits/..."}`

---

## 🎨 DESIGN & UI

### Couleurs
- **Vert principal** : #5F9E7F
- **Vert foncé** : #4d8a6a
- **Vert clair** : #8BC34A
- **Orange accent** : #FFC107

### Boutons
- **Border-radius** : 25px (arrondi)
- **Padding** : 10px 20px
- **Hover** : Transformation + ombre
- **Full-width** : Dans les cartes produits

### Cartes Produits
- **Grid** : 3 colonnes (desktop) → 2 (tablette) → 1 (mobile)
- **Gap** : 30px
- **Border-radius** : 12px
- **Shadow** : 0 4px 20px rgba(0,0,0,0.08)
- **Hover** : translateY(-5px)

---

## 🧪 TESTS À EFFECTUER

### ✅ Tests Backend
1. Ouvrir `dashboard.html` → Vérifier statistiques
2. Ajouter un produit avec image → Vérifier upload
3. Modifier un produit → Changer image
4. Supprimer un produit → Vérifier confirmation
5. Vérifier validation des champs (nom < 3 car, prix négatif, etc.)

### ✅ Tests Frontend
1. Ouvrir `produits.html` → Vérifier affichage images
2. Ajouter au panier → Vérifier compteur
3. Ouvrir `panier.html` → Modifier quantités
4. Passer commande → Vérifier formulaire
5. Suivre commande → Rechercher par numéro

### ✅ Tests Images
1. Ouvrir `test_images.php` → Vérifier tous les produits
2. Produits de base → Images dans `img/`
3. Nouveaux produits → Images dans `img/produits/`

---

## 🚀 DÉMARRAGE RAPIDE

1. **Démarrer XAMPP**
   - Apache ✅
   - MySQL ✅

2. **Créer la base de données**
   ```sql
   mysql -u root -p < database.sql
   ```

3. **Accéder au dashboard**
   ```
   http://localhost/PeaceConnect/view/back/dashboard.html
   ```

4. **Accéder au site**
   ```
   http://localhost/PeaceConnect/view/front/produits.html
   ```

---

## ✅ CHECKLIST FONCTIONNALITÉS

### Backend
- [x] Dashboard avec statistiques
- [x] CRUD Produits complet
- [x] Upload images avec validation
- [x] Gestion stock avec alertes
- [x] Badges colorés (Vert/Jaune/Rouge)
- [x] Modal responsive
- [x] Validation formulaires
- [x] Messages succès/erreur

### Frontend
- [x] Affichage produits en grille
- [x] Images correctement affichées
- [x] Boutons uniformes et arrondis
- [x] Panier fonctionnel
- [x] Système de commande
- [x] Suivi de commande
- [x] Responsive design

### Images
- [x] Upload dans `produits/`
- [x] Chemin correct dans BDD
- [x] Affichage backend (50x50)
- [x] Affichage frontend (200px)
- [x] Fallback sur logo.png
- [x] Support JPEG, JPG, PNG, GIF, WEBP

---

## 📝 NOTES IMPORTANTES

1. **Images uploadées** : Toujours préfixées par `produit_` + timestamp + uniqid
2. **Chemins relatifs** : Gérés automatiquement selon le préfixe
3. **Validation** : Côté client (JS) ET côté serveur (PHP)
4. **Sécurité** : htmlspecialchars sur toutes les sorties
5. **Base de données** : CASCADE sur delete pour éviter orphelins

---

## 🎉 PROJET COMPLET ET FONCTIONNEL !

Toutes les fonctionnalités principales sont opérationnelles :
- ✅ Dashboard avec stats temps réel
- ✅ CRUD produits avec images
- ✅ Gestion stock avec alertes
- ✅ Site frontend responsive
- ✅ Panier et commandes
- ✅ Upload images sécurisé
