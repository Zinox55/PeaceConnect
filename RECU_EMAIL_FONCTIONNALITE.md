# 📧 Envoi Automatique du Reçu de Don par Email

## ✅ Fonctionnalité Implémentée

Lorsqu'un donateur fait un don, **deux emails sont automatiquement envoyés** :

### 1️⃣ Email au Donateur (NOUVEAU !)
**Destinataire**: Email du donateur (saisi dans le formulaire)
**Objet**: "Thank You for Your Donation - Receipt #[ID]"
**Contenu**:
- 🙏 Message de remerciement personnalisé
- 💰 Montant de la donation mis en valeur
- 📋 Résumé complet de la donation
- ❤️ Détails de la cause soutenue
- **📎 REÇU PDF EN PIÈCE JOINTE**
- 🔗 Lien pour faire un autre don

**Format**: HTML professionnel avec design vert (couleur de la générosité)

### 2️⃣ Email à l'Administrateur
**Destinataire**: ghribiranim6@gmail.com
**Objet**: "New Donation Received - PeaceConnect"
**Contenu**:
- 🎉 Notification de nouvelle donation
- 👤 Informations complètes du donateur
- 💰 Montant et devise
- 📋 Tous les détails de la donation
- 🔗 Lien direct vers le dashboard BackOffice

**Format**: HTML professionnel avec design bleu (couleur corporate)

---

## 🎯 Flux Complet de Donation

```
1. Utilisateur remplit le formulaire de donation
   ↓
2. Validation et enregistrement dans la base de données
   ↓
3. Génération automatique du PDF (en mémoire)
   ↓
4. Envoi Email #1: Admin (notification)
   ↓
5. Envoi Email #2: Donateur (reçu + PDF attaché)
   ↓
6. Redirection vers la page de reçu (avec bouton téléchargement PDF)
```

---

## 📄 Détails du PDF Attaché

Le PDF attaché à l'email du donateur contient:

- **En-tête** avec logo PeaceConnect (bleu)
- **Numéro de reçu** unique
- **Date** de la donation
- **Informations du donateur**:
  - Nom
  - Email
- **Détails de la donation**:
  - Cause soutenue
  - Montant (mis en évidence)
  - Méthode de paiement
  - Date et heure de la transaction
- **Message du donateur** (si présent)
- **Montant total** dans un encadré vert
- **Message de remerciement**
- **Footer** avec date de génération

**Format**: PDF professionnel, optimisé pour l'impression et l'archivage

---

## 🔧 Implémentation Technique

### Fichier Modifié
`controller/DonController.php`

### Méthodes Ajoutées

#### 1. `sendDonationReceiptEmail($don, $donId)`
- **Rôle**: Envoyer le reçu par email au donateur
- **Paramètres**: Objet Don, ID de la donation
- **Processus**:
  1. Récupère les informations de la cause
  2. Génère le PDF en mémoire (chaîne binaire)
  3. Crée un email multipart/mixed (HTML + PDF attaché)
  4. Encode le PDF en base64
  5. Envoie l'email avec la pièce jointe
- **Retour**: true si succès, false sinon

#### 2. `generateReceiptPDF($don, $donId, $causeName, $returnString)`
- **Rôle**: Générer le PDF du reçu
- **Paramètres**:
  - Objet Don
  - ID de la donation
  - Nom de la cause
  - `$returnString`: true = retourne le PDF en chaîne, false = télécharge
- **Utilisation**: 
  - Mode "string" pour l'email (pièce jointe)
  - Mode "download" pour le téléchargement direct
- **Retour**: Contenu PDF ou false en cas d'erreur

### Modification dans `addDon()`

```php
// Après l'insertion dans la base
$donId = $db->lastInsertId();

// Email admin
$this->sendDonationNotificationEmail($don, $donId);

// NOUVEAU: Email donateur avec PDF
$this->sendDonationReceiptEmail($don, $donId);
```

---

## 📧 Structure de l'Email avec Pièce Jointe

### Headers MIME
```
MIME-Version: 1.0
Content-Type: multipart/mixed; boundary="[boundary]"
From: PeaceConnect <noreply@peaceconnect.org>
```

### Parties du Message

**Partie 1: Contenu HTML**
```
Content-Type: text/html; charset=UTF-8
Content-Transfer-Encoding: 7bit

[HTML du message de remerciement]
```

**Partie 2: Pièce Jointe PDF**
```
Content-Type: application/pdf; name="donation_receipt_[ID].pdf"
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename="donation_receipt_[ID].pdf"

[Contenu PDF encodé en base64]
```

---

## 🧪 Test de la Fonctionnalité

### Étape 1: Configurer l'Email (si pas déjà fait)
Suivez le guide: `GUIDE_ETAPES_SIMPLES.txt`

### Étape 2: Faire une Donation Test

1. Allez sur: http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/index.php

2. Remplissez le formulaire:
   - **Nom**: Votre nom
   - **Email**: Votre email réel (pour recevoir le reçu)
   - **Montant**: 100
   - **Cause**: Sélectionnez une cause
   - **Méthode**: Carte bancaire
   - **Message**: "Test de don avec reçu email"

