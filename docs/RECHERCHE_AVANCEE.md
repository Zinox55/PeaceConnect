# 🔍 Recherche Avancée - PeaceConnect

## 📋 Vue d'ensemble

La fonctionnalité de **Recherche Avancée** permet aux administrateurs de filtrer et rechercher des produits selon plusieurs critères simultanément avec pagination et tri personnalisable.

---

## ✨ Fonctionnalités

### 🎯 Filtres Disponibles

1. **Mot-clé** 
   - Recherche dans le nom et la description des produits
   - Insensible à la casse
   - Recherche partielle (LIKE %keyword%)

2. **Fourchette de Prix**
   - Prix minimum (€)
   - Prix maximum (€)
   - Support des décimales

3. **Fourchette de Stock**
   - Stock minimum
   - Stock maximum
   - Nombres entiers uniquement

4. **Statut du Stock**
   - **Tous** : Aucun filtre
   - **Rupture** : Stock = 0
   - **Faible** : 0 < Stock < 10
   - **OK** : Stock >= 10

5. **Tri**
   - ➖ Plus récent (date décroissante)
   - ➕ Plus ancien (date croissante)
   - 💰 Prix croissant
   - 💰 Prix décroissant
   - 📦 Stock croissant
   - 📦 Stock décroissant
   - 🔤 Nom A→Z
   - 🔤 Nom Z→A

6. **Pagination**
   - 10, 20, 50 ou 100 résultats par page
   - Navigation par page avec boutons Précédent/Suivant
   - Accès direct aux pages

---

## 🏗️ Architecture Technique

### Couche Modèle (`model/Produit.php`)

```php
/**
 * Recherche avancée avec filtres multiples
 * @param array $params Paramètres de recherche
 * @return array Résultats avec pagination
 */
public function advancedSearch($params = [])
```

**Paramètres acceptés:**
- `keyword` (string) : Mot-clé de recherche
- `prix_min` (float) : Prix minimum
- `prix_max` (float) : Prix maximum
- `stock_min` (int) : Stock minimum
- `stock_max` (int) : Stock maximum
- `statut_stock` (string) : 'rupture'|'faible'|'ok'
- `sort` (string) : Critère de tri
- `page` (int) : Numéro de page (défaut: 1)
- `limit` (int) : Éléments par page (défaut: 20, max: 100)

**Retour:**
```php
[
    'items' => [...],           // Tableau des produits
    'page' => 1,                // Page actuelle
    'limit' => 20,              // Limite par page
    'total' => 150,             // Total de résultats
    'pages' => 8,               // Nombre total de pages
    'filters_applied' => [...]  // Filtres appliqués
]
```

### Couche Contrôleur (`controller/ProduitController.php`)

**Endpoint:** `GET /controller/ProduitController.php?action=advanced_search`

**Exemple de requête:**
```
GET /controller/ProduitController.php?action=advanced_search
    &keyword=kit
    &prix_min=10
    &prix_max=50
    &statut_stock=ok
    &sort=prix_asc
    &page=1
    &limit=20
```

**Réponse JSON:**
```json
{
    "success": true,
    "data": {
        "items": [
            {
                "id": 1,
                "nom": "Kit Médiation",
                "description": "Kit complet...",
                "prix": "29.99",
                "stock": 50,
                "image": "produit_123.jpg",
                "date_creation": "2025-01-15 10:30:00"
            }
        ],
        "page": 1,
        "limit": 20,
        "total": 45,
        "pages": 3,
        "filters_applied": {
            "keyword": "kit",
            "prix_min": "10",
            "prix_max": "50",
            "statut_stock": "ok",
            "sort": "prix_asc"
        }
    }
}
```

### Interface Utilisateur (`view/back/dashboard.html`)

#### Composants UI

1. **Panneau de Recherche Pliable**
   - En-tête avec gradient violet
   - Icône chevron pour ouvrir/fermer
   - Animation de transition

2. **Grille de Filtres**
   - Layout responsive (grid CSS)
   - Labels avec icônes
   - Inputs et selects stylisés

3. **Boutons d'Action**
   - **Rechercher** (vert) : Lance la recherche
   - **Réinitialiser** (gris) : Vide les filtres
   - **Sauvegarder** (bleu) : Sauvegarde le preset

4. **Pagination**
   - Affichée en haut et en bas du tableau
   - Indicateur "X - Y sur Z résultats"
   - Navigation complète

5. **Info Résultats**
   - Bandeau bleu informatif
   - Résumé des filtres appliqués
   - Nombre total de résultats

---

## 🔒 Sécurité

### Protection SQL Injection
- ✅ Utilisation de **PDO prepared statements**
- ✅ **bindValue()** pour tous les paramètres
- ✅ Type casting strict (int, float, string)

### Validation des Entrées
- ✅ Vérification des types numériques
- ✅ Limitation de la pagination (max 100/page)
- ✅ Sanitization des strings

### Exemple de code sécurisé:
```php
if (isset($params['prix_min']) && is_numeric($params['prix_min'])) {
    $condition = " AND prix >= :prix_min";
    $sql .= $condition;
    $binds[':prix_min'] = (float)$params['prix_min'];
}

$stmt->bindValue(':prix_min', $binds[':prix_min'], PDO::PARAM_STR);
```

---

## 📊 Performance

### Optimisations Implémentées

1. **Index SQL** (recommandé)
```sql
CREATE INDEX idx_produits_prix ON produits(prix);
CREATE INDEX idx_produits_stock ON produits(stock);
CREATE INDEX idx_produits_date ON produits(date_creation);
CREATE INDEX idx_produits_nom ON produits(nom);
```

