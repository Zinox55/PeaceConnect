# 📁 Organisation du Projet PeaceConnect

## ✅ Structure Nettoyée et Organisée

Le projet a été réorganisé pour une meilleure clarté et maintenabilité.

### 📂 Structure des Dossiers

```
PeaceConnect/
├── 📄 config.php              # Configuration base de données
├── 📄 database.sql            # Structure de la base de données
├── 📄 README.md               # Documentation principale
├── 📄 .gitignore              # Fichiers ignorés par Git
│
├── 📁 config/                 # Configuration système
│   ├── config_mail.php        # Configuration email
│   └── config_paiement.php    # Configuration paiement
│
├── 📁 controller/             # Contrôleurs MVC
│   ├── CommandeController.php
│   ├── PaiementController.php
│   ├── PanierController.php
│   ├── ProduitController.php
│   ├── EmailController.php
│   ├── StatistiquesController.php
│   └── UploadController.php
│
├── 📁 model/                  # Modèles MVC
│   ├── Commande.php
│   ├── Panier.php
│   ├── Produit.php
│   └── Mailer.php
│
├── 📁 view/                   # Vues MVC
│   ├── front/                 # Interface client
│   │   ├── produits.html
│   │   ├── panier.html
│   │   ├── suivi.html
│   │   ├── commande.html
│   │   ├── paiement.html
│   │   ├── confirmation.html
│   │   ├── hero-navbar.css
│   │   └── navbar.js
│   ├── back/                  # Interface admin
│   │   └── dashboard.html
│   └── assets/                # Ressources
│       ├── css/
│       ├── js/
│       └── img/
│
├── 📁 docs/                   # 📚 Documentation (40 fichiers)
│   ├── CORRECTION_VALIDATION_COMMANDE.md
│   ├── DEPANNAGE_CACHE.md
│   ├── TEST_FORMAT_COMMANDE.md
│   ├── GUIDE_COMPLET.md
│   ├── INSTALLATION_PAIEMENT.md
│   ├── MAILING_README.md
│   └── ... (autres guides)
│
├── 📁 scripts/                # 🔧 Scripts utilitaires (7 fichiers)
│   ├── INSTALL_PHPMAILER.bat
│   ├── fix_statut_livree.php
│   ├── update_database.php
│   ├── verif_paiement.php
│   └── voir_logs_emails.php
│
├── 📁 tests/                  # 🧪 Fichiers de test (25 fichiers)
│   ├── test_validation.html
│   ├── test_paiement_complet.html
│   ├── test_email_controller.html
│   ├── diagnostic_commande.html
│   └── ... (autres tests)
│
├── 📁 sql/                    # 💾 Scripts SQL
│   └── test_stock.sql
│
├── 📁 vendor/                 # 📦 Dépendances PHP
│   └── phpmailer/
│
└── 📁 logs/                   # 📝 Logs système
    └── emails.log
```

## 📊 Statistiques

| Catégorie | Nombre de fichiers |
|-----------|-------------------|
| Documentation (docs/) | 40 fichiers MD |
| Tests (tests/) | 25 fichiers HTML/PHP |
| Scripts (scripts/) | 7 fichiers BAT/PHP |
| Contrôleurs | 8 fichiers PHP |
| Modèles | 4 fichiers PHP |
| Vues Front | 6 pages HTML |
| **Total racine** | **4 fichiers essentiels** |

## 🎯 Avantages de cette Organisation

### ✅ Racine Propre
- Seulement 4 fichiers essentiels à la racine
- Configuration et README facilement accessibles
- Structure claire et professionnelle

### ✅ Documentation Centralisée
- Tous les guides dans `docs/`
- Facile à trouver et à maintenir
- Historique complet des corrections

### ✅ Tests Isolés
- Tous les tests dans `tests/`
- N'interfèrent pas avec le code de production
- Faciles à exécuter et à supprimer

### ✅ Scripts Utilitaires Séparés
- Scripts d'installation et maintenance dans `scripts/`
- Faciles à exécuter quand nécessaire
- Ne polluent pas la racine

