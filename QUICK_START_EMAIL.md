# 📧 Guide de Configuration Email - PeaceConnect

## ✅ Fonctionnalité Implémentée

Le système envoie automatiquement un **email de notification** à `ghribiranim6@gmail.com` après chaque donation.

---

## 🚀 Configuration Rapide (3 minutes)

### Étape 1: Configurer sendmail.ini

1. Ouvrez le fichier: `C:\xampp\sendmail\sendmail.ini`

2. Modifiez ces lignes:
```ini
smtp_server=smtp.gmail.com
smtp_port=587
auth_username=VOTRE_EMAIL@gmail.com
auth_password=VOTRE_MOT_DE_PASSE_APPLICATION
force_sender=VOTRE_EMAIL@gmail.com
```

3. **IMPORTANT**: Pour `auth_password`, utilisez un **Mot de passe d'application Google**:
   - Allez sur: https://myaccount.google.com/security
   - Activez **Validation en 2 étapes**
   - Cliquez sur **Mots de passe d'application**
   - Créez un mot de passe pour "Mail" ou "Autre (Nom personnalisé)"
   - Copiez le mot de passe généré (16 caractères)
   - Collez-le dans `auth_password`

### Étape 2: Configurer php.ini

1. Ouvrez le fichier: `C:\xampp\php\php.ini`

2. Cherchez la section `[mail function]` et modifiez:
```ini
[mail function]
SMTP=smtp.gmail.com
smtp_port=587
sendmail_from=noreply@peaceconnect.org
sendmail_path="C:\xampp\sendmail\sendmail.exe -t"
```

### Étape 3: Redémarrer Apache

1. Ouvrez **XAMPP Control Panel**
2. Cliquez sur **Stop** pour Apache
3. Cliquez sur **Start** pour Apache

---

## 🧪 Tester la Configuration

1. Accédez à: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/test-email.php

2. Cette page va:
   - ✅ Afficher votre configuration actuelle
   - ✅ Envoyer un email de test à `ghribiranim6@gmail.com`
   - ✅ Indiquer si l'envoi a réussi

3. Vérifiez votre boîte email (et le dossier **Spam/Courrier indésirable**)

---

## 📧 Comment ça marche ?

### Envoi Automatique

Chaque fois qu'une donation est créée via le formulaire, un email est automatiquement envoyé contenant:

- 📋 **ID de la donation**
- 👤 **Nom et email du donateur**
- 💰 **Montant et devise**
- ❤️ **Cause sélectionnée**
- 💳 **Méthode de paiement**
- 📅 **Date et heure**
- 💬 **Message du donateur** (si présent)
- 🔗 **Lien direct vers le dashboard**

### Code Modifié

**Fichier**: `controller/DonController.php`
- ✅ Méthode `addDon()` modifiée pour appeler l'envoi d'email
- ✅ Nouvelle méthode `sendDonationNotificationEmail()` ajoutée
- ✅ Structure MVC respectée (logique dans le Controller)

---

## 🔧 Alternatives pour le Développement

### Option 1: Mailtrap (Recommandé pour tests)

**Avantage**: Capture les emails sans les envoyer réellement

1. Créez un compte gratuit: https://mailtrap.io
2. Créez un inbox
3. Copiez les credentials SMTP
4. Dans `sendmail.ini`:
```ini
smtp_server=smtp.mailtrap.io
smtp_port=2525
auth_username=VOTRE_USERNAME_MAILTRAP
auth_password=VOTRE_PASSWORD_MAILTRAP
```

### Option 2: MailHog (Serveur local)

1. Téléchargez: https://github.com/mailhog/MailHog/releases
2. Lancez `MailHog.exe`
3. Dans `php.ini`:
```ini
SMTP=localhost
smtp_port=1025
```
4. Interface web: http://localhost:8025

---

## 🐛 Dépannage

### Email non reçu ?

1. **Vérifiez les logs**:
   - `C:\xampp\sendmail\error.log`
   - `C:\xampp\sendmail\debug.log`

2. **Vérifiez le dossier Spam/Courrier indésirable**

3. **Testez avec test-email.php** pour isoler le problème

4. **Vérifiez le firewall** (port 587 doit être ouvert)

### Erreur "SMTP connect() failed" ?

- Vérifiez que Gmail autorise les applications moins sécurisées
- Assurez-vous d'utiliser un **mot de passe d'application** (pas votre mot de passe Gmail)
- Vérifiez la connexion Internet

### Rien ne se passe ?

1. Redémarrez Apache après chaque modification
2. Vérifiez que les fichiers `.ini` sont bien sauvegardés
3. Essayez avec Mailtrap pour exclure les problèmes Gmail

---

## 📝 Fichiers Créés

- ✅ `controller/DonController.php` - Méthode d'envoi email ajoutée
- ✅ `view/BackOffice/test-email.php` - Page de test
- ✅ `EMAIL_SETUP_GUIDE.md` - Guide détaillé
- ✅ `sendmail.ini.example` - Exemple de configuration sendmail
- ✅ `php.ini.email-config.example` - Exemple de configuration PHP
- ✅ `QUICK_START_EMAIL.md` - Ce guide

---

## 📞 Support

Si vous rencontrez des problèmes:

1. Vérifiez les logs dans `C:\xampp\sendmail\`
2. Testez avec `test-email.php`
3. Essayez Mailtrap pour éliminer les problèmes de configuration Gmail
4. Vérifiez que Apache est bien redémarré

---

## ✨ Prêt à tester !

1. Configurez `sendmail.ini` et `php.ini`
2. Redémarrez Apache
3. Testez avec: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/test-email.php
4. Faites une donation de test: http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/index.php

**L'email sera automatiquement envoyé à ghribiranim6@gmail.com ! 🎉**