2. **Requête COUNT Séparée**
   - Compte avant pagination
   - Évite de charger toutes les données

3. **Pagination LIMIT/OFFSET**
   - Charge uniquement les résultats nécessaires
   - Réduit la charge mémoire

4. **Réutilisation des Paramètres**
   - Binds partagés entre COUNT et SELECT

---

## 🎨 Utilisation

### Dans le Dashboard Admin

1. **Accéder à la Recherche**
   - Aller dans "Gestion Produits"
   - Cliquer sur le panneau "Recherche Avancée"

2. **Configurer les Filtres**
   - Remplir un ou plusieurs critères
   - Choisir le tri
   - Sélectionner le nombre de résultats

3. **Lancer la Recherche**
   - Cliquer sur "Rechercher"
   - Les résultats s'affichent instantanément

4. **Naviguer dans les Résultats**
   - Utiliser la pagination
   - Modifier/Supprimer les produits normalement

5. **Sauvegarder un Preset**
   - Configurer vos filtres favoris
   - Cliquer sur "Sauvegarder"
   - Donner un nom au preset

### Via l'API (pour développeurs)

```javascript
// Exemple JavaScript
async function searchProducts() {
    const params = new URLSearchParams({
        action: 'advanced_search',
        keyword: 'kit',
        prix_min: 10,
        prix_max: 50,
        statut_stock: 'ok',
        sort: 'prix_asc',
        page: 1,
        limit: 20
    });
    
    const response = await fetch(
        `../../controller/ProduitController.php?${params.toString()}`
    );
    const result = await response.json();
    
    if (result.success) {
        console.log('Produits trouvés:', result.data.items);
        console.log('Total:', result.data.total);
    }
}
```

---

## 📱 Responsive Design

### Breakpoints
- **Desktop** (> 768px) : Grille 3 colonnes
- **Tablette** (≤ 768px) : Grille 1 colonne
- **Mobile** (< 576px) : Stack vertical

### Adaptations
- Boutons full-width sur mobile
- Pagination compacte
- Labels toujours visibles

---

## 🧪 Tests

### Cas de Test

1. **Recherche Simple**
```
Entrée: keyword = "kit"
Résultat attendu: Tous les produits contenant "kit"
```

2. **Fourchette de Prix**
```
Entrée: prix_min = 10, prix_max = 50
Résultat attendu: Produits entre 10€ et 50€
```

3. **Stock Faible**
```
Entrée: statut_stock = "faible"
Résultat attendu: Produits avec 0 < stock < 10
```

4. **Combinaison Multiple**
```
Entrée: keyword = "médiation", prix_max = 30, statut_stock = "ok"
Résultat attendu: Produits "médiation" <= 30€ avec stock >= 10
```

5. **Pagination**
```
Entrée: limit = 10, page = 2
Résultat attendu: Résultats 11-20
```

### Commandes de Test (via navigateur)
```
# Test basique
http://localhost/PeaceConnect/controller/ProduitController.php?action=advanced_search

# Test avec filtres
http://localhost/PeaceConnect/controller/ProduitController.php?action=advanced_search&keyword=kit&prix_min=10&prix_max=50&sort=prix_asc

# Test pagination
http://localhost/PeaceConnect/controller/ProduitController.php?action=advanced_search&page=2&limit=10
```

---

## 🐛 Dépannage

### Erreur: "Aucun résultat"
- ✅ Vérifier que des produits existent dans la base
- ✅ Élargir les critères de recherche
- ✅ Tester sans filtres

### Erreur: "Erreur recherche avancée"
- ✅ Vérifier la connexion à la base de données
- ✅ Consulter les logs PHP
- ✅ Vérifier la structure de la table `produits`

### Pagination ne fonctionne pas
- ✅ Vérifier JavaScript dans la console
- ✅ S'assurer que `executeAdvancedSearch()` est définie
- ✅ Vérifier les paramètres GET

### Filtres ne s'appliquent pas
- ✅ Vérifier les IDs des inputs HTML
- ✅ Consulter la requête dans Network (DevTools)
- ✅ Vérifier la logique SQL dans le modèle

---

## 🔮 Améliorations Futures

### Fonctionnalités Prévues

1. **Presets Avancés**
   - Chargement des presets sauvegardés
   - Partage entre utilisateurs
   - Presets par défaut système

2. **Export**
   - Export CSV des résultats
   - Export Excel
   - Export PDF

3. **Filtres Avancés**
   - Recherche par catégorie
   - Filtrage par date de création
   - Recherche par vendeur

4. **Visualisation**
   - Graphiques des résultats
   - Histogrammes de prix
   - Statistiques de stock

5. **Recherche Sauvegardée**
   - Alertes sur nouveaux résultats
   - Notifications push
   - Rapports planifiés

---

## 📚 Ressources

### Fichiers Modifiés
- `model/Produit.php` : Méthode `advancedSearch()`
- `controller/ProduitController.php` : Méthode `advancedSearch()` + route
- `view/back/dashboard.html` : UI + CSS + JavaScript

### Documentation Associée
- [Guide Complet](GUIDE_COMPLET.md)
- [API REST](../README.md#-api-rest)
- [Sécurité](../README.md#-sécurité)

### Support
- Issues GitHub: [https://github.com/Zinox55/PeaceConnect/issues](https://github.com/Zinox55/PeaceConnect/issues)
- Email: support@peaceconnect.org

---

**Développé avec ❤️ pour PeaceConnect** 🌍
