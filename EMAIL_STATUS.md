# 📧 Configuration Email - PeaceConnect

## ⚙️ État Actuel

**Envoi d'emails**: ❌ **DÉSACTIVÉ**

Les emails sont actuellement désactivés. Seul l'affichage du reçu de don fonctionne.

---

## 🔄 Pour Activer l'Envoi d'Emails

Si vous souhaitez réactiver l'envoi automatique d'emails après chaque donation :

### Étape 1: Configurer XAMPP pour l'envoi d'emails

Suivez le guide: `GUIDE_ETAPES_SIMPLES.txt`

### Étape 2: Activer les emails dans le code

**Fichier**: `controller/DonController.php`

**Ligne ~52-56**, décommentez ces lignes:

```php
// Changer de:
// $this->sendDonationNotificationEmail($don, $donId);
// $this->sendDonationReceiptEmail($don, $donId);

// À:
$this->sendDonationNotificationEmail($don, $donId);
$this->sendDonationReceiptEmail($don, $donId);
```

### Étape 3: Tester

Faites une donation test pour vérifier que les emails sont bien envoyés.

---

## ❌ Pour Désactiver l'Envoi d'Emails

Si vous voulez désactiver l'envoi d'emails (état actuel):

**Fichier**: `controller/DonController.php`

**Ligne ~52-56**, commentez ces lignes:

```php
// Changer de:
$this->sendDonationNotificationEmail($don, $donId);
$this->sendDonationReceiptEmail($don, $donId);

// À:
// $this->sendDonationNotificationEmail($don, $donId);
// $this->sendDonationReceiptEmail($don, $donId);
```

---

## 📋 État Actuel du Système

### ✅ Fonctionnalités Actives

- Formulaire de donation
- Enregistrement dans la base de données
- Affichage du reçu de don
- Téléchargement PDF du reçu
- Dashboard avec statistiques
- Gestion des donations (BackOffice)
- Gestion des causes (BackOffice)

### ❌ Fonctionnalités Désactivées

- Envoi d'email à l'admin (ghribiranim6@gmail.com)
- Envoi d'email au donateur avec PDF

---

## 🎯 Avantages de la Désactivation

- ✅ Pas besoin de configurer SMTP
- ✅ Fonctionne immédiatement
- ✅ Pas d'erreurs d'envoi d'email
- ✅ Plus rapide (pas d'attente email)
- ✅ Parfait pour les tests et développement

---

## 💡 Recommandation

**Pendant le développement**: Gardez les emails désactivés (état actuel)

**En production**: Activez les emails après avoir configuré SMTP

---

## 🧪 Test du Système Actuel

1. Allez sur: http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/index.php

2. Remplissez le formulaire de donation

3. Soumettez

4. **Résultat attendu**:
   - ✅ Redirection vers la page de reçu
   - ✅ Affichage des détails de la donation
   - ✅ Bouton de téléchargement PDF fonctionne
   - ❌ Aucun email envoyé

---

## 📞 Support

Pour toute question sur la configuration email, consultez:
- `GUIDE_ETAPES_SIMPLES.txt`
- `SOLUTION_RAPIDE_EMAIL.md`
- `RECU_EMAIL_FONCTIONNALITE.md`

---

**📌 Note**: Le code des emails est toujours présent et fonctionnel, il est simplement commenté pour faciliter les tests sans configuration SMTP.
