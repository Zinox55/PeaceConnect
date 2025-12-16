# PeaceConnect - Blog de Gestion d'Articles

## 🚀 Installation

1. Clonez le projet dans `C:\xampp\htdocs\`
2. Importez `setup.sql` dans votre base de données
3. Configurez `config.php` avec vos identifiants de base de données
4. **Configurez l'envoi d'emails** (voir ci-dessous)

## 📧 Configuration Email Newsletter (IMPORTANT!)

Le système de newsletter est **déjà installé** mais nécessite une configuration rapide (3 minutes).

### ⚠️ Pourquoi les emails ne sont pas envoyés?

XAMPP **ne peut PAS envoyer d'emails par défaut**. Vous devez configurer Gmail SMTP.

### ✅ Configuration Rapide

1. **Ouvrez la page de configuration:**
   ```
   http://localhost/PeaceConnecti/PeaceConnect/config_email.php
   ```

2. **Créez un mot de passe d'application Gmail:**
   - Allez sur: https://myaccount.google.com/apppasswords
   - Créez un mot de passe pour "Mail"
   - Copiez le mot de passe (16 caractères)

3. **Modifiez `email_config.php`:**
   ```php
   public static $smtp_username = 'votre-email@gmail.com';
   public static $smtp_password = 'xxxx xxxx xxxx xxxx'; // Mot de passe d'app
   ```

4. **Testez sur:** `config_email.php`

### 📖 Guide Détaillé

Consultez `CONFIGURATION_EMAIL.txt` pour un guide complet en français.

## ✨ Fonctionnalités

- ✅ Gestion complète des articles (CRUD)
- ✅ Dashboard administrateur professionnel
- ✅ Frontend moderne avec animations
- ✅ **Système de newsletter par email**
- ✅ Commentaires et likes
- ✅ Upload d'images
- ✅ Statuts d'articles (Brouillon/Approuvé)

## 🔗 URLs Importantes

- Dashboard Admin: `http://localhost/PeaceConnecti/PeaceConnect/view/back/dashboard.php`
- Page Frontend: `http://localhost/PeaceConnecti/PeaceConnect/view/Front/list_articles.php`
- **Configuration Email:** `http://localhost/PeaceConnecti/PeaceConnect/config_email.php`
- Test Email: `http://localhost/PeaceConnecti/PeaceConnect/test_email.php`