## 📚 Documentation Principale

### Guides Essentiels (dans docs/)

1. **GUIDE_COMPLET.md** - Guide complet du projet
2. **INSTALLATION_PAIEMENT.md** - Installation du système de paiement
3. **MAILING_README.md** - Configuration des emails
4. **CORRECTION_VALIDATION_COMMANDE.md** - Validation des commandes
5. **DEPANNAGE_CACHE.md** - Résolution problèmes de cache

### Guides Techniques

- **GUIDE_JOINTURES.md** - Jointures SQL
- **GUIDE_IMAGE_SUIVI.md** - Gestion des images
- **EXPORT_CSV_AMELIORE.md** - Export de données
- **NAVBAR_UNIFIEE.md** - Navigation unifiée

## 🧪 Tests Disponibles

### Tests Fonctionnels (dans tests/)

1. **test_validation.html** - Test validation numéros de commande
2. **test_paiement_complet.html** - Test système de paiement
3. **test_email_controller.html** - Test envoi d'emails
4. **diagnostic_commande.html** - Diagnostic des commandes

### Tests Techniques

- **test_bd_paiement.php** - Test base de données paiement
- **test_images_disponibles.php** - Test images produits
- **test_creation_commande.php** - Test création commande

## 🔧 Scripts Utilitaires

### Scripts d'Installation (dans scripts/)

1. **INSTALL_PHPMAILER.bat** - Installation PHPMailer
2. **INSTALL_PHP.bat** - Installation PHP

### Scripts de Maintenance

- **update_database.php** - Mise à jour BDD
- **fix_statut_livree.php** - Correction statuts
- **verif_paiement.php** - Vérification paiements
- **voir_logs_emails.php** - Consultation logs emails

## 🚀 Démarrage Rapide

### 1. Configuration Initiale

```bash
# 1. Configurer la base de données
# Modifier config.php avec vos paramètres

# 2. Importer la structure
mysql -u root -p peaceconnect < database.sql

# 3. Installer PHPMailer (si nécessaire)
scripts/INSTALL_PHPMAILER.bat
```

### 2. Configuration Email

```bash
# Copier et configurer
cp config/config_mail.php.example config/config_mail.php
# Éditer config/config_mail.php avec vos paramètres SMTP
```

### 3. Configuration Paiement

```bash
# Copier et configurer
cp config/config_paiement.php.example config/config_paiement.php
# Éditer config/config_paiement.php avec vos clés API
```

### 4. Accès

- **Front Office** : `http://localhost/peaceconnect/view/front/produits.html`
- **Back Office** : `http://localhost/peaceconnect/view/back/dashboard.html`

## 📖 Documentation Complète

Consultez `docs/INDEX_DOCUMENTATION.md` pour un index complet de toute la documentation disponible.

## 🔍 Recherche de Documentation

Pour trouver un guide spécifique :

```bash
# Rechercher dans la documentation
cd docs
grep -r "mot-clé" *.md
```

Ou consultez directement :
- Problème de paiement → `docs/INSTALLATION_PAIEMENT.md`
- Problème d'email → `docs/MAILING_README.md`
- Problème d'images → `docs/GUIDE_IMAGE_SUIVI.md`
- Problème de validation → `docs/CORRECTION_VALIDATION_COMMANDE.md`

## 🎨 Structure MVC Respectée

```
Model (model/)
  ↓ Données
Controller (controller/)
  ↓ Logique
View (view/)
  ↓ Affichage
```

## ✨ Prochaines Étapes

1. ✅ Structure organisée
2. ✅ Documentation centralisée
3. ✅ Tests isolés
4. ⏳ Ajouter tests unitaires
5. ⏳ Améliorer la documentation API
6. ⏳ Créer guide de contribution

---

**Date de réorganisation** : 9 décembre 2025  
**Structure** : MVC propre et organisée  
**Fichiers à la racine** : 4 essentiels uniquement
