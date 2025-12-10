# Configuration Email pour PeaceConnect

## ✅ Fonctionnalité Ajoutée

Le système envoie automatiquement un email à **ghribiranim6@gmail.com** après chaque donation.

## 📧 Configuration XAMPP pour l'envoi d'emails

### Option 1: Utiliser Gmail SMTP (Recommandé)

1. **Installer un package SMTP** - Créer un fichier `sendmail.ini` dans `C:\xampp\sendmail\`:

```ini
[sendmail]
smtp_server=smtp.gmail.com
smtp_port=587
error_logfile=error.log
debug_logfile=debug.log
auth_username=VOTRE_EMAIL@gmail.com
auth_password=VOTRE_MOT_DE_PASSE_APPLICATION
force_sender=VOTRE_EMAIL@gmail.com
```

2. **Modifier `php.ini`** dans `C:\xampp\php\`:

```ini
[mail function]
SMTP=smtp.gmail.com
smtp_port=587
sendmail_from=VOTRE_EMAIL@gmail.com
sendmail_path="C:\xampp\sendmail\sendmail.exe -t"
```

3. **Créer un mot de passe d'application Gmail**:
   - Allez sur https://myaccount.google.com/security
   - Activez la validation en deux étapes
   - Créez un "Mot de passe d'application"
   - Utilisez ce mot de passe dans `sendmail.ini`

### Option 2: Utiliser un serveur SMTP local (Test)

Pour tester sans configuration complexe, utilisez un serveur SMTP local comme **Papercut** ou **MailHog**:

1. Téléchargez Papercut SMTP: https://github.com/ChangemakerStudios/Papercut-SMTP/releases
2. Lancez Papercut
3. Modifiez `php.ini`:

```ini
[mail function]
SMTP=localhost
smtp_port=25
sendmail_from=noreply@peaceconnect.org
```

4. Les emails seront capturés dans Papercut (pas envoyés réellement)

### Option 3: Utiliser Mailtrap (Développement)

1. Créez un compte gratuit sur https://mailtrap.io
2. Obtenez vos identifiants SMTP
3. Configurez `sendmail.ini` avec les credentials Mailtrap

## 🔄 Redémarrer Apache

Après toute modification de `php.ini`, redémarrez Apache depuis le panneau de contrôle XAMPP.

## 🧪 Test de l'Email

L'email sera automatiquement envoyé à chaque nouvelle donation créée via:
- FrontOffice: http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/index.php

## 📋 Contenu de l'Email

L'email envoyé contient:
- ✅ ID de la donation
- ✅ Nom et email du donateur
- ✅ Montant et devise
- ✅ Cause sélectionnée
- ✅ Méthode de paiement
- ✅ Date et heure
- ✅ Message du donateur (si présent)
- ✅ Lien vers le dashboard BackOffice

## 🎨 Format de l'Email

L'email est au format HTML avec:
- Design responsive
- Couleurs PeaceConnect
- Mise en page professionnelle
- Bouton d'accès au dashboard

## 🔧 Dépannage

**Problème**: Email non reçu
- Vérifiez les logs dans `C:\xampp\sendmail\error.log`
- Vérifiez les dossiers spam/courrier indésirable
- Confirmez que Apache est redémarré
- Testez avec `mail()` PHP directement

**Problème**: Erreur SMTP
- Vérifiez les credentials dans `sendmail.ini`
- Assurez-vous que le port 587 n'est pas bloqué par le firewall
- Vérifiez que la validation en deux étapes est activée (Gmail)

## 📝 Structure MVC Respectée

La fonctionnalité d'email a été ajoutée dans:
- **Controller**: `DonController.php` → méthode `sendDonationNotificationEmail()`
- Appelée automatiquement après `addDon()`
- Aucune modification de la structure MVC
