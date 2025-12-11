# 🚀 Guide d'Installation - PeaceConnect

## ⚠️ Problème actuel
PHP n'est pas installé sur votre système Windows.

## 📦 Solution 1 : XAMPP (Recommandé pour débutants)

### Étape 1 : Télécharger XAMPP
1. Aller sur : **https://www.apachefriends.org/**
2. Cliquer sur "Download" pour Windows
3. Télécharger XAMPP (inclut Apache, MySQL, PHP)

### Étape 2 : Installer XAMPP
1. Lancer le fichier `.exe` téléchargé
2. Installer dans `C:\xampp`
3. Cocher : **Apache**, **MySQL**, **PHP**, **phpMyAdmin**
4. Terminer l'installation

### Étape 3 : Démarrer les services
1. Ouvrir **XAMPP Control Panel** (icône sur le bureau)
2. Cliquer sur **Start** pour :
   - ✅ Apache (serveur web)
   - ✅ MySQL (base de données)

### Étape 4 : Copier votre projet
```powershell
# Dans PowerShell (en tant qu'administrateur)
Copy-Item -Recurse "c:\Users\user\Desktop\2A\PeaceConnect - Copie - Copie" "C:\xampp\htdocs\PeaceConnect"
```

### Étape 5 : Créer la base de données
1. Ouvrir : **http://localhost/phpmyadmin**
2. Cliquer sur "Nouvelle base de données"
3. Nom : `peaceconnect`
4. Encodage : `utf8mb4_unicode_ci`
5. Cliquer sur "Créer"
6. Cliquer sur "Importer"
7. Choisir le fichier : `C:\xampp\htdocs\PeaceConnect\database.sql`
8. Cliquer sur "Exécuter"

### Étape 6 : Tester
Ouvrir dans votre navigateur :
- **BackOffice** : http://localhost/PeaceConnect/view/back/produits.html
- **FrontOffice** : http://localhost/PeaceConnect/view/front/produits.html
- **Panier** : http://localhost/PeaceConnect/view/front/panier.html

---

## 📦 Solution 2 : PHP + MySQL séparément (Avancé)

### Étape 1 : Installer PHP

#### 1.1 Télécharger PHP
1. Aller sur : **https://windows.php.net/download/**
2. Télécharger : **PHP 8.2 VC15 x64 Thread Safe** (fichier .zip)
3. Extraire dans `C:\php\`

#### 1.2 Configurer PHP
```powershell
# Dans PowerShell (Administrateur)

# Copier le fichier de configuration
Copy-Item "C:\php\php.ini-development" "C:\php\php.ini"

# Activer les extensions nécessaires
(Get-Content C:\php\php.ini) -replace ';extension=mysqli', 'extension=mysqli' | Set-Content C:\php\php.ini
(Get-Content C:\php\php.ini) -replace ';extension=pdo_mysql', 'extension=pdo_mysql' | Set-Content C:\php\php.ini
(Get-Content C:\php\php.ini) -replace ';extension=mbstring', 'extension=mbstring' | Set-Content C:\php\php.ini

