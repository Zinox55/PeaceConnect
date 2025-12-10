# 🎯 PeaceConnect - Liens Rapides

## 📱 URLs du Projet

### 🌐 FrontOffice (Utilisateurs)
- **Page principale / Donation**: http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/index.php
- **Reçu de donation**: http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/receiptDon.php?id={ID}
- **Export PDF reçu**: http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/exportReceiptPDF.php?id={ID}

### 🔐 BackOffice (Administration)
- **Dashboard**: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/index.php
- **Liste des donations**: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/tables.php
- **Liste des causes**: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/causesTables.php
- **Ajouter une donation**: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/addDonBackoffice.php
- **Ajouter une cause**: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/addCauseBackoffice.php
- **Rechercher donations**: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/searchDon.php

### 🧪 Outils de Test
- **Test Email**: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/test-email.php
- **phpMyAdmin**: http://localhost/phpmyadmin

---

## ✅ Checklist de Configuration

### 1. Base de Données
- [ ] XAMPP installé
- [ ] Apache démarré
- [ ] MySQL démarré
- [ ] Base `mvc_charity` créée (via import SQL)
- [ ] Tables `don` et `cause` présentes

### 2. Configuration Email
- [ ] `sendmail.ini` configuré dans `C:\xampp\sendmail\`
- [ ] Mot de passe d'application Gmail créé
- [ ] `php.ini` modifié dans `C:\xampp\php\`
- [ ] Apache redémarré après modifications
- [ ] Test email réussi

### 3. Fichiers du Projet
- [ ] Dossier `vendor/tcpdf/` présent (pour PDF)
- [ ] Fichiers controllers présents
- [ ] Fichiers models présents
- [ ] Fichiers views présents
- [ ] `config.php` configuré

---

## 🚀 Workflow Complet

### Pour Faire une Donation

1. **Accédez au formulaire**:
   http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/index.php

2. **Remplissez les informations**:
   - Nom du donateur
   - Email
   - Montant
   - Cause (sélection)
   - Méthode de paiement
   - Message (optionnel)

3. **Soumettez le formulaire**

4. **Résultats automatiques**:
   - ✅ Donation enregistrée dans la base
   - ✅ Redirection vers page de reçu
   - ✅ Email envoyé à `ghribiranim6@gmail.com`
   - ✅ PDF téléchargeable

### Pour Consulter les Statistiques

1. **Accédez au dashboard**:
   http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/index.php

2. **Consultez**:
   - Nombre total de donations
   - Montant total collecté
   - Causes actives
   - Donations récentes
   - Répartition par méthode de paiement
   - Top causes

3. **Actions disponibles**:
   - Voir les détails
   - Exporter en PDF
   - Modifier/Supprimer

---

## 📧 Format de l'Email Envoyé

**Destinataire**: ghribiranim6@gmail.com  
**Objet**: New Donation Received - PeaceConnect

**Contenu**:
```
🎉 New Donation Received!

Donation Details:
- Donation ID: #123
- Donor Name: John Doe
- Donor Email: john@example.com
- Amount: 100.00 DT
- Cause: Gaza Relief Fund
- Payment Method: carte_bancaire
- Date: December 10, 2025 15:30:00
- Message: "Keep up the good work!"

[View in Dashboard Button]
```

---

## 🔧 Commandes Utiles

### Redémarrer Apache (PowerShell)
```powershell
# Via XAMPP Control Panel (recommandé)
# Cliquez sur Stop puis Start pour Apache
```

### Vérifier les logs email
```powershell
Get-Content C:\xampp\sendmail\error.log -Tail 20
Get-Content C:\xampp\sendmail\debug.log -Tail 20
```

### Accéder à la base de données
```powershell
cd C:\xampp\mysql\bin
.\mysql.exe -u root -p
# Pas de mot de passe par défaut, appuyez sur Entrée
```

---

## 📊 Statistiques Disponibles

### Dashboard BackOffice
- **Total Donations**: Nombre total de donations
- **Total Amount**: Somme totale collectée
- **Active Causes**: Nombre de causes enregistrées
- **Average Donation**: Montant moyen par donation

### Tables Détaillées
- Liste complète des donations avec filtres
- Détails de chaque donation
- Export PDF individuel
- Recherche avancée

---

## 🎨 Personnalisation

### Changer l'email de notification

Éditez `controller/DonController.php`, ligne ~59:
```php
$to = "votre-nouveau-email@example.com";
```

### Modifier le design de l'email

Éditez `controller/DonController.php`, méthode `sendDonationNotificationEmail()`:
- Modifiez le HTML dans la variable `$message`
- Changez les couleurs dans le `<style>`

### Personnaliser le PDF

Éditez `controller/DonController.php`, méthode `exportReceiptPDF()`:
- Modifiez les couleurs (RGB)
- Changez le texte
- Ajustez la mise en page

---

## 🐛 Solutions aux Problèmes Courants

### Email non reçu
1. Vérifiez `C:\xampp\sendmail\error.log`
2. Testez avec http://localhost/.../test-email.php
3. Vérifiez le dossier spam
4. Confirmez que Apache est redémarré

### PDF ne se génère pas
1. Vérifiez que `vendor/tcpdf/` existe
2. Pas d'output (echo, print) avant la génération PDF
3. Vérifiez les logs PHP

### Erreur base de données
1. Confirmez que MySQL est démarré
2. Vérifiez `config.php`
3. Importez `database/mvc_charity.sql`

### Page blanche
1. Activez l'affichage des erreurs PHP
2. Vérifiez les logs Apache
3. Vérifiez les chemins des require_once

---

## 📞 Support

### Logs à vérifier
- `C:\xampp\sendmail\error.log` - Erreurs email
- `C:\xampp\sendmail\debug.log` - Debug email
- `C:\xampp\apache\logs\error.log` - Erreurs Apache
- `C:\xampp\php\logs\php_error_log` - Erreurs PHP

### Tests à effectuer
1. Test email: test-email.php
2. Test base de données: phpMyAdmin
3. Test Apache: http://localhost
4. Test donation complète: FrontOffice

---

## ✨ Prêt à Utiliser !

**Tout est configuré ! Voici les prochaines étapes :**

1. ✅ **Testez l'email**: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/test-email.php
2. ✅ **Faites une donation test**: http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/index.php
3. ✅ **Vérifiez le dashboard**: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/index.php
4. ✅ **Vérifiez votre email**: ghribiranim6@gmail.com

**🎉 Le projet PeaceConnect est opérationnel ! 🎉**
