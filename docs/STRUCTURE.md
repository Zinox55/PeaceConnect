# 📁 Structure du Projet PeaceConnect

## Vue d'ensemble

```
PeaceConnect/
├── 📁 config/                     # Configurations du projet
│   ├── config_mail.php            # Configuration email (sensible - gitignore)
│   ├── config_mail.php.example    # Template configuration email
│   └── README.md                  # Documentation config
│
├── 📁 controller/                 # Contrôleurs (API REST)
│   ├── CommandeController.php     # Gestion des commandes
│   ├── EmailController.php        # API mailing
│   ├── PanierController.php       # Gestion du panier
│   ├── ProduitController.php      # Gestion des produits
│   └── UploadController.php       # Upload d'images
│
├── 📁 model/                      # Modèles (logique métier)
│   ├── Commande.php               # Modèle Commande
│   ├── Database.php               # Connexion base de données
│   ├── Mailer.php                 # Service mailing (PHPMailer)
│   ├── Panier.php                 # Modèle Panier
│   └── Produit.php                # Modèle Produit
│
├── 📁 view/                       # Vues (Frontend)
│   ├── 📁 assets/                 # Ressources statiques
│   │   ├── css/                   # Feuilles de style
│   │   ├── fonts/                 # Polices
│   │   ├── img/                   # Images
│   │   │   └── produits/          # Images produits uploadées
│   │   ├── js/                    # Scripts JavaScript
│   │   └── vendor/                # Bibliothèques tierces
│   │
│   ├── 📁 back/                   # Interface administrateur
│   │   ├── dashboard.html         # Tableau de bord
│   │   ├── produits.html          # Gestion produits
│   │   ├── commandes.html         # Gestion commandes
│   │   ├── clients.html           # Gestion clients
│   │   └── stock.html             # Gestion stock
│   │
│   └── 📁 front/                  # Interface client
│       ├── produits.html          # Catalogue produits
│       ├── panier.html            # Panier
│       ├── commande.html          # Formulaire commande
│       └── suivi.html             # Suivi commande
│
├── 📁 sql/                        # Scripts SQL
│   └── fix_foreign_key.sql        # Corrections base de données
│
├── 📁 tests/                      # Tests et diagnostics
│   ├── test_email_commande.php    # Test envoi email
│   ├── test_complet.html          # Tests complets
│   └── diagnostic_cache.html      # Diagnostic cache
│
├── 📁 docs/                       # Documentation
│   ├── GUIDE_COMPLET.md           # Guide complet
│   ├── DÉMARRAGE_RAPIDE.md        # Démarrage rapide
│   └── MAILING_SETUP.md           # Configuration mailing
│
├── 📁 vendor/                     # Dépendances (gitignore)
│   ├── phpmailer/                 # PHPMailer library
│   └── autoload.php               # Autoloader
│
├── config.php                     # Configuration base de données
├── database.sql                   # Structure base de données
├── .gitignore                     # Fichiers ignorés par Git
├── README.md                      # Documentation principale
├── INSTALLATION.md                # Guide d'installation
├── MAILING_README.md              # Documentation mailing
└── index.php                      # Point d'entrée (redirection)
```

## 🎯 Architecture

### Backend (MVC)
- **Model**: Logique métier et accès base de données
- **View**: Templates HTML/CSS/JS
- **Controller**: API REST JSON

### Frontend
- **Back-office**: Interface admin (SB Admin 2)
- **Front-office**: Interface client (Bootstrap)

### Services
- **Mailing**: PHPMailer + Gmail SMTP
- **Upload**: Gestion images produits
- **Panier**: Session PHP

## 🔒 Sécurité

### Fichiers sensibles (gitignore)
- `config/config_mail.php` - Identifiants Gmail SMTP
- `vendor/` - Dépendances PHP
- `view/assets/img/produits/*` - Images uploadées

### Configuration
1. Copier `config/config_mail.php.example` vers `config/config_mail.php`
2. Éditer avec vos informations Gmail
3. Ne jamais commiter le fichier réel

## 🚀 Installation

Voir [INSTALLATION.md](INSTALLATION.md) pour les instructions détaillées.

## 📧 Système de Mailing

Voir [MAILING_README.md](MAILING_README.md) pour la configuration email.

## 📝 Documentation

- [Guide Complet](docs/GUIDE_COMPLET.md)
- [Démarrage Rapide](docs/DÉMARRAGE_RAPIDE.md)
- [Configuration Mailing](docs/MAILING_SETUP.md)

## ⚙️ Technologies

- **Backend**: PHP 7.4+
- **Base de données**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Templates**: Bootstrap 4, SB Admin 2
- **Mailing**: PHPMailer 6.9+
- **Serveur**: Apache (XAMPP)
