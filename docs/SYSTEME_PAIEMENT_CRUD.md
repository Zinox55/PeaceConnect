# 💳 Système de Paiement PeaceConnect - Documentation

## 🎯 Vue d'ensemble

Le système de paiement est maintenant **complètement intégré** dans la page de commande avec support CRUD complet.

## 📋 Fonctionnalités

### ✅ Méthodes de paiement supportées
1. **Carte bancaire** - Paiement direct avec carte
2. **Stripe** - Intégration Stripe (mode simulation)
3. **PayPal** - Paiement via PayPal (mode simulation)
4. **Virement bancaire** - Instructions de virement

### ✅ Opérations CRUD

#### CREATE (Créer)
- **Endpoint**: `PaiementController.php?action=creer`
- **Méthode**: POST
- **Fonction**: Crée une commande complète avec paiement
- **Actions**:
  - Crée l'enregistrement commande
  - Ajoute les détails (articles)
  - Met à jour le stock
  - Vide le panier
  - Génère transaction ID
  - Enregistre les détails de paiement

#### READ (Lire)
- **Endpoint 1**: `PaiementController.php?action=infos&numero=CMD-XXX`
- **Endpoint 2**: `PaiementController.php?action=statut&numero=CMD-XXX`
- **Méthode**: GET
- **Fonction**: Récupère les informations de paiement

#### UPDATE (Mettre à jour)
- **Endpoint**: `PaiementController.php?action=confirmer`
- **Méthode**: POST
- **Fonction**: Confirme/met à jour un paiement existant

#### DELETE (Supprimer/Rembourser)
- **Endpoint**: `PaiementController.php?action=rembourser`
- **Méthode**: POST
- **Fonction**: Rembourse un paiement et annule la commande

## 🔄 Flux de paiement

```
1. Utilisateur sur commande.html
   ↓
2. Remplit formulaire de livraison
   ↓
3. Clique "Continuer vers le paiement"
   ↓
4. Section paiement s'affiche (même page)
   ↓
5. Choisit une méthode de paiement
   ↓
6. Clique "Payer maintenant"
   ↓
7. Appel API: PaiementController.php?action=creer
   ↓
8. Serveur:
   - Crée la commande
   - Ajoute les articles
   - Met à jour stock
   - Vide panier
   - Enregistre paiement
   ↓
9. Redirection vers confirmation.html
   ↓
10. Affichage numéro de commande
```

## 📁 Structure des fichiers

### Backend (PHP)
```
controller/
├── PaiementController.php          # Contrôleur principal de paiement
│   ├── creerCommandeAvecPaiement() # CREATE - Nouvelle commande avec paiement
│   ├── confirmerPaiement()         # UPDATE - Confirmer un paiement
│   ├── getInfosPaiement()          # READ - Infos paiement
│   ├── verifierStatut()            # READ - Statut paiement
│   ├── rembourser()                # DELETE - Rembourser
│   ├── creerSessionStripe()        # Stripe integration
│   └── creerPaiementPayPal()       # PayPal integration
│
├── PanierController.php            # Gestion du panier
└── CommandeController.php          # Gestion des commandes
```

### Frontend
```
view/front/
├── commande.html                   # Page avec formulaire + paiement intégré
├── confirmation.html               # Page de confirmation
├── produits.html                   # Page des produits
└── panier.html                     # Page du panier

view/assets/js/
├── commande.js                     # Logique formulaire + paiement
│   ├── passerCommande()           # Validation et préparation
│   ├── afficherSectionPaiement()  # Affichage section paiement
│   ├── afficherResumeCommande()   # Résumé commande
│   ├── setupPaymentOptions()      # Gestion options de paiement
│   ├── afficherFormulaireMethode() # Formulaires spécifiques
│   └── traiterPaiement()          # Envoi au serveur
│
├── panier.js                      # Gestion panier
└── cart-badge.js                  # Badge nombre d'articles
```

### Tests
```
tests/
└── test_paiement_complet_v2.html  # Test automatique du système

verif_paiement.php                 # Vérification base de données
```

## 🗄️ Base de données

### Table: commandes
```sql
id                      INT PRIMARY KEY AUTO_INCREMENT
numero_commande         VARCHAR(50) UNIQUE NOT NULL
nom_client             VARCHAR(255) NOT NULL
email_client           VARCHAR(255) NOT NULL
telephone_client       VARCHAR(20)
adresse_client         TEXT NOT NULL
total                  DECIMAL(10,2) NOT NULL
statut                 ENUM('en_attente', 'confirmee', 'livree', 'annulee')
methode_paiement       ENUM('card', 'paypal', 'virement', 'stripe')
statut_paiement        ENUM('en_attente', 'paye', 'echoue', 'rembourse')
date_paiement          TIMESTAMP NULL
transaction_id         VARCHAR(100)
payment_intent_id      VARCHAR(100)
payment_method_details TEXT
date_commande          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
date_livraison         TIMESTAMP NULL

INDEX idx_statut_paiement (statut_paiement)
INDEX idx_methode_paiement (methode_paiement)
INDEX idx_numero_commande (numero_commande)
```

