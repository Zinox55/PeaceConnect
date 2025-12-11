# 🔍 Debug Images Panier

## Problème
Les images ne s'affichent pas correctement dans le panier alors qu'elles s'affichent sur la page produits.

## ✅ Vérifications effectuées

### 1. Code JavaScript (panier.js)
Le code est **CORRECT** :
```javascript
const rawImage = (item.image || '').trim();
let imagePath = '../assets/img/logo.png';

if (rawImage) {
    if (rawImage.startsWith('produit_')) {
        imagePath = `../assets/img/produits/${rawImage}`;
    } else {
        imagePath = `../assets/img/${rawImage}`;
    }
}
```

### 2. Requête SQL (Panier.php)
La requête récupère bien le champ `image` :
```sql
SELECT p.id as panier_id, pr.id, pr.nom, pr.description, pr.prix, 
       pr.image, p.quantite, (pr.prix * p.quantite) as sous_total
FROM panier p
INNER JOIN produits pr ON p.produit_id = pr.id
```

### 3. Structure des dossiers
```
view/assets/img/
├── logo.png
├── téléchargement.jpeg
├── enfants-classe.jpg.jpeg
└── produits/
    ├── produit_1763544064_691d8c006b732.jpeg
    ├── produit_1763544121_691d8c39a70d7.jpeg
    └── ...
```

## 🧪 Test de diagnostic

Ouvrez le fichier `test_panier_images.html` dans votre navigateur pour :
1. Voir les données brutes retournées par l'API
2. Vérifier le champ `image` de chaque produit
3. Tester l'affichage des images
4. Identifier les chemins incorrects

## 🔧 Solutions possibles

### Solution 1 : Vérifier la base de données
Exécutez cette requête SQL pour voir les images stockées :
```sql
SELECT p.id, pr.nom, pr.image 
FROM panier p
INNER JOIN produits pr ON p.produit_id = pr.id;
```

### Solution 2 : Vérifier la console du navigateur
1. Ouvrez la page panier
2. Appuyez sur F12
3. Allez dans l'onglet "Console"
4. Cherchez les logs :
   - `PANIER ITEM RAW:` - Données brutes
   - `IMAGE RESOLVED:` - Chemin calculé

### Solution 3 : Vérifier les chemins d'images
Les images doivent être :
- **Nouvelles images** : `produit_XXXXX.jpeg` → dans `/produits/`
- **Anciennes images** : `téléchargement.jpeg` → dans `/img/`

### Solution 4 : Mettre à jour les produits
Si les produits ont des anciennes images, vous pouvez :
1. Aller dans le back office
2. Modifier chaque produit
3. Uploader une nouvelle image
4. Sauvegarder

## 📊 Comparaison Page Produits vs Panier

### Page Produits (produit-front.js)
```javascript
if (produit.image.startsWith('produit_')) {
    imagePath = `../assets/img/produits/${produit.image}`;
} else {
    imagePath = `../assets/img/${produit.image}`;
}
```

### Page Panier (panier.js)
```javascript
if (rawImage.startsWith('produit_')) {
    imagePath = `../assets/img/produits/${rawImage}`;
} else {
    imagePath = `../assets/img/${rawImage}`;
}
```

**Le code est identique !** ✅

## 🎯 Cause probable

Le problème vient probablement de :
1. **Base de données** : Le champ `image` est vide ou NULL pour certains produits
2. **Produits anciens** : Les produits ont des images qui n'existent plus
3. **Cache navigateur** : Le navigateur affiche une ancienne version

## 🔄 Actions à faire

1. **Ouvrir test_panier_images.html** pour voir les données exactes
2. **Vérifier la console** (F12) pour voir les logs
3. **Vider le cache** du navigateur (Ctrl+Shift+Delete)
4. **Mettre à jour les images** des produits dans le back office

## 💡 Note importante

Le système fonctionne avec un fallback :
```html
<img src="${imagePath}" onerror="this.src='../assets/img/logo.png'">
```

Si vous voyez le logo, c'est que :
- L'image n'existe pas à l'emplacement spécifié
- Le chemin est incorrect
- Le fichier a été supprimé

## ✅ Vérification rapide

Exécutez cette commande SQL pour voir tous les produits et leurs images :
```sql
SELECT id, nom, image, 
       CASE 
           WHEN image IS NULL THEN '❌ NULL'
           WHEN image = '' THEN '❌ VIDE'
           WHEN image LIKE 'produit_%' THEN '✅ Nouvelle image'
           ELSE '⚠️ Ancienne image'
       END AS statut_image
FROM produits
ORDER BY id;
```

Cela vous montrera quels produits ont des problèmes d'images.