# Ajouter PHP au PATH
[Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\php", [EnvironmentVariableTarget]::Machine)
```

#### 1.3 Vérifier l'installation
```powershell
# Fermer et rouvrir PowerShell, puis :
php -v
```

### Étape 2 : Installer MySQL

#### 2.1 Télécharger MySQL
1. Aller sur : **https://dev.mysql.com/downloads/installer/**
2. Télécharger : **MySQL Installer for Windows**
3. Choisir "mysql-installer-community"

#### 2.2 Installer MySQL
1. Lancer l'installeur
2. Choisir : **Developer Default**
3. Configuration :
   - Root Password : (laisser vide ou mettre un mot de passe)
   - Port : **3306**
4. Terminer l'installation

#### 2.3 Créer la base de données
```bash
# Dans l'invite de commandes
cd "C:\Program Files\MySQL\MySQL Server 8.0\bin"
mysql -u root -p

# Dans MySQL
CREATE DATABASE peaceconnect CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Importer le fichier SQL
mysql -u root -p peaceconnect < "c:\Users\user\Desktop\2A\PeaceConnect - Copie - Copie\database.sql"
```

### Étape 3 : Démarrer le serveur PHP
```powershell
cd "c:\Users\user\Desktop\2A\PeaceConnect - Copie - Copie"
php -S localhost:8000
```

### Étape 4 : Tester
Ouvrir : **http://localhost:8000/view/front/produits.html**

---

## 📦 Solution 3 : Docker (Pour développeurs avancés)

### Créer un docker-compose.yml
```yaml
version: '3.8'
services:
  php:
    image: php:8.2-apache
    ports:
      - "8000:80"
    volumes:
      - ./:/var/www/html
    depends_on:
      - mysql
  
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: peaceconnect
    ports:
      - "3306:3306"
    volumes:
      - ./database.sql:/docker-entrypoint-initdb.d/database.sql
```

### Lancer
```bash
docker-compose up -d
```

---

## 🔍 Vérification de l'installation

### Test PHP
```powershell
php -v
# Doit afficher : PHP 8.x.x
```

### Test MySQL
```powershell
mysql --version
# Doit afficher : mysql Ver 8.x.x
```

### Test connexion base de données
```powershell
mysql -u root -p -e "SHOW DATABASES;"
# Doit lister 'peaceconnect'
```

### Test serveur web
```powershell
cd "c:\Users\user\Desktop\2A\PeaceConnect - Copie - Copie"
php -S localhost:8000
```
Ouvrir : http://localhost:8000

---

## ❓ Problèmes fréquents

### Erreur : "php n'est pas reconnu"
**Cause** : PHP n'est pas dans le PATH  
**Solution** :
```powershell
# Ajouter manuellement au PATH
$env:Path += ";C:\php"
# OU pour XAMPP
$env:Path += ";C:\xampp\php"
```

### Erreur : "Access denied for user 'root'@'localhost'"
**Cause** : Mot de passe MySQL incorrect  
**Solution** : Modifier `config.php` pour ajuster l'utilisateur/mot de passe
```php
$dsn = 'mysql:host=localhost;dbname=peaceconnect;charset=utf8mb4';
self::$pdo = new PDO($dsn, 'root', 'votre_mot_de_passe');
```

### Erreur : "Could not find driver"
**Cause** : Extension PDO MySQL non activée  
**Solution** : Dans `php.ini`, décommenter :
```ini
extension=pdo_mysql
extension=mysqli
```

### Erreur : "Table 'peaceconnect.produits' doesn't exist"
**Cause** : Base de données non créée  
**Solution** : Importer `database.sql`
```bash
mysql -u root -p peaceconnect < database.sql
```

---

## ✅ Checklist d'installation

- [ ] PHP installé (version 7.4+)
- [ ] MySQL installé (version 5.7+)
- [ ] Base de données `peaceconnect` créée
- [ ] Fichier `database.sql` importé
- [ ] Extensions PHP activées (pdo_mysql, mysqli)
- [ ] Serveur démarré (Apache ou `php -S`)
- [ ] Page accessible : http://localhost:8000

---

## 🎯 Récapitulatif des URLs

Une fois installé, voici les URLs importantes :

| Page | URL |
|------|-----|
| **Admin - Produits** | http://localhost:8000/view/back/produits.html |
| **Catalogue public** | http://localhost:8000/view/front/produits.html |
| **Panier** | http://localhost:8000/view/front/panier.html |
| **Commander** | http://localhost:8000/view/front/commande.html |
| **Suivi** | http://localhost:8000/view/front/suivi.html |

---

## 📞 Besoin d'aide ?

Si vous rencontrez des problèmes, vérifiez :
1. Les logs PHP : `php -S localhost:8000` (affiche les erreurs)
2. Les logs MySQL : dans le dossier d'installation MySQL
3. La console du navigateur (F12)

**Recommandation** : Pour un débutant, **XAMPP est la solution la plus simple** ! 🚀
