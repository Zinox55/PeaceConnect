# 🕊️ PeaceConnect - Plateforme de Dons Caritatifs

Plateforme de gestion de dons et causes caritatives avec architecture MVC.

## ✨ Fonctionnalités

### 🎯 Gestion des Donations
- ✅ Formulaire de donation (FrontOffice)
- ✅ Création, modification, suppression de donations
- ✅ Recherche et filtrage des donations
- ✅ Export PDF des reçus de donation
- ✅ **Notification email automatique** après chaque donation

### ❤️ Gestion des Causes
- ✅ Création et gestion des causes
- ✅ Association donations ↔ causes
- ✅ Statistiques par cause

### 📊 Dashboard BackOffice
- ✅ Statistiques en temps réel
- ✅ Total des donations et montants
- ✅ Causes actives
- ✅ Donations récentes
- ✅ Répartition par méthode de paiement
- ✅ Top causes avec barres de progression

### 📧 Système de Notification Email
- ✅ Email automatique à `ghribiranim6@gmail.com` après chaque donation (notification admin)
- ✅ **Email de reçu envoyé au donateur avec PDF en pièce jointe**
- ✅ Format HTML professionnel
- ✅ Détails complets de la donation
- ✅ Lien direct vers le dashboard

### 📄 Export PDF
- ✅ Génération de reçus PDF avec TCPDF
- ✅ Design professionnel avec logo et couleurs
- ✅ Export depuis FrontOffice et BackOffice

## 🏗️ Architecture MVC

```
PeaceConnect/
├── model/              # Modèles (Don, Cause)
├── view/               # Vues (FrontOffice, BackOffice)
├── controller/         # Contrôleurs (DonController, CauseController)
├── config.php          # Configuration base de données
└── database/           # Scripts SQL
```

## 🚀 Installation

### 1. Prérequis
- XAMPP (Apache + MySQL + PHP 7.4+)
- Navigateur web moderne

### 2. Configuration Base de Données

1. Démarrez Apache et MySQL dans XAMPP
2. Accédez à phpMyAdmin: http://localhost/phpmyadmin
3. Importez le fichier: `database/mvc_charity.sql`
4. La base `mvc_charity` sera créée automatiquement

### 3. Configuration Email (Optionnel mais recommandé)

**Option A: Gmail SMTP**

1. Éditez `C:\xampp\sendmail\sendmail.ini`:
```ini
smtp_server=smtp.gmail.com
smtp_port=587
auth_username=votre-email@gmail.com
auth_password=MOT_DE_PASSE_APPLICATION
force_sender=votre-email@gmail.com
```

2. Créez un mot de passe d'application Gmail:
   - https://myaccount.google.com/security
   - Activez la validation en 2 étapes
   - Créez un mot de passe d'application

3. Éditez `C:\xampp\php\php.ini`:
```ini
[mail function]
SMTP=smtp.gmail.com
smtp_port=587
sendmail_from=noreply@peaceconnect.org
sendmail_path="C:\xampp\sendmail\sendmail.exe -t"
```

4. Redémarrez Apache

**Option B: Mailtrap (Pour tests)**
- Voir: `QUICK_START_EMAIL.md`

### 4. Tester l'Email

http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/test-email.php

## 📱 Utilisation

### FrontOffice (Utilisateur)
http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/index.php

- Faire une donation
- Recevoir un reçu PDF téléchargeable
- **Recevoir automatiquement un email avec le reçu PDF en pièce jointe**
- Email de notification envoyé automatiquement à l'admin

### BackOffice (Administration)
http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/index.php

- Dashboard avec statistiques
- Gestion des donations
- Gestion des causes
- Export PDF des reçus

## 📚 Documentation

- `QUICK_START_EMAIL.md` - Configuration email en 3 minutes
- `EMAIL_SETUP_GUIDE.md` - Guide détaillé email
- `sendmail.ini.example` - Exemple configuration sendmail
- `php.ini.email-config.example` - Exemple configuration PHP

## 🛠️ Technologies

- **Backend**: PHP 7.4+
- **Base de données**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap
- **Template**: SB Admin 2 (BackOffice)
- **PDF**: TCPDF
- **Email**: PHP mail() avec SMTP

## 📂 Structure des Tables

### Table `don` (Donations)
- id_don (PK)
- montant
- devise
- date_don
- donateur_nom
- donateur_email
- message
- methode_paiement
- transaction_id
- cause (FK → cause.id_cause)

### Table `cause` (Causes)
- id_cause (PK)
- nom_cause
- description
- montant_objectif
- date_creation

## 🎨 Fonctionnalités Email

### Email de Notification Admin
Envoyé à `ghribiranim6@gmail.com`:
- 📋 ID de la donation
- 👤 Informations du donateur
- 💰 Montant et devise
- ❤️ Cause sélectionnée
- 💳 Méthode de paiement
- 📅 Date et heure
- 💬 Message (optionnel)
- 🔗 Lien vers le dashboard

### Email de Reçu au Donateur
Envoyé à l'email du donateur:
- 🙏 Message de remerciement personnalisé
- 📄 **Reçu PDF en pièce jointe**
- 📋 Résumé de la donation
- 💰 Montant mis en valeur
- ❤️ Détails de la cause
- 🔗 Lien pour faire un autre don

## 🔒 Sécurité

- ✅ Requêtes préparées PDO (protection SQL injection)
- ✅ Validation des données côté serveur
- ✅ Échappement HTML (protection XSS)
- ✅ Sessions PHP sécurisées

## 📈 Statistiques Dashboard

- Total donations
- Montant total collecté
- Nombre de causes actives
- Montant moyen par donation
- 5 dernières donations
- Répartition par méthode de paiement
- Top 5 causes

## 🐛 Dépannage

### Email non reçu?
1. Vérifiez `C:\xampp\sendmail\error.log`
2. Testez avec `test-email.php`
3. Vérifiez le dossier spam
4. Confirmez que Apache est redémarré

### Erreur TCPDF?
- Vérifiez que `vendor/tcpdf/` existe
- Pas d'output avant `$pdf->Output()`

### Erreur base de données?
- Vérifiez `config.php`
- Confirmez que MySQL est démarré
- Vérifiez que `mvc_charity` existe

## 👥 Auteurs

Projet développé avec architecture MVC respectée.

## 📄 Licence

Projet éducatif - PeaceConnect 2025

---

## 🚀 Démarrage Rapide

1. Importez `database/mvc_charity.sql`
2. Configurez email (voir `QUICK_START_EMAIL.md`)
3. Testez avec: http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/index.php
4. Accédez au dashboard: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/index.php

**✨ Prêt à recevoir des donations ! 🎉**