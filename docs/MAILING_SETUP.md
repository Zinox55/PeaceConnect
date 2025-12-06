# Configuration du Système de Mailing avec Gmail

## 📧 Installation de PHPMailer

### Option 1: Avec Composer (Recommandé)
```bash
cd e:\xampp\htdocs\PeaceConnect
composer require phpmailer/phpmailer
```

### Option 2: Installation manuelle
1. Télécharger PHPMailer: https://github.com/PHPMailer/PHPMailer/releases
2. Extraire dans `e:\xampp\htdocs\PeaceConnect\vendor\phpmailer\`

## 🔐 Configuration Gmail

### Étape 1: Activer la vérification en 2 étapes
1. Aller sur https://myaccount.google.com/security
2. Activer "Vérification en 2 étapes"

### Étape 2: Générer un mot de passe d'application
1. Aller sur https://myaccount.google.com/apppasswords
2. Sélectionner "Autre (nom personnalisé)"
3. Entrer "PeaceConnect"
4. Copier le mot de passe généré (16 caractères)

### Étape 3: Configurer le fichier config/config_mail.php
```php
'smtp' => [
    'username' => 'votre-email@gmail.com',
    'password' => 'xxxx xxxx xxxx xxxx', // Mot de passe d'application
],
'admin' => [
    'email' => 'admin@gmail.com', // Email qui recevra les alertes
],
```

## 🚀 Utilisation

### Tester la configuration
```
GET http://localhost/PeaceConnect/controller/EmailController.php?action=test&email=test@example.com
```

### Envoyer les alertes de stock
```
GET http://localhost/PeaceConnect/controller/EmailController.php?action=send_stock_alerts
```

### Obtenir la configuration
```
GET http://localhost/PeaceConnect/controller/EmailController.php?action=config
```

## 📝 Templates Disponibles

1. **low_stock_admin** - Alerte de stock faible pour l'admin
2. **order_confirmation** - Confirmation de commande pour le client
3. **order_status** - Mise à jour du statut de commande

## ⚙️ Intégration dans le Dashboard

Le bouton "Envoyer Alertes Email" a été ajouté au dashboard pour envoyer manuellement les alertes de stock par email.

## 🔧 Dépannage

### Erreur "Could not authenticate"
- Vérifier que le mot de passe d'application est correct
- Vérifier que la vérification en 2 étapes est activée

### Erreur "SMTP connect() failed"
- Vérifier la connexion Internet
- Vérifier que le port 587 n'est pas bloqué par le firewall

### Erreur "PHPMailer non installé"
- Exécuter `composer require phpmailer/phpmailer`
