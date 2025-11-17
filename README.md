# PeaceConnect - CRUD Complet

## 📋 Description
Application web complète de gestion de produits solidaires avec architecture MVC, CRUD fonctionnel et gestion de panier.

## ✅ Fonctionnalités

### BackOffice (Administration)
- ✅ **CREATE** : Ajouter des produits
- ✅ **READ** : Lister tous les produits
- ✅ **UPDATE** : Modifier les produits
- ✅ **DELETE** : Supprimer les produits
- ✅ Validation JavaScript sans HTML5
- ✅ Messages en temps réel

### FrontOffice (Public)
- ✅ Affichage dynamique des produits
- ✅ Ajout au panier (base de données)
- ✅ Gestion du panier (modifier, supprimer)
- ✅ Calcul automatique du total
- ✅ Compteur de panier en temps réel

## 🚀 Installation

### 1. Base de données
```sql
mysql -u root -p
CREATE DATABASE peaceconnect;
exit;
mysql -u root -p peaceconnect < database.sql
```

### 2. Configuration
Modifier `model/Database.php` si nécessaire :
```php
private $host = 'localhost';
private $db_name = 'peaceconnect';
private $username = 'root';
private $password = '';
```

### 3. Lancer le serveur
```bash
php -S localhost:8000
```

## 📂 Structure

```
PeaceConnect/
├── model/
│   ├── Database.php      # Connexion PDO (Singleton)
│   ├── Produit.php       # CRUD Produits
│   └── Panier.php        # CRUD Panier
├── controller/
│   ├── ProduitController.php  # API REST Produits
│   └── PanierController.php   # API REST Panier
├── view/
│   ├── back/
│   │   └── produits.html      # BackOffice Admin
│   ├── front/
│   │   ├── produits.html      # Liste produits
│   │   └── panier.html        # Panier
│   └── assets/js/
│       ├── produit-validation.js  # Validation BackOffice
│       ├── produit-front.js       # FrontOffice produits
│       └── panier.js              # Gestion panier
└── database.sql          # Script SQL
```

## 🔐 Sécurité

- ✅ PDO avec prepared statements
- ✅ Validation double (client + serveur)
- ✅ Sanitization des données
- ✅ Protection XSS
- ✅ Pattern Singleton

## 📡 API REST

### Produits
- `GET /controller/ProduitController.php` - Liste tous
- `GET /controller/ProduitController.php?action=readOne&id=1` - Un produit
- `POST /controller/ProduitController.php` - Créer
- `PUT /controller/ProduitController.php` - Modifier
- `DELETE /controller/ProduitController.php` - Supprimer

### Panier
- `GET /controller/PanierController.php` - Voir le panier
- `GET /controller/PanierController.php?action=count` - Compter articles
- `POST /controller/PanierController.php` - Ajouter au panier
- `PUT /controller/PanierController.php` - Modifier quantité
- `DELETE /controller/PanierController.php` - Supprimer un article
- `DELETE /controller/PanierController.php?action=vider` - Vider panier

## 🎯 Validation

### Règles
- **Nom** : Minimum 3 caractères
- **Prix** : Nombre positif, max 2 décimales
- **Stock** : Nombre entier positif

### Sans HTML5
Toute la validation est faite en JavaScript pur et PHP (pas d'attributs HTML5 comme required, min, max, etc.)

## 🗄️ Base de données

### Tables
- **produits** : id, nom, description, prix, stock, image
- **panier** : id, produit_id, quantite
- **commandes** : id, numero_commande, client, total, statut
- **details_commande** : id, commande_id, produit_id, quantite, prix

## 📝 URLs

- BackOffice : `http://localhost:8000/view/back/produits.html`
- FrontOffice Produits : `http://localhost:8000/view/front/produits.html`
- Panier : `http://localhost:8000/view/front/panier.html`

## ✔️ Conformité

| Exigence | Status |
|----------|--------|
| CRUD FrontOffice et BackOffice | ✅ |
| Templates intégrés | ✅ |
| Validation sans HTML5 | ✅ |
| Architecture MVC | ✅ |
| POO | ✅ |
| PDO obligatoire | ✅ |

---

**Développé pour PeaceConnect** 🌍
