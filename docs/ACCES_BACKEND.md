# 🔐 Accès au Backend (Back Office)

## 🌐 URLs d'Accès

### Accès Principal

```
http://localhost/peaceconnect/view/back/
→ Redirige automatiquement vers dashboard.html
```

### Accès Direct au Dashboard

```
http://localhost/peaceconnect/view/back/dashboard.html
```

## 📊 Pages Disponibles

### 1. Dashboard (Tableau de Bord)
```
http://localhost/peaceconnect/view/back/dashboard.html
```
- Vue d'ensemble des statistiques
- Graphiques de ventes
- Commandes récentes
- Statistiques en temps réel

### 2. Gestion des Produits
```
http://localhost/peaceconnect/view/back/produits.html
```
- Liste des produits
- Ajouter/Modifier/Supprimer produits
- Gestion du stock
- Upload d'images

### 3. Gestion des Commandes
```
http://localhost/peaceconnect/view/back/commandes.html
```
- Liste des commandes
- Détails des commandes
- Changement de statut
- Historique

### 4. Gestion du Stock
```
http://localhost/peaceconnect/view/back/stock.html
```
- Suivi du stock
- Alertes de rupture
- Réapprovisionnement

### 5. Statistiques
```
http://localhost/peaceconnect/view/back/statistiques.html
```
- Statistiques détaillées
- Graphiques avancés
- Rapports de ventes
- Analyses

### 6. Export des Données

**Export CSV** :
```
http://localhost/peaceconnect/view/back/export_csv.html
```

**Export Excel** :
```
http://localhost/peaceconnect/view/back/export_excel.html
```

**Aperçu Export** :
```
http://localhost/peaceconnect/view/back/apercu_export.html
```

### 7. Gestion des Clients
```
http://localhost/peaceconnect/view/back/clients.html
```
- Liste des clients
- Historique des commandes par client
- Informations de contact

## 🔑 Authentification

**Note** : Actuellement, le backend n'a pas de système d'authentification.

### Pour Ajouter une Authentification

1. Créer une page `login.html`
2. Créer un contrôleur `AuthController.php`
3. Ajouter une table `admin` dans la base de données
4. Protéger toutes les pages avec une session PHP

## 📁 Structure Backend

```
view/back/
├── index.html              # Redirection vers dashboard
├── dashboard.html          # Tableau de bord principal
├── produits.html           # Gestion produits
├── commandes.html          # Gestion commandes
├── stock.html              # Gestion stock
├── statistiques.html       # Statistiques
├── clients.html            # Gestion clients
├── export_csv.html         # Export CSV
├── export_excel.html       # Export Excel
├── export_commandes.html   # Export commandes
├── apercu_export.html      # Aperçu exports
├── header.html             # Header commun
├── footer.html             # Footer commun
└── .htaccess              # Configuration Apache
```

## 🎨 Interface

Le backend utilise le template **SB Admin 2** :
- Design moderne et responsive
- Sidebar de navigation
- Graphiques Chart.js
- Tables DataTables
- Icônes Font Awesome

## 🚀 Démarrage Rapide

### 1. Accéder au Backend

```
http://localhost/peaceconnect/view/back/
```

### 2. Navigation

Utilisez le menu latéral (sidebar) pour naviguer entre les différentes sections :
- 📊 Dashboard
- 📦 Produits
- 🛒 Commandes
- 📊 Stock
- 📈 Statistiques
- 👥 Clients
- 📥 Exports

### 3. Fonctionnalités Principales

**Dashboard** :
- Cartes de statistiques (Commandes, Revenus, Produits, Clients)
- Graphique des ventes
- Liste des commandes récentes

**Produits** :
- CRUD complet (Create, Read, Update, Delete)
- Upload d'images
- Gestion du stock

**Commandes** :
- Liste complète des commandes
- Filtrage et recherche
- Changement de statut
- Détails de commande

## 🔧 Configuration

### Personnaliser le Titre

Éditez chaque fichier HTML :
```html
<title>Votre Titre - PeaceConnect Admin</title>
```

### Modifier le Logo

Remplacez le logo dans la sidebar :
```html
<a class="sidebar-brand" href="dashboard.html">
    <div class="sidebar-brand-icon">
        <i class="fas fa-heart"></i>
    </div>
    <div class="sidebar-brand-text">PeaceConnect</div>
</a>
```

### Changer les Couleurs

Modifiez `../assets/css/sb-admin-2.min.css` ou ajoutez des styles personnalisés.

## 📱 Responsive

Le backend est entièrement responsive :
- **Desktop** : Sidebar visible
- **Tablet** : Sidebar collapsible
- **Mobile** : Menu hamburger

## 🔒 Sécurité (À Implémenter)

### Recommandations

1. **Ajouter une authentification** :
   - Page de login
   - Sessions PHP
   - Vérification sur chaque page

2. **Protéger les contrôleurs** :
   - Vérifier les permissions
   - Valider les entrées
   - Prévenir les injections SQL

3. **HTTPS** :
   - Utiliser HTTPS en production
   - Certificat SSL

4. **Logs** :
   - Logger les actions admin
   - Tracer les modifications

## 🎯 Améliorations Futures

- [ ] Système d'authentification
- [ ] Gestion des rôles (Admin, Modérateur)
- [ ] Logs d'activité
- [ ] Notifications en temps réel
- [ ] Mode sombre
- [ ] Multi-langue
- [ ] API REST pour le backend
- [ ] Dashboard personnalisable

## 📊 Statistiques Disponibles

### Dashboard
- Nombre total de commandes
- Revenu total
- Nombre de produits
- Nombre de clients

### Statistiques Avancées
- Ventes par période
- Produits les plus vendus
- Clients les plus actifs
- Évolution du chiffre d'affaires

## 📥 Exports

### Formats Disponibles
- **CSV** : Compatible Excel, Google Sheets
- **Excel** : Format .xlsx natif
- **PDF** : (À implémenter)

### Données Exportables
- Commandes
- Produits
- Clients
- Statistiques

## 🆘 Dépannage

### Problème : Page blanche

**Solution** :
1. Vérifier que Apache est démarré
2. Vérifier les chemins des fichiers CSS/JS
3. Consulter la console du navigateur (F12)

### Problème : Données non affichées

**Solution** :
1. Vérifier la connexion à la base de données
2. Vérifier les contrôleurs PHP
3. Consulter les logs PHP

### Problème : Erreur 404

**Solution** :
1. Vérifier l'URL
2. Vérifier que le fichier existe
3. Vérifier les permissions

## 📞 Support

Pour toute question sur le backend :
1. Consultez la documentation dans `docs/`
2. Vérifiez les logs dans `logs/`
3. Testez avec les fichiers dans `tests/`

---

**Date de création** : 9 décembre 2025  
**Accès** : http://localhost/peaceconnect/view/back/  
**Template** : SB Admin 2
