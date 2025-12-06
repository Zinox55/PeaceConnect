# Migration : Ajout de la date de livraison

## 📋 Description
Cette migration ajoute la colonne `date_livraison` à la table `commandes` pour enregistrer automatiquement la date et l'heure de livraison lorsqu'une commande passe au statut "livrée".

## 🚀 Installation

### Option 1 : Via phpMyAdmin
1. Ouvrez phpMyAdmin
2. Sélectionnez la base de données `peaceconnect`
3. Allez dans l'onglet "SQL"
4. Copiez et exécutez le contenu du fichier `sql/migration_add_date_livraison.sql`

### Option 2 : Via ligne de commande MySQL
```bash
mysql -u root -p peaceconnect < sql/migration_add_date_livraison.sql
```

### Option 3 : Recréer la base de données complète
Si vous préférez recréer toute la base de données :
```bash
mysql -u root -p < database.sql
```

## ✅ Vérification
Après l'exécution de la migration, vérifiez que la colonne a été ajoutée :
```sql
DESCRIBE commandes;
```

Vous devriez voir la colonne `date_livraison` de type `TIMESTAMP NULL`.

## 📝 Fonctionnalités ajoutées

### 1. Date de création des produits
- ✅ Affichée dans le tableau des produits du back office
- ✅ Format français : `03/12/2025 14:30`
- ✅ Incluse dans l'export CSV

### 2. Date de livraison des commandes
- ✅ Enregistrée automatiquement quand le statut passe à "livrée"
- ✅ Affichée dans les détails de la commande
- ✅ Incluse dans l'export CSV
- ✅ Format français : `03/12/2025 14:30`

## 🔄 Comportement automatique
Lorsqu'un administrateur change le statut d'une commande à "livrée" :
1. Le système enregistre automatiquement la date et l'heure actuelle dans `date_livraison`
2. Cette date est affichée dans les détails de la commande
3. Elle est exportée dans le fichier CSV

## 📊 Export CSV amélioré
Les exports CSV incluent maintenant :
- **Produits** : Date de création
- **Commandes** : Date de commande + Date de livraison (si livrée)

Format optimisé pour Excel français (séparateur point-virgule, dates au format français).
