# PeaceConnect - Plateforme E-commerce Humanitaire

## 🌍 À propos

PeaceConnect est une plateforme e-commerce dédiée aux actions humanitaires. Elle permet aux utilisateurs d'acheter des produits solidaires et de contribuer à des causes importantes.

## ✨ Fonctionnalités

### Front Office
- 🛍️ **Catalogue de produits** avec recherche et filtres
- 🛒 **Panier dynamique** avec gestion des quantités
- 💳 **Système de paiement sécurisé** (Carte bancaire, PayPal, Virement)
- 📦 **Suivi de commande** en temps réel
- 📧 **Notifications par email** pour les confirmations

### Back Office
- 📊 **Dashboard** avec statistiques en temps réel
- 📦 **Gestion des produits** (CRUD complet)
- 🛍️ **Gestion des commandes** avec changement de statut
- 📈 **Statistiques avancées** et exports CSV
- 🖼️ **Upload d'images** pour les produits

### Système de Paiement
- 💳 **Carte bancaire** avec validation en temps réel
- 💰 **PayPal** (intégration simulée)
- 🏦 **Virement bancaire** avec coordonnées IBAN
- ✅ **Confirmation automatique** avec numéro de transaction
- 📊 **Suivi des paiements** et statuts

## 🚀 Installation Rapide

### Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx)
- Composer (pour PHPMailer)

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone https://github.com/votre-repo/peaceconnect.git
cd peaceconnect
```

2. **Configurer la base de données**
```bash
mysql -u root -p < database.sql
```

3. **Configurer la connexion**
Éditez `config.php` avec vos paramètres MySQL :
```php
private static $serveur = "localhost";
private static $bdd = "peaceconnect";
private static $user = "root";
private static $mdp = "";
```

4. **Installer PHPMailer (optionnel)**
```bash
composer require phpmailer/phpmailer
# ou
php composer.phar require phpmailer/phpmailer
```

5. **Configurer l'email (optionnel)**
Copiez et éditez le fichier de configuration :
```bash
cp config/config_mail.php.example config/config_mail.php
```

6. **Accéder à l'application**
- Front office : `http://localhost/peaceconnect/view/front/index.html`
- Back office : `http://localhost/peaceconnect/view/back/dashboard.html`

## 💳 Installation du Système de Paiement

Le système de paiement est déjà inclus dans la base de données principale. Si vous avez une installation existante, exécutez :

```bash
mysql -u root -p peaceconnect < sql/add_payment_fields.sql
```

Pour plus de détails, consultez [INSTALLATION_PAIEMENT.md](INSTALLATION_PAIEMENT.md)

## 📖 Documentation

- 📘 [Guide de démarrage rapide](docs/DÉMARRAGE_RAPIDE.md)
- 📗 [Guide complet](docs/GUIDE_COMPLET.md)
- 💳 [Guide du système de paiement](docs/PAIEMENT_GUIDE.md)
- 📊 [Documentation du dashboard](docs/DASHBOARD_README.md)
- 🔍 [Recherche avancée](docs/RECHERCHE_AVANCEE.md)
- 📧 [Configuration email](docs/MAILING_SETUP.md)

## 🧪 Tests

### Tester le système complet
```
http://localhost/peaceconnect/tests/test_paiement.php
```

### Données de test

**Carte bancaire (simulation) :**
- Numéro : `4242 4242 4242 4242`
- Date : `12/25`
- CVV : `123`
- Nom : `TEST USER`

## 📁 Structure du Projet

```
peaceconnect/
├── config/                 # Configuration
│   ├── config_mail.php    # Config email
│   └── README.md
├── controller/            # Contrôleurs API
│   ├── CommandeController.php
│   ├── PaiementController.php
│   ├── PanierController.php
│   ├── ProduitController.php
│   └── ...
├── model/                 # Modèles de données
│   ├── Commande.php
│   ├── Produit.php
│   └── ...
├── view/
│   ├── front/            # Interface utilisateur
│   │   ├── index.html
│   │   ├── produits.html
│   │   ├── panier.html
│   │   ├── commande.html
│   │   ├── paiement.html
│   │   └── confirmation.html
│   ├── back/             # Back office
│   │   ├── dashboard.html
│   │   ├── produits.html
│   │   └── commandes.html
│   └── assets/           # CSS, JS, Images
├── sql/                  # Scripts SQL
├── docs/                 # Documentation
├── tests/                # Tests
├── logs/                 # Logs d'erreurs
├── config.php            # Configuration DB
└── database.sql          # Structure DB

```

