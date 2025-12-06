# 📧 Système de Mailing Gmail - PeaceConnect

## ✨ Fonctionnalités

### Email de Confirmation de Commande
- ✅ **Email automatique** envoyé après chaque commande
- ✅ **Confirmation instantanée** au client avec numéro de commande
- ✅ **Détails complets** - Produits, quantités, prix, total
- ✅ **Design professionnel** - Template HTML responsive
- ✅ **Lien de suivi** - Accès direct au suivi de commande

### Templates Disponibles
1. **Confirmation Commande** - Email envoyé automatiquement après validation
2. **Mise à jour Statut** - Notification changement de statut (en attente, expédiée, livrée)

## 🚀 Installation

### Étape 1: Installer PHPMailer

#### Option A: Avec Composer (Recommandé)
```bash
cd e:\xampp\htdocs\PeaceConnect
composer require phpmailer/phpmailer
```

#### Option B: Installation manuelle
1. Télécharger PHPMailer: https://github.com/PHPMailer/PHPMailer/releases/latest
2. Extraire le contenu dans: `e:\xampp\htdocs\PeaceConnect\vendor\phpmailer\phpmailer\`
3. La structure doit être:
   ```
   vendor/
     phpmailer/
       phpmailer/
         src/
           PHPMailer.php
           SMTP.php
           Exception.php
   ```

#### Option C: Utiliser le script fourni
Double-cliquer sur: `INSTALL_PHPMAILER.bat`

### Étape 2: Configurer Gmail

#### 2.1 Activer la vérification en 2 étapes
1. Aller sur: https://myaccount.google.com/security
2. Cliquer sur "Vérification en 2 étapes"
3. Suivre les instructions pour l'activer

#### 2.2 Générer un mot de passe d'application
1. Aller sur: https://myaccount.google.com/apppasswords
2. Dans "Sélectionner une application": choisir "Autre (nom personnalisé)"
3. Entrer: **PeaceConnect**
4. Cliquer sur "Générer"
5. **Copier** le mot de passe de 16 caractères (format: xxxx xxxx xxxx xxxx)

#### 2.3 Configurer l'application
Éditer le fichier: `config/config_mail.php`

```php
'smtp' => [
    'username' => 'votre-email@gmail.com',        // VOTRE EMAIL GMAIL
    'password' => 'xxxx xxxx xxxx xxxx',          // MOT DE PASSE D'APPLICATION
],

'admin' => [
    'email' => 'admin@gmail.com',                 // EMAIL QUI REÇOIT LES ALERTES
    'name' => 'Admin PeaceConnect'
],
```

## 🧪 Tester la Configuration

### Test 1: Vérifier l'installation
Ouvrir dans le navigateur:
```
http://localhost/PeaceConnect/controller/EmailController.php?action=config
```

Vous devriez voir la configuration (sans le mot de passe).

### Test 2: Envoyer un email de test
```
http://localhost/PeaceConnect/controller/EmailController.php?action=test&email=votre-email@gmail.com
```

Si l'email est reçu ✅ la configuration fonctionne!

### Test 3: Passer une commande
1. Aller sur le site frontend
2. Ajouter des produits au panier
3. Valider la commande avec votre email
4. ✅ Vous recevrez automatiquement l'email de confirmation!

## 📱 Utilisation

### Envoi Automatique
Lorsqu'un client passe une commande via le formulaire de commande:
1. La commande est créée dans la base de données
2. **Un email de confirmation est automatiquement envoyé** au client
3. L'email contient:
   - Numéro de commande
   - Liste des produits commandés
   - Prix détaillés et total
   - Lien pour suivre la commande

### Notifications Toast
Des notifications apparaissent automatiquement:
- ✅ Confirmation d'envoi de l'email
- ℹ️ État de la création de commande

## 📝 Structure des Fichiers

```
PeaceConnect/
├── model/
│   └── Mailer.php                 # Classe principale de mailing
├── controller/
│   └── EmailController.php        # API d'envoi d'emails
├── config/
│   └── config_mail.php            # Configuration email
├── docs/
│   └── MAILING_SETUP.md          # Documentation détaillée
└── INSTALL_PHPMAILER.bat         # Script d'installation
```

## 🔧 Dépannage

### Erreur: "PHPMailer non installé"
**Solution**: Installer PHPMailer (voir Étape 1)

### Erreur: "Could not authenticate"
**Causes possibles**:
- Mot de passe d'application incorrect
- Vérification en 2 étapes non activée
- Email Gmail incorrect

**Solution**: 
1. Vérifier que la vérification en 2 étapes est activée
2. Générer un nouveau mot de passe d'application
3. Copier/coller exactement le mot de passe

### Erreur: "SMTP connect() failed"
**Causes possibles**:
- Pas de connexion Internet
- Port 587 bloqué par le firewall
- Antivirus bloquant la connexion

**Solution**:
1. Vérifier la connexion Internet
2. Désactiver temporairement l'antivirus
3. Essayer le port 465 avec SSL dans `config/config_mail.php`:
   ```php
   'port' => 465,
   'secure' => 'ssl',
   ```

### Email non reçu
**Vérifier**:
- Dossier Spam/Courrier indésirable
- Email admin correct dans `config/config_mail.php`
- Quota Gmail (500 emails/jour maximum)

## 🎨 Personnalisation

### Modifier les templates
Éditer le fichier: `model/Mailer.php`

Méthodes de templates:
- `templateOrderConfirmation()` - Confirmation de commande
- `templateOrderStatus()` - Mise à jour du statut

### Activer/Désactiver les notifications
Dans `config/config_mail.php`:
```php
'notifications' => [
    'order_confirmation_enabled' => true,       // Confirmation commande
    'order_status_update_enabled' => true,      // Mise à jour statut
]
```

## 📚 API Endpoints

### GET /controller/EmailController.php

| Action | Description | Paramètres |
|--------|-------------|------------|
| `test` | Tester la config | `email` (optionnel) |
| `config` | Voir la configuration | - |

### Emails automatiques

Les emails sont envoyés **automatiquement** par `CommandeController.php` lors de:
- Création d'une commande (`creer()`)
- Mise à jour du statut (`updateStatut()`)

### Exemples de réponses

**Succès**:
```json
{
    "success": true,
    "message": "Alerte de stock envoyée avec succès",
    "alerts_count": 2,
    "sent_to": "admin@gmail.com"
}
```

**Erreur**:
```json
{
    "success": false,
    "message": "Erreur: Could not authenticate"
}
```

## 🔐 Sécurité

- ⚠️ **Ne jamais commiter** `config/config_mail.php` avec les vrais identifiants
- ✅ Utiliser des **mots de passe d'application** Gmail (jamais le mot de passe principal)
- ✅ Activer la **vérification en 2 étapes**
- ✅ Limiter les permissions des fichiers de configuration

## 📞 Support

Pour toute question sur la configuration:
1. Consulter `docs/MAILING_SETUP.md`
2. Vérifier les logs PHP dans `xampp/logs/`
3. Activer le debug dans `config/config_mail.php`:
   ```php
   'options' => [
       'debug' => true
   ]
   ```

---

**Développé pour PeaceConnect** 🌍
Version 1.0 - Novembre 2025
