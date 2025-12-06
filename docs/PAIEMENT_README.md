# 💳 Système de Paiement PeaceConnect

## Vue d'ensemble

Système de paiement complet pour la plateforme e-commerce PeaceConnect avec support de **4 méthodes de paiement** :

- 💳 **Carte bancaire** (simulation pour tests)
- 🔵 **Stripe** (paiement sécurisé international)
- 🅿️ **PayPal** (paiement express)
- 🏦 **Virement bancaire** (paiement différé)

---

## 🚀 Démarrage rapide

### 1. Mise à jour de la base de données

```bash
# Option A : Migration depuis une base existante
mysql -u root -p peaceconnect < sql/migration_paiement_v2.sql

# Option B : Création complète
mysql -u root -p peaceconnect < database.sql
```

### 2. Configuration

Copiez et configurez le fichier de configuration :

```bash
cp config/config_paiement.php.example config/config_paiement.php
```

Éditez `config/config_paiement.php` avec vos clés API :

```php
'stripe' => [
    'publishable_key' => 'pk_test_VOTRE_CLE_ICI',
    'secret_key' => 'sk_test_VOTRE_CLE_ICI',
],
'paypal' => [
    'client_id' => 'VOTRE_CLIENT_ID_ICI',
    'client_secret' => 'VOTRE_SECRET_ICI',
],
```

### 3. Tests

Ouvrez dans votre navigateur :
```
http://localhost/PeaceConnect/tests/test_paiement_complet.html
```

---

## 📦 Structure des fichiers

```
PeaceConnect/
├── config/
│   ├── config_paiement.php              # Configuration paiements
│   └── config_paiement.php.example      # Template de configuration
│
├── controller/
│   └── PaiementController.php           # API de gestion des paiements
│
├── model/
│   └── Commande.php                     # Modèle de données
│
├── view/
│   ├── front/
│   │   ├── paiement.html               # Interface de paiement
│   │   └── confirmation.html           # Page de confirmation
│   └── assets/
│       └── js/
│           └── paiement.js             # Logique frontend
│
├── sql/
│   ├── migration_paiement_v2.sql       # Script de migration
│   └── add_payment_fields.sql          # Ajout champs paiement
│
├── tests/
│   └── test_paiement_complet.html      # Suite de tests
│
├── database.sql                         # Base complète
├── GUIDE_INSTALLATION_PAIEMENT.md      # Guide détaillé
└── PAIEMENT_README.md                  # Ce fichier
```

---

## 🔌 API Endpoints

### PaiementController

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `?action=infos&numero=XXX` | Obtenir infos de paiement |
| GET | `?action=statut&numero=XXX` | Vérifier statut paiement |
| POST | `?action=confirmer` | Confirmer un paiement |
| POST | `?action=stripe-session` | Créer session Stripe |
| POST | `?action=paypal-order` | Créer commande PayPal |
| POST | `?action=rembourser` | Rembourser une commande |

### Exemples d'utilisation

**Confirmer un paiement :**
```javascript
fetch('controller/PaiementController.php?action=confirmer', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        numero_commande: 'CMD-2025-123456',
        methode_paiement: 'stripe',
        transaction_id: 'pi_xxx',
        statut_paiement: 'paye'
    })
});
```

**Vérifier le statut :**
```javascript
fetch('controller/PaiementController.php?action=statut&numero=CMD-2025-123456')
    .then(r => r.json())
    .then(data => console.log(data));
```

---

## 🗄️ Structure de la base de données

### Table : commandes

| Colonne | Type | Description |
|---------|------|-------------|
| id | INT | Identifiant unique |
| numero_commande | VARCHAR(50) | Numéro de commande (CMD-YYYY-XXXXXX) |
| methode_paiement | ENUM | card, paypal, virement, stripe |
| statut_paiement | ENUM | en_attente, paye, echoue, rembourse |
| transaction_id | VARCHAR(100) | ID de transaction |
| payment_intent_id | VARCHAR(100) | ID PaymentIntent Stripe |
| payment_method_details | TEXT | Détails JSON du paiement |
| date_paiement | TIMESTAMP | Date du paiement |
| total | DECIMAL(10,2) | Montant total |
| ... | ... | Autres colonnes |

---

## 🎨 Interface utilisateur

### Page de paiement (`paiement.html`)

1. **Résumé de commande** - Affichage du panier et du total
2. **Sélection de méthode** - Choix entre 4 options de paiement
3. **Formulaires dynamiques** :
   - Carte : Numéro, expiration, CVV, nom
   - Stripe : Élément Stripe intégré
   - PayPal : Boutons PayPal natifs
   - Virement : Informations bancaires
4. **Validation** - Vérification côté client
5. **Traitement** - Animation de chargement
6. **Redirection** - Vers page de confirmation

### Page de confirmation (`confirmation.html`)

