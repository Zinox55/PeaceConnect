# 🔧 Solution : Images Anciennes qui ne s'affichent pas

## 🎯 Problème identifié

Les **nouveaux produits** (ajoutés via le back office) s'affichent correctement, mais les **anciens produits** (créés lors de l'installation) affichent le logo au lieu de leur vraie image.

### Exemple
- ✅ "jus" → Image correcte (produit récent)
- ❌ "Nourriture pour les Affamés" → Logo (produit ancien)

## 🔍 Cause du problème

Les anciens produits dans la base de données ont :
1. Un champ `image` vide (NULL ou '')
2. Un nom d'image incorrect
3. Un chemin d'image qui n'existe plus

## ✅ Solutions (3 options)

### Option 1 : Correction automatique via SQL (Recommandé)

**Étape 1** : Ouvrez phpMyAdmin

**Étape 2** : Sélectionnez la base `peaceconnect`

**Étape 3** : Exécutez le fichier `sql/fix_old_images.sql`

Ce script va :
- Mettre à jour les 6 produits initiaux avec les bonnes images
- Vérifier que les images existent
- Afficher un résumé

**Résultat** :
```
✅ Nourriture pour les Affamés → téléchargement.jpeg
✅ Éducation pour les Enfants → enfants-classe.jpg.jpeg
✅ Soins de Santé → téléchargement (2).jpeg
✅ Eau Pure → téléchargement (1).jpeg
✅ Soutien aux Moyens de Subsistance → téléchargement (3).jpeg
✅ Logement Digne → téléchargement (4).jpeg
```

---

### Option 2 : Test et diagnostic via PHP

**Étape 1** : Ouvrez `test_images_disponibles.php` dans votre navigateur

**Étape 2** : Vérifiez :
- Toutes les images disponibles dans `/img/`
- Toutes les images disponibles dans `/produits/`
- Les produits dans la base de données
- Les chemins calculés pour chaque produit

**Étape 3** : Identifiez les images manquantes (bordure rouge)

**Étape 4** : Corrigez manuellement dans la base de données

---

### Option 3 : Mise à jour manuelle via Back Office

**Étape 1** : Allez dans le back office → Gestion Produits

**Étape 2** : Pour chaque produit ancien :
1. Cliquez sur "Modifier"
2. Uploadez une nouvelle image
3. Sauvegardez

**Avantage** : Les nouvelles images seront automatiquement dans `/produits/` avec le bon format

---

## 📊 Vérification après correction

### Test 1 : Console du navigateur
1. Ouvrez la page panier (F12 → Console)
2. Cherchez les logs : `PANIER ITEM RAW:` et `IMAGE RESOLVED:`
3. Vérifiez que les chemins sont corrects

### Test 2 : Affichage visuel
1. Ajoutez des produits au panier
2. Vérifiez que toutes les images s'affichent
3. Allez sur la page suivi
4. Vérifiez que les images sont identiques

### Test 3 : Script de test
```bash
# Ouvrez dans le navigateur
http://localhost/votre-projet/test_images_disponibles.php
```

## 🗂️ Structure des images

### Images fixes (anciennes)
```
view/assets/img/
├── logo.png                      ← Fallback par défaut
├── téléchargement.jpeg           ← Nourriture
├── enfants-classe.jpg.jpeg       ← Éducation
├── téléchargement (1).jpeg       ← Eau
├── téléchargement (2).jpeg       ← Santé
├── téléchargement (3).jpeg       ← Subsistance
└── téléchargement (4).jpeg       ← Logement
```

### Images uploadées (nouvelles)
```
view/assets/img/produits/
├── produit_1763544064_691d8c006b732.jpeg
├── produit_1763544121_691d8c39a70d7.jpeg
└── ...
```

## 🔄 Logique de résolution

Le code JavaScript résout les chemins ainsi :

```javascript
const rawImage = (item.image || '').trim();

if (rawImage) {
    if (rawImage.startsWith('produit_')) {
        // Nouvelle image → /produits/
        imagePath = `../assets/img/produits/${rawImage}`;
    } else {
        // Ancienne image → /img/
        imagePath = `../assets/img/${rawImage}`;
    }
} else {
    // Pas d'image → logo
    imagePath = '../assets/img/logo.png';
}
```

## ⚠️ Problèmes courants

### Problème 1 : Image NULL dans la base
**Symptôme** : Logo affiché partout
**Solution** : Exécutez `sql/fix_old_images.sql`

### Problème 2 : Fichier image manquant
**Symptôme** : Logo affiché même avec un nom d'image
**Solution** : Vérifiez que le fichier existe dans `/img/` ou `/produits/`

### Problème 3 : Mauvais chemin
**Symptôme** : Console affiche "404 Not Found"
**Solution** : Vérifiez les logs dans la console (F12)

### Problème 4 : Cache navigateur
**Symptôme** : Corrections non visibles
**Solution** : Videz le cache (Ctrl+Shift+Delete)

## 📝 Commandes SQL utiles

### Voir tous les produits et leurs images
```sql
SELECT id, nom, image FROM produits ORDER BY id;
```

### Compter les produits par type d'image
```sql
SELECT 
    CASE 
        WHEN image IS NULL OR image = '' THEN 'Sans image'
        WHEN image LIKE 'produit_%' THEN 'Image uploadée'
        ELSE 'Image fixe'
    END AS type,
    COUNT(*) AS nombre
FROM produits
GROUP BY type;
```

### Mettre à jour un produit spécifique
```sql
UPDATE produits 
SET image = 'téléchargement.jpeg' 
WHERE nom = 'Nourriture pour les Affamés';
```

## ✅ Checklist finale

- [ ] Exécuter `sql/fix_old_images.sql`
- [ ] Ouvrir `test_images_disponibles.php`
- [ ] Vérifier que toutes les images existent
- [ ] Tester l'affichage sur la page produits
- [ ] Tester l'affichage dans le panier
- [ ] Tester l'affichage sur la page suivi
- [ ] Vider le cache du navigateur
- [ ] Vérifier les logs de la console (F12)

## 🎉 Résultat attendu

Après correction, **toutes les images** doivent s'afficher correctement sur :
- ✅ Page Produits
- ✅ Page Panier
- ✅ Page Suivi
- ✅ Page Commande

Les anciens produits afficheront leurs vraies images au lieu du logo !