3. Soumettez le formulaire

### Étape 3: Vérifier les Résultats

**✅ Dans le navigateur**:
- Redirection vers la page de reçu
- Bouton PDF téléchargeable fonctionne

**✅ Dans Mailtrap (ou votre inbox)**:
- **Email 1** (Admin): "New Donation Received"
  - Reçu par ghribiranim6@gmail.com
  - Contient les détails de la donation
  
- **Email 2** (Donateur): "Thank You for Your Donation"
  - Reçu par l'email saisi dans le formulaire
  - **Contient une pièce jointe PDF** 📎
  - Message de remerciement personnalisé

**✅ Ouvrir la pièce jointe PDF**:
- Nom du fichier: `donation_receipt_[ID].pdf`
- Contenu professionnel et complet
- Prêt à imprimer ou archiver

---

## 🎨 Personnalisation

### Modifier le Message de Remerciement
Éditez `DonController.php`, méthode `sendDonationReceiptEmail()`:
```php
$htmlMessage = "
    <div class='thank-you'>Dear " . $don->getDonateurNom() . ",</div>
    <p>VOTRE MESSAGE ICI</p>
";
```

### Modifier le Design du PDF
Éditez `DonController.php`, méthode `generateReceiptPDF()`:
```php
// Changer les couleurs
$pdf->SetFillColor(78, 115, 223); // RGB pour le bleu

// Modifier le texte
$pdf->Cell(0, 10, 'VOTRE TEXTE', 0, 1, 'L');
```

### Modifier l'Email Expéditeur
```php
$from = "donations@peaceconnect.org";
$fromName = "PeaceConnect Donations Team";
```

---

## 🔒 Sécurité et Gestion des Erreurs

### Gestion des Erreurs
- Si la génération du PDF échoue → Email envoyé quand même (sans pièce jointe)
- Si l'email échoue → Donation enregistrée quand même
- Logs d'erreur enregistrés avec `error_log()`

### Logs
Les erreurs sont enregistrées dans:
- `C:\xampp\apache\logs\error.log` (erreurs PHP)
- `C:\xampp\sendmail\error.log` (erreurs email)

### Vérifier les Logs
```powershell
# Logs PHP
Get-Content C:\xampp\apache\logs\error.log -Tail 20

# Logs Email
Get-Content C:\xampp\sendmail\error.log -Tail 20
```

---

## 📊 Avantages de cette Fonctionnalité

### Pour le Donateur
✅ Reçoit immédiatement son reçu
✅ PDF professionnel pour ses archives
✅ Confirmation claire de sa donation
✅ Pas besoin de télécharger manuellement
✅ Message de remerciement personnalisé

### Pour l'Organisation
✅ Image professionnelle
✅ Meilleure expérience utilisateur
✅ Réduit les demandes de reçus
✅ Traçabilité complète
✅ Conformité et transparence

### Technique
✅ Automatique (aucune action manuelle)
✅ Génération PDF réutilisée
✅ Structure MVC respectée
✅ Gestion d'erreurs robuste
✅ Logs pour le debugging

---

## 🆘 Dépannage

### Email non reçu par le donateur

**1. Vérifier l'email saisi**
- Assurez-vous que l'email est valide
- Pas de fautes de frappe

**2. Vérifier le dossier spam**
- L'email peut être dans les courriers indésirables

**3. Vérifier les logs**
```powershell
Get-Content C:\xampp\sendmail\error.log -Tail 30
```

**4. Tester avec Mailtrap**
- Utilisez Mailtrap pour voir tous les emails envoyés
- https://mailtrap.io

### PDF non attaché

**1. Vérifier la génération du PDF**
- Testez le téléchargement direct depuis la page de reçu

**2. Vérifier les logs PHP**
```powershell
Get-Content C:\xampp\apache\logs\error.log -Tail 30
```

**3. Vérifier TCPDF**
- Assurez-vous que `vendor/tcpdf/` existe

### Email envoyé mais vide

**1. Vérifier la configuration SMTP**
- Suivez `GUIDE_ETAPES_SIMPLES.txt`

**2. Redémarrer Apache**
- XAMPP Control Panel → Stop → Start

---

## 📝 Structure MVC Respectée

```
Model (don.php)
  ↓
Controller (DonController.php)
  ├── addDon() → Enregistre la donation
  ├── sendDonationNotificationEmail() → Email admin
  ├── sendDonationReceiptEmail() → Email donateur + PDF
  └── generateReceiptPDF() → Génère le PDF
  ↓
View (index.php, receiptDon.php)
  └── Affichage et formulaires
```

**Logique métier** = Controller ✅
**Données** = Model ✅
**Présentation** = View ✅

---

## ✨ Résumé

**Avant**: 
- Donation → Reçu téléchargeable uniquement
- Email admin seulement

**Maintenant**:
- Donation → **2 emails automatiques**
- **Donateur reçoit son reçu PDF par email** 📧📎
- Admin reçoit la notification
- Reçu toujours téléchargeable sur la page

**Impact**: Meilleure expérience utilisateur, processus professionnel, automatisation complète !

---

**🎉 La fonctionnalité est opérationnelle ! Testez-la dès maintenant !**
