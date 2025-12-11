# 🧪 Test Format Numéro de Commande

## 📋 Format Officiel du Système

**Format généré** : `CMD-YYYYMMDD-XXXXXX`

- `CMD-` : Préfixe fixe
- `YYYYMMDD` : Date (8 chiffres) - Année, Mois, Jour
- `-` : Séparateur
- `XXXXXX` : Code unique (6 caractères alphanumériques majuscules)

**Exemple** : `CMD-20251209-A1B2C3`

## 🔍 Pattern Regex Utilisé

```javascript
/^CMD-\d{8}-[A-Z0-9]{6}$/i
```

**Explication** :
- `^` : Début de chaîne
- `CMD-` : Préfixe obligatoire
- `\d{8}` : Exactement 8 chiffres (date YYYYMMDD)
- `-` : Tiret séparateur
- `[A-Z0-9]{6}` : Exactement 6 caractères alphanumériques (A-Z, 0-9)
- `$` : Fin de chaîne
- `i` : Insensible à la casse (accepte minuscules)

## ✅ Exemples Valides

| Numéro de Commande | Valide | Description |
|-------------------|--------|-------------|
| `CMD-20251209-A1B2C3` | ✅ | Format parfait |
| `CMD-20251209-123456` | ✅ | Chiffres uniquement |
| `CMD-20251209-ABCDEF` | ✅ | Lettres uniquement |
| `cmd-20251209-a1b2c3` | ✅ | Minuscules (acceptées) |
| `CMD-20250101-XYZ123` | ✅ | Date valide |

## ❌ Exemples Invalides

| Numéro de Commande | Valide | Raison |
|-------------------|--------|--------|
| `CMD-2025-123456` | ❌ | Date trop courte (4 chiffres au lieu de 8) |
| `CMD-20251209-AB` | ❌ | Code trop court (2 au lieu de 6) |
| `CMD-20251209-ABCDEFGH` | ❌ | Code trop long (8 au lieu de 6) |
| `20251209-A1B2C3` | ❌ | Manque préfixe "CMD-" |
| `CMD-20251209` | ❌ | Manque le code |
| `CMD-20251209-AB@#$%` | ❌ | Caractères spéciaux non autorisés |

## 🔧 Code de Génération (PaiementController.php)

```php
// Générer un numéro de commande unique
$numeroCommande = 'CMD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

// Exemples générés :
// CMD-20251209-5F8A2B
// CMD-20251209-9C3D1E
// CMD-20251209-7A4B6C
```

## 🧪 Tests à Effectuer

### Test 1 : Format Correct
```
Entrée : CMD-20251209-A1B2C3
Résultat attendu : ✅ Validation réussie
```

### Test 2 : Format Ancien (court)
```
Entrée : CMD-2025-123456
Résultat attendu : ❌ Format invalide (ex: CMD-20251209-A1B2C3)
```

### Test 3 : Sans Préfixe
```
Entrée : 20251209-A1B2C3
Résultat attendu : ❌ Format invalide (ex: CMD-20251209-A1B2C3)
```

### Test 4 : Code Trop Court
```
Entrée : CMD-20251209-ABC
Résultat attendu : ❌ Format invalide (ex: CMD-20251209-A1B2C3)
```

### Test 5 : Minuscules
```
Entrée : cmd-20251209-a1b2c3
Résultat attendu : ✅ Validation réussie (insensible à la casse)
```

## 📝 Fichiers Modifiés

1. **view/assets/js/suivi.js**
   - Pattern regex : `/^CMD-\d{8}-[A-Z0-9]{6}$/i`
   - Message d'erreur : "Format invalide (ex: CMD-20251209-A1B2C3)"

2. **view/front/suivi.html**
   - Placeholder : "N° de commande (ex: CMD-20251209-A1B2C3)"
   - Title : "Format: CMD-YYYYMMDD-XXXXXX (date + code)"

## 🎯 Avantages de ce Format

✅ **Unique** : Date + code aléatoire garantit l'unicité  
✅ **Traçable** : La date est visible dans le numéro  
✅ **Lisible** : Format clair et structuré  
✅ **Sécurisé** : Code aléatoire difficile à deviner  
✅ **Standardisé** : Longueur fixe (21 caractères)  

## 🔍 Vérification dans la Base de Données

Pour vérifier les numéros de commande existants :

```sql
-- Voir tous les numéros de commande
SELECT id, numero_commande, date_commande 
FROM commande 
ORDER BY date_commande DESC 
LIMIT 10;

-- Vérifier le format
SELECT 
    numero_commande,
    CASE 
        WHEN numero_commande REGEXP '^CMD-[0-9]{8}-[A-Z0-9]{6}$' 
        THEN 'Valide' 
        ELSE 'Invalide' 
    END AS format_status
FROM commande;
```

## 🚀 Test Rapide

1. Ouvrez : `http://localhost/peaceconnect/view/front/suivi.html`
2. Entrez : `CMD-20251209-A1B2C3`
3. Cliquez : "Suivre ma commande"
4. Résultat : ✅ Pas d'erreur de format

---

**Date de création** : 9 décembre 2025  
**Format validé** : `CMD-YYYYMMDD-XXXXXX`  
**Longueur totale** : 21 caractères
