# 🔗 Guide des Jointures - PeaceConnect

## 📋 Table des matières
1. [Nouvelles méthodes disponibles](#nouvelles-méthodes)
2. [Exemples d'utilisation](#exemples-dutilisation)
3. [Requêtes SQL directes](#requêtes-sql-directes)

---

## 🆕 Nouvelles Méthodes Disponibles

### **Classe Produit**

#### 1. `getStatistiques($produit_id)`
Obtenir les statistiques de vente d'un produit spécifique.

**Retourne :**
```php
[
    'id' => 1,
    'nom' => 'Nourriture pour les Affamés',
    'prix' => 29.99,
    'stock' => 50,
    'nombre_commandes' => 5,
    'quantite_vendue' => 15,
    'chiffre_affaires' => 449.85,
    'etat_stock' => 'En stock'
]
```

#### 2. `getTopVentes($limit = 5)`
Obtenir les produits les plus vendus.

#### 3. `getNonCommandes()`
Obtenir les produits jamais commandés.

#### 4. `getAllAvecStatistiques()`
Obtenir tous les produits avec leurs statistiques de vente.

---

### **Classe Panier**

#### 1. `lireToutAvecStock()`
Lire le panier avec vérification du stock.

**Retourne :**
```php
[
    'panier_id' => 1,
    'quantite_demandee' => 2,
    'produit_id' => 1,
    'nom' => 'Nourriture pour les Affamés',
    'prix' => 29.99,
    'stock_disponible' => 50,
    'disponibilite' => 'Disponible', // ou 'Stock insuffisant' ou 'Rupture de stock'
    'sous_total' => 59.98
]
```

#### 2. `verifierDisponibilite()`
Vérifier si tous les articles du panier sont disponibles.

**Retourne :**
```php
[
    'total_articles' => 3,
    'articles_disponibles' => 2,
    'articles_indisponibles' => 1
]
```

#### 3. `getDetailsComplets()`
Obtenir les détails complets du panier avec alertes de stock.

---

### **Classe Commande**

#### 1. `lireToutAvecDetails()`
Lire toutes les commandes avec le nombre de produits.

**Retourne :**
```php
[
    'id' => 1,
    'numero_commande' => 'CMD-2024-000001',
    'nom_client' => 'Jean Dupont',
    'total' => 149.95,
    'statut' => 'en_attente',
    'nombre_produits' => 3,
    'quantite_totale' => 5
]
```

#### 2. `lireCommandeComplete($numero_commande)`
Lire une commande avec tous ses produits (triple jointure).

**Retourne :**
```php
[
    'id' => 1,
    'numero_commande' => 'CMD-2024-000001',
    'nom_client' => 'Jean Dupont',
    'email_client' => 'jean@example.com',
    'total' => 149.95,
    'statut' => 'en_attente',
    'details' => [
        [
            'detail_id' => 1,
            'quantite' => 2,
            'prix_unitaire' => 29.99,
            'produit_id' => 1,
            'produit_nom' => 'Nourriture pour les Affamés',
            'image' => 'téléchargement.jpeg',
            'sous_total' => 59.98
        ],
        // ...autres produits
    ],
    'nombre_produits' => 3
]
```

#### 3. `lireParStatut($statut)`
Lire les commandes par statut avec leurs produits.

#### 4. `getStatistiquesGlobales()`
Obtenir les statistiques globales des commandes par statut.

**Retourne :**
```php
[
    [
        'statut' => 'livree',
        'nombre_commandes' => 10,
        'chiffre_affaires' => 1500.00,
        'panier_moyen' => 150.00,
        'commande_min' => 50.00,
        'commande_max' => 300.00
    ],
    // ...autres statuts
]
```

#### 5. `getResume($commande_id)`
Obtenir le résumé d'une commande pour le dashboard.

#### 6. `lireParClient($email_client)`
Obtenir toutes les commandes d'un client par email.

---

## 💻 Exemples d'Utilisation

### **Exemple 1 : Afficher le panier avec alertes de stock**

```php
<?php
require_once '../model/Panier.php';

$panier = new Panier();
$articles = $panier->lireToutAvecStock();

foreach ($articles as $article) {
    echo "<div class='panier-item'>";
    echo "<h3>{$article['nom']}</h3>";
    echo "<p>Quantité demandée : {$article['quantite_demandee']}</p>";
    echo "<p>Stock disponible : {$article['stock_disponible']}</p>";
    
    // Afficher alerte selon disponibilité
    $class = '';
    if ($article['disponibilite'] === 'Disponible') {
        $class = 'alert-success';
    } elseif ($article['disponibilite'] === 'Stock insuffisant') {
        $class = 'alert-warning';
    } else {
        $class = 'alert-danger';
    }
    
    echo "<p class='{$class}'>{$article['disponibilite']}</p>";
    echo "<p>Sous-total : {$article['sous_total']} €</p>";
    echo "</div>";
}
?>
```

### **Exemple 2 : Dashboard avec statistiques des produits**

```php
<?php
require_once '../model/Produit.php';

$produit = new Produit();

// Top 5 des ventes
$topVentes = $produit->getTopVentes(5);

echo "<h2>Top 5 des Produits</h2>";
echo "<table class='table'>";
echo "<tr><th>Produit</th><th>Quantité Vendue</th><th>Chiffre d'affaires</th></tr>";

foreach ($topVentes as $item) {
    echo "<tr>";
    echo "<td>{$item['nom']}</td>";
    echo "<td>{$item['quantite_vendue']}</td>";
    echo "<td>{$item['chiffre_affaires']} €</td>";
    echo "</tr>";
}
echo "</table>";

// Produits jamais commandés
$nonCommandes = $produit->getNonCommandes();
echo "<h2>Produits Jamais Commandés (" . count($nonCommandes) . ")</h2>";
foreach ($nonCommandes as $item) {
    echo "<p>{$item['nom']} - Stock: {$item['stock']}</p>";
}
?>
```

### **Exemple 3 : Page de suivi de commande complète**

```php
<?php
require_once '../model/Commande.php';

$commande = new Commande();
$numero = $_GET['numero'] ?? '';

$commandeComplete = $commande->lireCommandeComplete($numero);

if ($commandeComplete) {
    echo "<h2>Commande {$commandeComplete['numero_commande']}</h2>";
    echo "<p>Client : {$commandeComplete['nom_client']}</p>";
    echo "<p>Email : {$commandeComplete['email_client']}</p>";
    echo "<p>Statut : {$commandeComplete['statut']}</p>";
    echo "<p>Total : {$commandeComplete['total']} €</p>";
    
    echo "<h3>Détails ({$commandeComplete['nombre_produits']} produits)</h3>";
    echo "<table class='table'>";
    
    foreach ($commandeComplete['details'] as $detail) {
        echo "<tr>";
        echo "<td><img src='../assets/img/produits/{$detail['image']}' width='50'></td>";
        echo "<td>{$detail['produit_nom']}</td>";
        echo "<td>{$detail['quantite']} x {$detail['prix_unitaire']} €</td>";
        echo "<td>{$detail['sous_total']} €</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Commande non trouvée</p>";
}
?>
```

### **Exemple 4 : Statistiques globales pour le dashboard admin**

```php
<?php
require_once '../model/Commande.php';
require_once '../model/Produit.php';

$commande = new Commande();
$produit = new Produit();

// Statistiques des commandes par statut
$stats = $commande->getStatistiquesGlobales();

echo "<div class='row'>";
foreach ($stats as $stat) {
    echo "<div class='col-md-3'>";
    echo "<div class='card'>";
    echo "<h4>{$stat['statut']}</h4>";
    echo "<p>Commandes : {$stat['nombre_commandes']}</p>";
    echo "<p>CA : {$stat['chiffre_affaires']} €</p>";
    echo "<p>Panier moyen : {$stat['panier_moyen']} €</p>";
    echo "</div>";
    echo "</div>";
}
echo "</div>";

// Top produits
$topProduits = $produit->getTopVentes(5);
echo "<h2>Top 5 Produits</h2>";
foreach ($topProduits as $p) {
    echo "<div class='produit-stat'>";
    echo "<img src='../assets/img/produits/{$p['image']}' width='100'>";
    echo "<h4>{$p['nom']}</h4>";
    echo "<p>Vendus : {$p['quantite_vendue']}</p>";
    echo "<p>CA : {$p['chiffre_affaires']} €</p>";
    echo "</div>";
}
?>
```

### **Exemple 5 : Vérification avant validation du panier**

```php
<?php
require_once '../model/Panier.php';

$panier = new Panier();

// Vérifier la disponibilité
$disponibilite = $panier->verifierDisponibilite();

if ($disponibilite['articles_indisponibles'] > 0) {
    echo "<div class='alert alert-warning'>";
    echo "{$disponibilite['articles_indisponibles']} article(s) ne sont pas disponibles en stock.";
    echo "</div>";
    
    // Afficher les détails avec alertes
    $details = $panier->lireToutAvecStock();
    foreach ($details as $item) {
        if ($item['disponibilite'] !== 'Disponible') {
            echo "<p class='text-danger'>{$item['nom']} : {$item['disponibilite']}</p>";
        }
    }
} else {
    echo "<div class='alert alert-success'>";
    echo "Tous les articles sont disponibles !";
    echo "</div>";
    echo "<button class='btn btn-primary'>Valider la commande</button>";
}
?>
```

---

## 📊 Requêtes SQL Directes

Si vous préférez utiliser les requêtes SQL directement, elles sont disponibles dans :
📁 **`sql/jointures.sql`**

Ce fichier contient :
- ✅ Toutes les jointures commentées
- ✅ 3 vues SQL prêtes à l'emploi :
  - `vue_panier_complet`
  - `vue_commandes_details`
  - `vue_statistiques_produits`
- ✅ Requêtes de rapport et d'analyse

### Utiliser les vues SQL

```sql
-- Importer les vues
source sql/jointures.sql;

-- Ensuite utiliser dans vos requêtes
SELECT * FROM vue_panier_complet;
SELECT * FROM vue_commandes_details WHERE statut = 'en_attente';
SELECT * FROM vue_statistiques_produits ORDER BY quantite_vendue DESC LIMIT 5;
```

---

## 🎯 Cas d'Usage Recommandés

| Page | Méthode Recommandée | Pourquoi |
|------|---------------------|----------|
| **Panier** | `lireToutAvecStock()` | Affiche les alertes de stock |
| **Validation** | `verifierDisponibilite()` | Vérifie avant de commander |
| **Suivi Commande** | `lireCommandeComplete()` | Toutes les infos en 1 requête |
| **Dashboard Admin** | `getStatistiquesGlobales()` | Vue d'ensemble complète |
| **Gestion Stock** | `getAllAvecStatistiques()` | Stock + ventes combinés |
| **Top Produits** | `getTopVentes()` | Produits populaires |

---

## ⚡ Performance

Toutes les jointures utilisent des **INNER JOIN** ou **LEFT JOIN** optimisés pour :
- ✅ Réduire le nombre de requêtes SQL
- ✅ Éviter le problème N+1
- ✅ Charger toutes les données en une seule fois
- ✅ Utiliser les index de clés étrangères

---

## 🔧 Support

Pour toute question sur l'utilisation des jointures :
1. Consulter `sql/jointures.sql` pour voir les requêtes SQL brutes
2. Tester avec les exemples ci-dessus
3. Adapter selon vos besoins

**Bon développement ! 🚀**