## 🔧 Technologies Utilisées

- **Frontend :** HTML5, CSS3, JavaScript (Vanilla)
- **Backend :** PHP 7.4+
- **Base de données :** MySQL 5.7+
- **Email :** PHPMailer
- **Icons :** Font Awesome 6
- **Fonts :** Google Fonts (Work Sans)

## 🎨 Fonctionnalités Détaillées

### Gestion des Produits
- Ajout, modification, suppression
- Upload d'images avec prévisualisation
- Gestion du stock en temps réel
- Code-barres unique
- Système de notation (0-5 étoiles)
- Date de création automatique

### Gestion des Commandes
- Création depuis le panier
- Validation des données client
- Génération automatique de numéro de commande
- Statuts : En attente, Confirmée, Livrée, Annulée
- Date de livraison automatique
- Export CSV avec statistiques

### Système de Paiement
- 3 méthodes : Carte, PayPal, Virement
- Validation côté client et serveur
- Génération d'ID de transaction
- Statuts de paiement : En attente, Payé, Échoué, Remboursé
- Page de confirmation avec détails
- Historique des transactions

### Panier
- Ajout/suppression d'articles
- Modification des quantités
- Calcul automatique des totaux
- Badge de notification
- Persistance des données
- Vérification du stock

### Suivi de Commande
- Recherche par numéro de commande
- Affichage du statut en temps réel
- Détails des produits commandés
- Informations de livraison
- Historique des paiements

## 🔒 Sécurité

- ✅ Protection contre les injections SQL (PDO)
- ✅ Validation des données côté serveur
- ✅ Sanitization des entrées utilisateur
- ✅ Headers CORS configurés
- ✅ Gestion des erreurs sécurisée
- ⚠️ HTTPS recommandé en production
- ⚠️ Tokenisation des cartes recommandée

## 📊 Statistiques et Exports

- Nombre total de commandes
- Chiffre d'affaires par statut
- Panier moyen
- Produits les plus vendus
- Export CSV avec formatage français
- Statistiques en temps réel

## 🐛 Dépannage

### Problème : Images non affichées
**Solution :** Vérifiez les permissions du dossier `view/assets/img/produits/`
```bash
chmod 755 view/assets/img/produits/
```

### Problème : Erreur de connexion à la base de données
**Solution :** Vérifiez les paramètres dans `config.php`

### Problème : Emails non envoyés
**Solution :** Vérifiez la configuration dans `config/config_mail.php`

### Problème : Colonnes de paiement manquantes
**Solution :** Exécutez le script de migration
```bash
mysql -u root -p peaceconnect < sql/add_payment_fields.sql
```

## 📝 Changelog

### Version 2.0 (Décembre 2025)
- ✨ Ajout du système de paiement complet
- ✨ Page de confirmation de commande
- ✨ Support de 3 méthodes de paiement
- ✨ Génération d'ID de transaction
- 🐛 Correction des images dans le panier
- 🐛 Correction du statut "livrée"
- 📚 Documentation complète du paiement

### Version 1.5
- ✨ Export CSV amélioré avec statistiques
- ✨ Navbar unifiée avec effet transparent
- ✨ Badge panier rouge unifié
- ✨ Modal détails commande avec produits
- 🗑️ Suppression de la gestion clients

### Version 1.0
- 🎉 Version initiale
- ✨ CRUD produits et commandes
- ✨ Panier fonctionnel
- ✨ Dashboard avec statistiques

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. Fork le projet
2. Créez une branche (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 👥 Auteurs

- **PeaceConnect Development Team**

## 📞 Support

Pour toute question ou problème :
- 📧 Email : info@peaceconnect.org
- 📱 Téléphone : +33 (0)1 23 45 67 89
- 🌐 Site web : https://peaceconnect.org

## 🙏 Remerciements

Merci à tous les contributeurs et utilisateurs de PeaceConnect pour leur soutien dans notre mission humanitaire.

---

**Fait avec ❤️ pour un monde meilleur**
