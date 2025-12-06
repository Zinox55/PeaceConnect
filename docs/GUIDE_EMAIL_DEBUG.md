# 📧 Guide de diagnostic - Email de confirmation

## 🔍 Problème
Les emails de confirmation de commande ne sont pas envoyés sur Gmail.

## ✅ Fichiers de test disponibles

### 1. **test_email_simple.php** - Test de base
🔗 `http://localhost/PeaceConnect/test_email_simple.php`

**Ce qu'il fait :**
- Vérifie que PHPMailer est installé
- Teste la connexion SMTP Gmail
- Affiche les erreurs détaillées avec debug
- Envoie un email de test simple

**Quand l'utiliser :** Pour vérifier la configuration SMTP de base

---

### 2. **test_email_controller.html** - Test du système complet
🔗 `http://localhost/PeaceConnect/test_email_controller.html`

**Ce qu'il fait :**
- Récupère la dernière commande automatiquement
- Teste l'envoi d'email via EmailController
- Affiche les réponses JSON
- Détecte les erreurs de format

**Quand l'utiliser :** Pour tester le flux complet comme dans confirmation.html

---

### 3. **voir_logs_emails.php** - Consulter l'historique
🔗 `http://localhost/PeaceConnect/voir_logs_emails.php`

**Ce qu'il fait :**
- Affiche tous les logs d'envoi d'emails
- Montre les succès ✅ et erreurs ❌
- Historique complet par mois

**Quand l'utiliser :** Pour voir si des emails ont été tentés

---

## 🔧 Checklist de diagnostic

### Étape 1 : Vérifier la configuration Gmail
1. Ouvrez `config/config_mail.php`
2. Vérifiez :
   - ✅ `username` = votre email Gmail complet
   - ✅ `password` = mot de passe d'application (16 caractères sans espaces)
   - ✅ `host` = smtp.gmail.com
   - ✅ `port` = 587
   - ✅ `secure` = 'tls'

### Étape 2 : Générer un mot de passe d'application Gmail
1. Allez sur : https://myaccount.google.com/apppasswords
2. Activez l'authentification à 2 facteurs si demandé
3. Créez un mot de passe d'application pour "Mail"
4. Copiez le mot de passe (16 caractères)
5. Mettez-le dans `config/config_mail.php` → `'password' => 'xxxx xxxx xxxx xxxx'`

### Étape 3 : Tester la connexion
1. Ouvrez `test_email_simple.php`
2. Regardez le debug SMTP
3. Si erreur d'authentification → vérifier le mot de passe d'application
4. Si timeout → vérifier pare-feu/antivirus

### Étape 4 : Tester le système complet
1. Ouvrez `test_email_controller.html`
2. Cliquez sur "Test complet"
3. Vérifiez que le JSON est bien retourné
4. Si "Réponse invalide" → le mode debug PHPMailer casse le JSON

### Étape 5 : Passer une vraie commande
1. Allez sur `view/front/commande.html`
2. Remplissez le formulaire et passez commande
3. Sur la page de confirmation, regardez le message d'email
4. Vérifiez Gmail (et le dossier spam)

---

## ⚠️ Erreurs courantes et solutions

### Erreur : "Invalid password"
**Cause :** Le mot de passe d'application est incorrect
**Solution :**
1. Générez un nouveau mot de passe d'application sur Google
2. Remplacez dans `config/config_mail.php`
3. N'utilisez PAS votre mot de passe Gmail normal

### Erreur : "Could not connect to SMTP host"
**Cause :** Port bloqué ou connexion internet
**Solution :**
1. Vérifiez votre connexion internet
2. Désactivez temporairement le pare-feu/antivirus
3. Essayez le port 465 avec SSL au lieu de 587 TLS

### Erreur : "Réponse invalide (pas du JSON)"
**Cause :** Le mode debug PHPMailer génère du HTML
**Solution :**
1. Dans `config/config_mail.php`, mettez `'debug' => false`
2. Rechargez la page

### Pas d'email reçu mais pas d'erreur
**Cause :** Email dans le spam ou délai d'envoi
**Solution :**
1. Vérifiez le dossier spam de Gmail
2. Attendez 1-2 minutes (délai SMTP)
3. Vérifiez les logs avec `voir_logs_emails.php`

---

## 📝 Modifications apportées

### ✅ Fichiers modifiés :
1. **confirmation.html** - Ajout de l'appel `envoyerEmailConfirmation()`
2. **CommandeController.php** - Ajout action `derniere` pour tests
3. **config_mail.php** - Mode debug désactivé pour la production
4. **Mailer.php** - Ajout du logging des emails

### ✅ Fichiers créés :
1. **test_email_simple.php** - Test SMTP de base
2. **test_email_controller.html** - Test du contrôleur
3. **voir_logs_emails.php** - Visualisation des logs

---

## 🚀 Prochaines étapes

1. **Testez d'abord** `test_email_simple.php` pour confirmer que Gmail fonctionne
2. **Ensuite** testez `test_email_controller.html` pour vérifier le système complet
3. **Enfin** passez une vraie commande et vérifiez l'email

---

## 📞 Support

Si le problème persiste après avoir suivi ce guide :
1. Consultez les logs dans `logs/emails_YYYY-MM.log`
2. Vérifiez les erreurs PHP dans `logs/commande_errors.log`
3. Activez temporairement le debug dans `config_mail.php` pour voir les détails

---

**Date de création :** 6 décembre 2025
**Version :** 1.0
