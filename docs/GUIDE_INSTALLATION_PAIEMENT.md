# Guide d'installation du système de paiement - PeaceConnect

## 🚀 Vue d'ensemble

Le système de paiement PeaceConnect supporte maintenant 4 méthodes de paiement :
- ✅ **Carte bancaire** (simulation)
- ✅ **Stripe** (nécessite clés API)
- ✅ **PayPal** (nécessite clés API)
- ✅ **Virement bancaire**

---

## 📋 Prérequis

- PHP 7.4 ou supérieur
- MySQL/MariaDB
- Serveur web (Apache/XAMPP recommandé)
- Compte Stripe (optionnel) - https://dashboard.stripe.com
- Compte PayPal Developer (optionnel) - https://developer.paypal.com

---

## 🔧 Installation

### Étape 1 : Mise à jour de la base de données

Exécutez le script SQL pour mettre à jour votre base de données :

```sql
-- Mise à jour de la table commandes
ALTER TABLE commandes 
MODIFY COLUMN methode_paiement ENUM('card', 'paypal', 'virement', 'stripe') DEFAULT NULL,
ADD COLUMN IF NOT EXISTS payment_intent_id VARCHAR(100) NULL DEFAULT NULL AFTER transaction_id,
ADD COLUMN IF NOT EXISTS payment_method_details TEXT NULL DEFAULT NULL AFTER payment_intent_id;

-- Créer l'index si nécessaire
CREATE INDEX IF NOT EXISTS idx_numero_commande ON commandes(numero_commande);
```

**OU** recréez complètement la base avec :
```bash
# Dans phpMyAdmin ou en ligne de commande MySQL
DROP DATABASE IF EXISTS peaceconnect;
SOURCE database.sql;
```

### Étape 2 : Configuration des clés API

#### 2.1 Configuration Stripe

1. Créez un compte sur https://stripe.com
2. Allez dans **Dashboard** → **Developers** → **API Keys**
3. Copiez vos clés (mode test pour commencer)
4. Ouvrez `config/config_paiement.php`
5. Remplacez les valeurs suivantes :

```php
'stripe' => [
    'publishable_key' => 'pk_test_VOTRE_CLE_PUBLIQUE',
    'secret_key' => 'sk_test_VOTRE_CLE_SECRETE',
    // ...
],
```

6. Dans `view/front/paiement.html`, ligne 15, remplacez :
```javascript
<script src="https://js.stripe.com/v3/"></script>
```

7. Dans `view/assets/js/paiement.js`, ligne 138, remplacez :
```javascript
stripe = Stripe('pk_test_VOTRE_CLE_PUBLIQUE_ICI');
```

#### 2.2 Configuration PayPal

1. Créez un compte sur https://developer.paypal.com
2. Allez dans **Dashboard** → **My Apps & Credentials**
3. Créez une nouvelle application
4. Copiez votre **Client ID** et **Client Secret**
5. Ouvrez `config/config_paiement.php`
6. Remplacez les valeurs suivantes :

```php
'paypal' => [
    'mode' => 'sandbox', // Utilisez 'live' en production
    'client_id' => 'VOTRE_CLIENT_ID',
    'client_secret' => 'VOTRE_CLIENT_SECRET',
    // ...
],
```

7. Dans `view/front/paiement.html`, ligne 18, remplacez :
```html
<script src="https://www.paypal.com/sdk/js?client-id=VOTRE_CLIENT_ID&currency=EUR"></script>
```

#### 2.3 Configuration Virement Bancaire

Modifiez les informations bancaires dans `config/config_paiement.php` :

```php
'virement' => [
    'nom_banque' => 'Votre Banque',
    'titulaire' => 'Votre Nom',
    'iban' => 'FR76 XXXX XXXX XXXX XXXX XXXX XXX',
    'bic' => 'BNPAFRPPXXX',
    // ...
],
```

### Étape 3 : Vérification des fichiers

Assurez-vous que ces fichiers existent et sont à jour :

```
PeaceConnect/
├── config/
│   ├── config_paiement.php ✅ (nouveau)
│   └── config_paiement.php.example ✅ (nouveau)
├── controller/
│   └── PaiementController.php ✅ (mis à jour)
├── view/
│   ├── front/
│   │   ├── paiement.html ✅ (mis à jour)
│   │   └── confirmation.html ✅ (mis à jour)
│   └── assets/
│       └── js/
│           └── paiement.js ✅ (mis à jour)
└── database.sql ✅ (mis à jour)
```

---

## 🧪 Tests

### Test 1 : Carte bancaire (Simulation)

1. Ajoutez des produits au panier
2. Allez à la page de paiement
3. Sélectionnez "Carte Bancaire"
4. Entrez n'importe quels numéros de test :
   - Numéro : `4242 4242 4242 4242`
   - Expiration : `12/25`
   - CVV : `123`
   - Nom : `TEST USER`
5. Cliquez sur "Payer"

✅ **Résultat attendu** : Redirection vers la page de confirmation

### Test 2 : Stripe (Nécessite clés API)

1. Configurez vos clés Stripe (voir section 2.1)
2. Sélectionnez "Stripe"
3. Entrez une carte de test Stripe :
   - Numéro : `4242 4242 4242 4242`
   - Date : `12/25`
   - CVV : `123`
4. Cliquez sur "Payer avec Stripe"

✅ **Résultat attendu** : Paiement traité par Stripe

### Test 3 : PayPal (Nécessite clés API)

1. Configurez vos clés PayPal (voir section 2.2)
2. Sélectionnez "PayPal"
3. Cliquez sur le bouton PayPal
4. Connectez-vous avec un compte sandbox PayPal

