# 🏠 Configuration Page d'Accueil

## ✅ Configuration Appliquée

Le projet est maintenant configuré pour afficher automatiquement la page d'accueil lorsque vous accédez au dossier front.

## 🌐 URLs d'Accès

### Accès Direct à l'Accueil

```
http://localhost/peaceconnect/
→ Redirige vers → http://localhost/peaceconnect/view/front/

http://localhost/peaceconnect/view/front/
→ Affiche → index.html (Page d'accueil)
```

### Autres Pages

```
http://localhost/peaceconnect/view/front/produits.html
http://localhost/peaceconnect/view/front/panier.html
http://localhost/peaceconnect/view/front/suivi.html
http://localhost/peaceconnect/view/front/commande.html
```

## 📄 Fichiers Créés

### 1. `view/front/index.html`

Page d'accueil avec :
- Hero section attractive
- Boutons CTA vers Produits et Suivi
- Section Features (4 avantages)
- Footer complet
- Animations CSS

### 2. `view/front/.htaccess`

Configuration Apache pour :
- Définir `index.html` comme page par défaut
- Désactiver l'affichage de la liste des fichiers
- Activer la compression GZIP
- Configurer le cache des ressources

### 3. `.htaccess` (racine)

Configuration pour :
- Rediriger `/peaceconnect/` vers `/peaceconnect/view/front/`
- Protéger les fichiers sensibles (config.php, database.sql)
- Protéger le dossier .git

## 🎨 Page d'Accueil

### Structure

```
┌─────────────────────────────────────┐
│         Navbar Transparent          │
├─────────────────────────────────────┤
│                                     │
│         Hero Section                │
│   "Bienvenue sur PeaceConnect"      │
│                                     │
│   [Découvrir] [Suivre commande]    │
│                                     │
├─────────────────────────────────────┤
│                                     │
│      Pourquoi PeaceConnect ?        │
│                                     │
│  [❤️]      [🛡️]     [🚚]     [🎧]  │
│ Solidaire Sécurisé Rapide Support  │
│                                     │
├─────────────────────────────────────┤
│            Footer                   │
└─────────────────────────────────────┘
```

### Fonctionnalités

✅ **Hero Animé**
- Animations fadeInUp
- Fond image avec overlay
- 2 boutons CTA

✅ **Section Features**
- 4 cartes avec icônes
- Effet hover
- Grid responsive

✅ **Navigation**
- Même navbar que les autres pages
- Badge panier dynamique
- Menu mobile

## 🔧 Configuration Apache

### Vérifier que mod_rewrite est activé

```bash
# Dans httpd.conf ou apache2.conf
LoadModule rewrite_module modules/mod_rewrite.so

# Autoriser .htaccess
<Directory "C:/xampp/htdocs">
    AllowOverride All
</Directory>
```

### Redémarrer Apache

```bash
# XAMPP
Arrêter et redémarrer Apache depuis le panneau de contrôle
```

## 📱 Responsive

La page d'accueil est entièrement responsive :

### Desktop (> 1024px)
- Hero pleine largeur
- Features en grille 4 colonnes
- Navbar complète

### Tablet (768px - 1024px)
- Features en grille 2 colonnes
- Navbar réduite

### Mobile (< 768px)
- Features en 1 colonne
- Burger menu
- Boutons CTA empilés

## 🎯 Personnalisation

### Modifier le Texte du Hero

Éditez `view/front/index.html` :

```html
<h1>Bienvenue sur PeaceConnect</h1>
<p class="lead">Votre message personnalisé ici</p>
```

### Modifier l'Image de Fond

```html
<section class="hero" style="background-image:url('VOTRE_IMAGE.jpg');">
```

### Ajouter/Modifier les Features

```html
<div class="feature-card">
  <div class="feature-icon">
    <i class="fas fa-VOTRE-ICONE"></i>
  </div>
  <h3>Titre</h3>
  <p>Description</p>
</div>
```

## 🚀 Test

### 1. Accès Direct

Ouvrez votre navigateur et allez à :
```
http://localhost/peaceconnect/
```

Vous devriez être automatiquement redirigé vers la page d'accueil.

### 2. Accès au Dossier Front

```
http://localhost/peaceconnect/view/front/
```

La page d'accueil s'affiche automatiquement (pas de liste de fichiers).

### 3. Navigation

Cliquez sur "Découvrir nos produits" → Redirige vers `produits.html`

## ⚠️ Dépannage

### Problème : Liste de fichiers affichée

**Cause** : `.htaccess` non pris en compte

**Solution** :
1. Vérifier que `AllowOverride All` est activé dans `httpd.conf`
2. Redémarrer Apache
3. Vérifier que le fichier `.htaccess` existe dans `view/front/`

### Problème : Erreur 404

**Cause** : Chemin incorrect dans `.htaccess`

**Solution** :
Vérifier le `RewriteBase` dans `.htaccess` racine :
```apache
RewriteBase /peaceconnect/
```

Si votre projet est dans un autre dossier, ajustez le chemin.

### Problème : Redirection ne fonctionne pas

**Cause** : `mod_rewrite` non activé

**Solution** :
1. Ouvrir `httpd.conf`
2. Décommenter : `LoadModule rewrite_module modules/mod_rewrite.so`
3. Redémarrer Apache

## 📊 Avantages

✅ **Expérience Utilisateur**
- Pas de liste de fichiers confuse
- Page d'accueil professionnelle
- Navigation intuitive

✅ **SEO**
- URL propre
- Page d'accueil indexable
- Structure claire

✅ **Sécurité**
- Liste de fichiers désactivée
- Fichiers sensibles protégés
- Dossier .git caché

## 🎨 Améliorations Futures

- [ ] Ajouter un slider de produits vedettes
- [ ] Intégrer des témoignages clients
- [ ] Ajouter une section "Nos actions"
- [ ] Créer une page "À propos"
- [ ] Ajouter un formulaire de contact

---

**Date de création** : 9 décembre 2025  
**Fichiers créés** : index.html, .htaccess (x2)  
**Accès** : http://localhost/peaceconnect/
