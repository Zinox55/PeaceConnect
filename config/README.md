# Configuration du Projet

Ce dossier contient les fichiers de configuration du projet PeaceConnect.

## Fichiers

### config_mail.php
**⚠️ Fichier sensible - Ne pas commiter avec de vraies informations**

Configuration du système de mailing (Gmail SMTP).

**Installation:**
1. Copier `config_mail.php.example` vers `config_mail.php`
2. Éditer `config_mail.php` avec vos informations Gmail
3. Générer un mot de passe d'application Gmail: https://myaccount.google.com/apppasswords
4. Remplacer les valeurs par défaut

**Paramètres requis:**
- `smtp.username`: Votre adresse Gmail
- `smtp.password`: Mot de passe d'application (16 caractères)
- `from.email`: Email expéditeur (même que username)
- `admin.email`: Email de l'administrateur

### config_mail.php.example
Fichier d'exemple avec la structure de configuration email.
Ce fichier peut être commité dans Git sans risque.

## Sécurité

🔒 Le fichier `config_mail.php` est ajouté au `.gitignore` pour éviter de commiter des informations sensibles.

## Structure

```
config/
├── config_mail.php          # Configuration réelle (ignoré par Git)
├── config_mail.php.example  # Template de configuration
└── README.md                # Ce fichier
```
