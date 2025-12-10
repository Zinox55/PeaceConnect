# 🚀 SOLUTION RAPIDE - Configuration Email en 5 Minutes

## ❌ Problème Actuel
```
Failed to connect to mailserver at "localhost" port 25
```
XAMPP n'est pas configuré pour envoyer des emails.

---

## ✅ SOLUTION RECOMMANDÉE: Mailtrap (FACILE)

### Pourquoi Mailtrap?
- ✅ **Gratuit** et **facile** à configurer
- ✅ **Pas de configuration Gmail** compliquée
- ✅ Tous les emails sont **capturés** (pas envoyés réellement)
- ✅ **Parfait pour le développement et les tests**
- ✅ Interface web pour voir tous les emails

---

## 📋 Étapes de Configuration (5 minutes)

### Étape 1: Créer un compte Mailtrap (2 min)

1. Allez sur: **https://mailtrap.io**
2. Cliquez sur **"Sign Up"** (Inscription gratuite)
3. Créez votre compte (email + mot de passe)

### Étape 2: Obtenir vos credentials (1 min)

1. Connectez-vous à Mailtrap
2. Dans le menu, cliquez sur **"Email Testing"** → **"Inboxes"**
3. Cliquez sur votre inbox (ou créez-en un)
4. Cliquez sur **"SMTP Settings"**
5. Copiez ces informations:
   - **Username**: (ex: `a1b2c3d4e5f6g7`)
   - **Password**: (ex: `9876543210abcd`)

### Étape 3: Exécuter le script de configuration (2 min)

#### Option A: Script Automatique (RECOMMANDÉ)

1. **Clic droit** sur PowerShell → **Exécuter en tant qu'administrateur**

2. Exécutez:
```powershell
cd C:\xampp\htdocs\PeaceConnectr\PeaceConnect
.\setup-email-mailtrap.ps1
```

3. Choisissez **option 1** (Mailtrap)

4. Entrez vos credentials Mailtrap

5. Le script configure automatiquement tout !

#### Option B: Configuration Manuelle

Si le script ne fonctionne pas, modifiez manuellement:

**Fichier 1**: `C:\xampp\sendmail\sendmail.ini`
```ini
[sendmail]
smtp_server=smtp.mailtrap.io
smtp_port=2525
auth_username=VOTRE_USERNAME_MAILTRAP
auth_password=VOTRE_PASSWORD_MAILTRAP
force_sender=noreply@peaceconnect.org
```

**Fichier 2**: `C:\xampp\php\php.ini`

Cherchez `[mail function]` et modifiez:
```ini
[mail function]
SMTP=smtp.mailtrap.io
smtp_port=2525
sendmail_path="C:\xampp\sendmail\sendmail.exe -t"
```

### Étape 4: Redémarrer Apache

1. Ouvrez **XAMPP Control Panel**
2. Cliquez sur **Stop** pour Apache
3. Cliquez sur **Start** pour Apache

### Étape 5: Tester !

1. Ouvrez: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/test-email.php

2. Vous devriez voir: **✅ Email sent successfully**

3. Vérifiez sur **https://mailtrap.io** → votre inbox → l'email est là !

---

## 🎯 Test Complet

### Faire une donation de test:

1. Allez sur: http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/index.php

2. Remplissez le formulaire:
   - Nom: Test User
   - Email: test@example.com
   - Montant: 100
   - Sélectionnez une cause
   - Méthode: Carte bancaire

3. Soumettez

4. **Résultat attendu**:
   - ✅ Redirection vers le reçu
   - ✅ PDF téléchargeable
   - ✅ Email visible dans **Mailtrap inbox**

5. Vérifiez Mailtrap:
   - Allez sur https://mailtrap.io
   - Ouvrez votre inbox
   - Vous verrez l'email "New Donation Received - PeaceConnect"

---

## 🔧 Dépannage

### Problème: "Failed to connect to mailserver"

**Solution**: 
```powershell
# Vérifiez la configuration
Get-Content C:\xampp\sendmail\sendmail.ini | Select-String "smtp_server|smtp_port|auth_username"

# Devrait afficher:
# smtp_server=smtp.mailtrap.io
# smtp_port=2525
# auth_username=VOTRE_USERNAME
```

### Problème: Email non reçu dans Mailtrap

1. Vérifiez les logs:
```powershell
Get-Content C:\xampp\sendmail\error.log -Tail 20
```

2. Confirmez que Apache est redémarré

3. Vérifiez que vous êtes connecté au bon inbox Mailtrap

### Problème: Script PowerShell ne s'exécute pas

**Solution**: Activer l'exécution de scripts
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

---

## 📊 Après Configuration Réussie

### Emails capturés dans Mailtrap

Tous les emails envoyés par PeaceConnect seront visibles sur:
- **URL**: https://mailtrap.io
- **Menu**: Email Testing → Inboxes → Votre Inbox

### Contenu de l'email

Vous verrez:
- ✉️ **Sujet**: New Donation Received - PeaceConnect
- 📋 **Contenu HTML**: Détails complets de la donation
- 👤 Nom et email du donateur
- 💰 Montant et cause
- 🔗 Lien vers le dashboard

---

## 🎓 Alternative: Gmail (Pour Production)

Si vous voulez envoyer de vrais emails (pas recommandé pour les tests):

1. Exécutez le script: `.\setup-email-mailtrap.ps1`
2. Choisissez **option 2** (Gmail)
3. Créez un mot de passe d'application Gmail:
   - https://myaccount.google.com/security
   - Validation en 2 étapes → Activer
   - Mots de passe d'application → Créer
4. Suivez les instructions du script

---

## ✅ Checklist de Vérification

- [ ] Compte Mailtrap créé
- [ ] Credentials copiés
- [ ] `sendmail.ini` configuré
- [ ] `php.ini` configuré
- [ ] Apache redémarré
- [ ] Test avec test-email.php réussi
- [ ] Email visible dans Mailtrap
- [ ] Donation test effectuée
- [ ] Email de donation reçu dans Mailtrap

---

## 🎉 Prêt !

Une fois configuré:
- ✅ Chaque donation enverra automatiquement un email
- ✅ Les emails seront capturés dans Mailtrap
- ✅ Vous pouvez les consulter à tout moment
- ✅ Aucun email réel n'est envoyé (parfait pour les tests)

**Pour passer en production**: Changez simplement la configuration pour Gmail.

---

## 🆘 Besoin d'Aide?

1. **Logs sendmail**:
   - `C:\xampp\sendmail\error.log`
   - `C:\xampp\sendmail\debug.log`

2. **Test de base**:
   ```powershell
   # Vérifier que sendmail.exe existe
   Test-Path C:\xampp\sendmail\sendmail.exe
   
   # Vérifier la configuration
   Get-Content C:\xampp\sendmail\sendmail.ini
   ```

3. **Recommencer à zéro**:
   ```powershell
   # Restaurer les sauvegardes
   Copy-Item C:\xampp\sendmail\sendmail.ini.backup C:\xampp\sendmail\sendmail.ini
   Copy-Item C:\xampp\php\php.ini.backup C:\xampp\php\php.ini
   ```

---

**💡 Astuce**: Mailtrap est utilisé par des milliers de développeurs pour tester les emails. C'est la solution la plus simple et la plus fiable pour le développement !
