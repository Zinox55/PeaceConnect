# 🛒 Badge Panier Unifié

## ✅ Badge rouge au-dessus du chariot

Le badge panier est maintenant unifié sur toutes les pages avec un style moderne et cohérent.

## 🎨 Design du badge

### Caractéristiques visuelles
- **Position** : Au-dessus du chariot (top: -18px)
- **Couleur** : Dégradé rouge (#ff6b8f → #ff3d5f)
- **Forme** : Cercle parfait
- **Bordure** : Anneau blanc de 2px
- **Ombre** : Légère ombre portée rouge
- **Taille** : 18x18px (adaptatif pour 2+ chiffres)

### Animation
- **Apparition** : Scale de 0 à 1 avec effet élastique
- **Mise à jour** : Animation "bump" quand le nombre change
- **Transition** : Fluide et naturelle

## 📍 Position exacte

```
     [2]  ← Badge rouge
      🛒  ← Icône chariot
    Panier ← Texte
```

Le badge est :
- Centré horizontalement sur l'icône
- Positionné 18px au-dessus
- Toujours visible et lisible

## 🎯 Comportement

### États du badge
1. **Vide (0 articles)** : Badge invisible
2. **1-9 articles** : Badge circulaire avec 1 chiffre
3. **10-99 articles** : Badge élargi avec 2 chiffres
4. **99+ articles** : Badge avec "99+"

### Animations
- **Ajout au panier** : Animation "bump" (rebond)
- **Suppression** : Mise à jour fluide
- **Chargement** : Apparition progressive

## 💻 Code HTML

```html
<a href="panier.html" class="cart-link">
  <span class="cart-icon-wrapper">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-badge" aria-label="Articles dans le panier" role="status"></span>
  </span> 
  Panier
</a>
```

## 🎨 Code CSS

```css
.cart-badge {
  position: absolute;
  top: -18px;
  left: 50%;
  transform: translate(-50%, 0) scale(0);
  background: linear-gradient(135deg, #ff6b8f, #ff3d5f);
  color: #fff;
  font-size: 11px;
  font-weight: 600;
  min-width: 18px;
  height: 18px;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 1px 4px rgba(255, 0, 80, 0.35);
}

.cart-badge.show {
  transform: translate(-50%, 0) scale(1);
  opacity: 1;
}
```

## 📱 Responsive

### Desktop
- Badge visible dans la navbar
- Animation complète

### Mobile
- Badge visible dans le menu burger
- Même style et comportement

## 🔄 Mise à jour automatique

Le badge se met à jour automatiquement :
- ✅ Lors de l'ajout d'un produit
- ✅ Lors de la suppression d'un produit
- ✅ Lors du changement de quantité
- ✅ Au chargement de la page
- ✅ Entre les onglets (localStorage sync)

## 📊 Fichiers concernés

### CSS
- `view/front/hero-navbar.css` - Styles du badge
- `view/assets/css/navbar.css` - Styles alternatifs (backup)

### JavaScript
- `view/assets/js/cart-badge.js` - Logique du badge
- `view/assets/js/panier.js` - Gestion du panier

### HTML
Toutes les pages front office :
- ✅ index.html
- ✅ produits.html
- ✅ panier.html
- ✅ suivi.html
- ✅ commande.html

## 🎯 Accessibilité

- **aria-label** : "Articles dans le panier"
- **role** : "status" (mise à jour dynamique)
- **Contraste** : Excellent (rouge sur blanc)
- **Taille** : Suffisante pour être cliquable

## ✨ Avantages

1. **Visibilité** : Badge rouge très visible
2. **Position** : Au-dessus du chariot (standard e-commerce)
3. **Animation** : Feedback visuel immédiat
4. **Cohérence** : Même style sur toutes les pages
5. **Performance** : Mise à jour sans rechargement
6. **UX** : Utilisateur toujours informé du contenu du panier

## 🎨 Comparaison

### Avant
- ❌ Badge à côté du chariot
- ❌ Style incohérent
- ❌ Pas d'animation
- ❌ Difficile à voir

### Après
- ✅ Badge au-dessus du chariot
- ✅ Style unifié et moderne
- ✅ Animation fluide
- ✅ Très visible (rouge)
- ✅ Position standard e-commerce