1. **Animation de succès** - Icône avec animation
2. **Détails de commande** - Numéro, date, montant
3. **Informations de paiement** - Méthode, statut, transaction ID
4. **Informations client** - Nom, email, adresse
5. **Actions** - Suivre commande, continuer achats

---

## 🔒 Sécurité

### Mesures implémentées

✅ **Validation côté serveur** - Toutes les données sont validées
✅ **Échappement XSS** - htmlspecialchars() sur toutes les entrées
✅ **Requêtes préparées** - Protection contre injection SQL
✅ **ENUM strict** - Limitation des valeurs possibles
✅ **Vérification d'existence** - Validation des commandes
✅ **Transactions BD** - Intégrité des données
✅ **Logs complets** - Traçabilité des paiements

### Recommandations production

🔐 **HTTPS obligatoire** - Certificat SSL requis
🔑 **Variables d'environnement** - Pas de clés dans le code
🚫 **Désactiver display_errors** - Pas d'infos sensibles
📝 **Logs sécurisés** - Rotation et archivage
🔍 **Monitoring** - Alertes sur transactions suspectes
🔄 **Backups réguliers** - Sauvegarde base de données

---

## 🧪 Tests

### Cartes de test

**Stripe :**
- Succès : `4242 4242 4242 4242`
- Décliné : `4000 0000 0000 0002`
- Expire : `12/25` | CVV : `123`

**PayPal :**
- Compte sandbox à créer sur developer.paypal.com
- Test avec compte personnel sandbox

### Scénarios de test

1. ✅ **Paiement par carte** - Transaction simulée
2. ✅ **Paiement Stripe** - Avec vraies clés test
3. ✅ **Paiement PayPal** - Via sandbox
4. ✅ **Virement bancaire** - Commande en attente
5. ✅ **Validation formulaire** - Champs obligatoires
6. ✅ **Gestion d'erreurs** - Messages appropriés
7. ✅ **Page confirmation** - Affichage complet

---

## 📊 Statistiques et reporting

### Requêtes utiles

**Statistiques par méthode :**
```sql
SELECT 
    methode_paiement,
    COUNT(*) as nb_transactions,
    SUM(total) as montant_total,
    AVG(total) as montant_moyen
FROM commandes
WHERE statut_paiement = 'paye'
GROUP BY methode_paiement;
```

**Commandes en attente :**
```sql
SELECT numero_commande, nom_client, total, date_commande
FROM commandes
WHERE statut_paiement = 'en_attente'
ORDER BY date_commande DESC;
```

**Revenus du jour :**
```sql
SELECT 
    DATE(date_paiement) as jour,
    COUNT(*) as nb_paiements,
    SUM(total) as revenus
FROM commandes
WHERE statut_paiement = 'paye'
  AND DATE(date_paiement) = CURDATE()
GROUP BY DATE(date_paiement);
```

---

## 🐛 Dépannage

### Problèmes courants

**"Stripe is not defined"**
```html
<!-- Vérifiez dans paiement.html -->
<script src="https://js.stripe.com/v3/"></script>
```

**"PayPal SDK non chargé"**
```html
<!-- Remplacez YOUR_PAYPAL_CLIENT_ID -->
<script src="https://www.paypal.com/sdk/js?client-id=VOTRE_CLIENT_ID&currency=EUR"></script>
```

**Erreur base de données**
```bash
# Vérifiez la connexion
php -r "require 'config.php'; var_dump(config::getConnexion());"
```

**Page blanche**
```php
// Activez les erreurs temporairement
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---

## 📚 Documentation complète

Pour plus de détails, consultez :

- 📖 **[GUIDE_INSTALLATION_PAIEMENT.md](GUIDE_INSTALLATION_PAIEMENT.md)** - Installation pas à pas
- 🧪 **[tests/test_paiement_complet.html](tests/test_paiement_complet.html)** - Suite de tests automatisés
- 💾 **[sql/migration_paiement_v2.sql](sql/migration_paiement_v2.sql)** - Script de migration
- 🎯 **[SYSTEME_PAIEMENT_COMPLET.md](SYSTEME_PAIEMENT_COMPLET.md)** - Documentation technique

---

## 🔄 Versions

### v2.0 (Décembre 2025) - Version actuelle
- ✨ Support Stripe
- ✨ Support PayPal
- ✨ Interface améliorée
- ✨ Nouveaux champs BD
- 🔒 Sécurité renforcée

### v1.0
- Support carte bancaire
- Support virement

---

## 📧 Support

Pour toute question :

1. Consultez le guide d'installation
2. Lancez les tests automatisés
3. Vérifiez les logs d'erreur
4. Consultez la documentation Stripe/PayPal

---

## 📄 Licence

© 2025 PeaceConnect - Tous droits réservés

---

**Fait avec ❤️ pour PeaceConnect**