## 🧪 Tests

### 1. Vérification automatique
```
http://localhost/PeaceConnect/verif_paiement.php
```
Affiche:
- Structure de la base de données
- Dernières commandes
- Statistiques de paiement
- État du panier

### 2. Test interactif
```
http://localhost/PeaceConnect/tests/test_paiement_complet_v2.html
```
Permet de:
- Vérifier l'environnement
- Tester l'API panier
- Créer une commande test
- Tester chaque méthode de paiement

### 3. Test utilisateur complet
```
1. http://localhost/PeaceConnect/view/front/produits.html
   → Ajouter des produits au panier

2. http://localhost/PeaceConnect/view/front/panier.html
   → Voir le panier

3. http://localhost/PeaceConnect/view/front/commande.html
   → Remplir formulaire
   → Choisir méthode de paiement
   → Payer

4. confirmation.html
   → Voir le résumé
```

## 📊 Format des données

### Requête de création de commande
```json
{
  "client": {
    "nom": "Dhia Eddin Hamdouni",
    "email": "hamdounidhiaeddine@gmail.com",
    "telephone": "0612345678",
    "adresse": "123 Rue de Test, Paris"
  },
  "articles": [
    {
      "id": 1,
      "nom": "Nourriture pour les Affamés",
      "prix": 29.99,
      "quantite": 2,
      "image": "téléchargement.jpeg"
    }
  ],
  "total": 59.98,
  "methode_paiement": "card"
}
```

### Réponse de succès
```json
{
  "success": true,
  "message": "Commande créée avec succès",
  "numero_commande": "CMD-20251205-A1B2C3",
  "transaction_id": "CARD-1234567890ABCDEF",
  "statut_paiement": "paye",
  "commande_id": 15
}
```

### Réponse d'erreur
```json
{
  "success": false,
  "message": "Stock insuffisant pour Nourriture pour les Affamés"
}
```

## 🔐 Sécurité

### Validations côté serveur
- ✅ Validation des données client
- ✅ Vérification de la méthode de paiement
- ✅ Contrôle du stock disponible
- ✅ Transaction SQL (rollback en cas d'erreur)
- ✅ Génération de numéros uniques

### Validations côté client
- ✅ Validation des champs du formulaire
- ✅ Vérification du panier non vide
- ✅ Confirmation avant paiement

## 🎨 Interface utilisateur

### Sections
1. **Informations de livraison**
   - Formulaire avec validation en temps réel
   - Bordures vertes/rouges selon validation
   - Messages d'erreur clairs

2. **Section paiement** (cachée initialement)
   - Résumé de commande (gauche)
   - Options de paiement (droite)
   - Formulaires dynamiques selon méthode
   - Bouton retour pour modifier
   - Design responsive

### Animations
- Transition douce entre sections
- Effets hover sur options de paiement
- Spinner pendant traitement
- Scroll automatique vers le haut

## 🚀 Utilisation en production

### Configuration Stripe
```php
// config/config_paiement.php
'stripe' => [
    'publishable_key' => 'pk_live_VOTRE_CLE',
    'secret_key' => 'sk_live_VOTRE_CLE'
]
```

### Configuration PayPal
```php
// config/config_paiement.php
'paypal' => [
    'client_id' => 'VOTRE_CLIENT_ID',
    'client_secret' => 'VOTRE_SECRET',
    'mode' => 'live' // ou 'sandbox' pour tests
]
```

## 📝 Logs et débogage

### Console navigateur
Tous les événements sont loggés:
```javascript
📦 Début passerCommande avec données
📡 Réponse reçue du panier: 200
🛒 Données panier: {...}
💾 Sauvegarde dans localStorage: {...}
✅ Affichage section paiement...
💳 Traitement paiement: card {...}
✅ Réponse paiement: {...}
```

### Logs serveur
À implémenter avec:
```php
error_log('Paiement créé: ' . $numeroCommande);
```

## ❓ Dépannage

### Le panier est vide
- Ajoutez des produits via produits.html
- Vérifiez la table `panier` en BDD

### Erreur base de données
- Vérifiez que toutes les colonnes existent
- Exécutez `database.sql` pour recréer

### La page de paiement ne s'affiche pas
- Ouvrez la console (F12)
- Vérifiez les logs JavaScript
- Testez avec test_paiement_complet_v2.html

### Stock insuffisant
- Vérifiez le stock dans la table `produits`
- Augmentez le stock si nécessaire

## 📞 Support

Pour toute question, consultez:
1. `verif_paiement.php` - État du système
2. Console navigateur (F12) - Erreurs JavaScript
3. Logs Apache/PHP - Erreurs serveur

---

✅ **Le système est maintenant complètement fonctionnel avec CRUD complet !**