✅ **Résultat attendu** : Paiement traité par PayPal

### Test 4 : Virement bancaire

1. Sélectionnez "Virement Bancaire"
2. Notez les informations bancaires affichées
3. Cliquez sur "Confirmer la commande"

✅ **Résultat attendu** : Commande créée avec statut "en attente"

---

## 🔒 Sécurité

### Recommandations importantes :

1. **Ne jamais commit les clés API** dans Git
   ```bash
   # Ajoutez à .gitignore
   echo "config/config_paiement.php" >> .gitignore
   ```

2. **Utilisez HTTPS en production**
   - Obligatoire pour Stripe et PayPal
   - Configurez un certificat SSL

3. **Variables d'environnement (Production)**
   ```php
   // Exemple avec getenv()
   'stripe' => [
       'secret_key' => getenv('STRIPE_SECRET_KEY'),
       // ...
   ],
   ```

4. **Validation côté serveur**
   - Toutes les validations sont en place dans `PaiementController.php`
   - Ne faites jamais confiance aux données client

5. **Logs de paiement**
   - Tous les paiements sont enregistrés dans la base de données
   - Transaction ID, date, montant, méthode

---

## 🐛 Dépannage

### Problème : "Stripe is not defined"

**Solution** : Vérifiez que le script Stripe est chargé dans `paiement.html` :
```html
<script src="https://js.stripe.com/v3/"></script>
```

### Problème : "PayPal SDK non chargé"

**Solution** : Remplacez `YOUR_PAYPAL_CLIENT_ID` par votre vrai Client ID dans `paiement.html`

### Problème : Erreur de connexion à la base de données

**Solution** : Vérifiez `config.php` :
```php
private static $serveur = "localhost";
private static $bdd = "peaceconnect";
private static $utilisateur = "root";
private static $mdp = "";
```

### Problème : Page blanche après paiement

**Solution** : Activez les erreurs PHP pour voir le problème :
```php
// En haut de PaiementController.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---

## 📊 Suivi des commandes

### Dans l'interface admin

1. Allez sur `view/back/commandes.html`
2. Vous verrez toutes les commandes avec :
   - Numéro de commande
   - Méthode de paiement
   - Statut du paiement
   - Transaction ID
   - Montant

### Via l'API

```javascript
// Obtenir les infos de paiement
fetch('controller/PaiementController.php?action=infos&numero=CMD-2025-123456')
  .then(r => r.json())
  .then(data => console.log(data));

// Vérifier le statut
fetch('controller/PaiementController.php?action=statut&numero=CMD-2025-123456')
  .then(r => r.json())
  .then(data => console.log(data));
```

---

## 🚀 Passage en production

### Checklist avant mise en ligne :

- [ ] Remplacer les clés de test par les clés de production
- [ ] Changer `mode` PayPal de `sandbox` à `live`
- [ ] Configurer HTTPS/SSL
- [ ] Désactiver `display_errors` PHP
- [ ] Mettre à jour les URLs de retour (success_url, cancel_url)
- [ ] Tester tous les modes de paiement
- [ ] Configurer les webhooks Stripe (optionnel)
- [ ] Sauvegarder la base de données
- [ ] Activer les logs d'erreur
- [ ] Vérifier les permissions des fichiers

### URLs à mettre à jour :

Dans `config/config_paiement.php` :
```php
'stripe' => [
    'success_url' => 'https://votredomaine.com/view/front/confirmation.html',
    'cancel_url' => 'https://votredomaine.com/view/front/paiement.html',
],
'paypal' => [
    'return_url' => 'https://votredomaine.com/view/front/confirmation.html',
    'cancel_url' => 'https://votredomaine.com/view/front/paiement.html',
],
```

---

## 📚 Documentation des API

### Endpoints disponibles

#### PaiementController.php

**GET** - Obtenir les infos de paiement
```
GET /controller/PaiementController.php?action=infos&numero=CMD-2025-123456
```

**GET** - Vérifier le statut
```
GET /controller/PaiementController.php?action=statut&numero=CMD-2025-123456
```

**POST** - Confirmer un paiement
```
POST /controller/PaiementController.php?action=confirmer
Body: {
  "numero_commande": "CMD-2025-123456",
  "methode_paiement": "stripe",
  "transaction_id": "pi_xxx",
  "statut_paiement": "paye"
}
```

**POST** - Créer session Stripe
```
POST /controller/PaiementController.php?action=stripe-session
Body: {
  "numero_commande": "CMD-2025-123456"
}
```

**POST** - Créer commande PayPal
```
POST /controller/PaiementController.php?action=paypal-order
Body: {
  "numero_commande": "CMD-2025-123456"
}
```

**POST** - Rembourser
```
POST /controller/PaiementController.php?action=rembourser
Body: {
  "numero_commande": "CMD-2025-123456"
}
```

---

## 💡 Support

Pour toute question ou problème :

1. Vérifiez la console navigateur (F12)
2. Vérifiez les logs PHP
3. Consultez la documentation Stripe/PayPal
4. Ouvrez une issue sur GitHub

---

## 📝 Changelog

### Version 2.0 (Décembre 2025)
- ✨ Ajout support Stripe
- ✨ Ajout support PayPal
- ✨ Interface de paiement améliorée
- ✨ Page de confirmation enrichie
- 🔒 Sécurité renforcée
- 📊 Meilleur suivi des transactions
- 🐛 Corrections de bugs

### Version 1.0
- Support carte bancaire
- Support virement bancaire

---

**Bon développement ! 🚀**
