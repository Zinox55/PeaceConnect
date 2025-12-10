# 🚀 DÉMARRAGE RAPIDE - Configuration Email

## ❌ Problème: Email Non Configuré

Si vous voyez cette erreur:
```
Failed to connect to mailserver at "localhost" port 25
```

## ✅ Solution en 2 Minutes

### Étape 1: Ouvrir PowerShell en Administrateur

1. Appuyez sur **Windows + X**
2. Sélectionnez **"Windows PowerShell (Admin)"** ou **"Terminal (Admin)"**

### Étape 2: Exécuter le Script

Copiez-collez cette commande:

```powershell
cd C:\xampp\htdocs\PeaceConnectr\PeaceConnect; .\setup-email-mailtrap.ps1
```

### Étape 3: Suivre les Instructions

Le script vous guidera pour:
- **Option 1: Mailtrap** (Recommandé - Facile et gratuit)
- **Option 2: Gmail** (Pour production)

---

## 📖 Guides Disponibles

1. **Guide Visuel Interactif**:
   - Ouvrez: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/setup-email-guide.html

2. **Documentation Markdown**:
   - `SOLUTION_RAPIDE_EMAIL.md` - Guide pas à pas
   - `QUICK_START_EMAIL.md` - Configuration rapide
   - `CONFIGURATION_COMPLETE.txt` - Instructions complètes

---

## 🎯 Mailtrap (Recommandé pour Tests)

**Pourquoi Mailtrap?**
- ✅ Gratuit et facile
- ✅ Configuration en 5 minutes
- ✅ Capture tous les emails (ne les envoie pas réellement)
- ✅ Interface web pour voir les emails
- ✅ Parfait pour développement

**Comment?**
1. Créez un compte: https://mailtrap.io
2. Obtenez vos credentials (Username + Password)
3. Exécutez le script PowerShell
4. Choisissez Option 1
5. Entrez vos credentials
6. Terminé !

---

## 📧 Gmail (Pour Production)

**Pour envoyer de vrais emails:**
1. Créez un mot de passe d'application Gmail
2. Exécutez le script PowerShell
3. Choisissez Option 2
4. Entrez vos credentials Gmail

**Créer Mot de Passe d'Application:**
- https://myaccount.google.com/security
- Validation en 2 étapes → Activer
- Mots de passe d'application → Créer

---

## 🧪 Tester

Après configuration:
http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/test-email.php

---

## 🆘 Aide

Si le script ne fonctionne pas:

1. **Ouvrez le guide visuel**:
   http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/setup-email-guide.html

2. **Consultez la documentation**:
   - `SOLUTION_RAPIDE_EMAIL.md`

3. **Configuration manuelle**:
   - Éditez `C:\xampp\sendmail\sendmail.ini`
   - Éditez `C:\xampp\php\php.ini`
   - Voir `CONFIGURATION_COMPLETE.txt` pour les détails

---

**✨ Une fois configuré, chaque donation enverra automatiquement un email à `ghribiranim6@gmail.com` !**
