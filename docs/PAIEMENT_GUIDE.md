# Guide du Système de Paiement

## Vue d'ensemble

Le système de paiement de PeaceConnect permet aux clients de finaliser leurs achats de manière sécurisée avec plusieurs méthodes de paiement.

## Flux de Paiement

### 1. Panier → Commande → Paiement → Confirmation

```
Panier (panier.html)
    ↓ Clic sur "Passer commande"
Formulaire Client (commande.html)
    ↓ Validation des informations
Page de Paiement (paiement.html)
    ↓ Sélection du mode de paiement
Confirmation (confirmation.html)
```

### 2. Étapes détaillées

#### Étape 1 : Panier
- Le client ajoute des produits au panier
- Il peut modifier les quantités ou supprimer des articles
- Clic sur "Passer commande" pour continuer

#### Étape 2 : Informations Client
- Formulaire avec validation en temps réel :
  - Nom (minimum 3 caractères)
  - Email (format valide)
  - Téléphone (format français)
  - Adresse de livraison (minimum 10 caractères)
- Les données sont sauvegardées dans `localStorage`

#### Étape 3 : Paiement
- Affichage du résumé de la commande
- Choix du mode de paiement :
  - **Carte bancaire** : Formulaire avec numéro, date d'expiration, CVV
  - **PayPal** : Redirection vers PayPal (simulation)
  - **Virement bancaire** : Affichage des coordonnées bancaires
- Validation et création de la commande

#### Étape 4 : Confirmation
- Affichage du numéro de commande
- Détails de la commande et du paiement
- Options : Suivre la commande ou continuer les achats

## Structure de la Base de Données

### Nouveaux champs dans la table `commandes`

```sql
ALTER TABLE commandes 
ADD COLUMN methode_paiement ENUM('card', 'paypal', 'virement') DEFAULT NULL,
ADD COLUMN statut_paiement ENUM('en_attente', 'paye', 'echoue', 'rembourse') DEFAULT 'en_attente',
ADD COLUMN date_paiement TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN transaction_id VARCHAR(100) NULL DEFAULT NULL;
```

### Statuts de paiement

- **en_attente** : Paiement non encore effectué (virement bancaire)
- **paye** : Paiement confirmé (carte, PayPal)
- **echoue** : Paiement refusé
- **rembourse** : Commande annulée et remboursée

## Fichiers du Système

### Frontend

1. **view/front/paiement.html**
   - Interface de sélection du mode de paiement
   - Formulaire de carte bancaire
   - Informations de virement

2. **view/front/confirmation.html**
   - Page de confirmation après paiement
   - Affichage des détails de la commande

3. **view/assets/js/paiement.js**
   - Gestion de l'interface de paiement
   - Validation des données de carte
   - Communication avec l'API

4. **view/assets/js/commande.js** (modifié)
   - Redirection vers la page de paiement
   - Sauvegarde des données dans localStorage

### Backend

1. **controller/PaiementController.php**
   - Confirmation du paiement
   - Mise à jour du statut
   - Gestion des remboursements

2. **model/Commande.php** (modifié)
   - Nouveaux champs de paiement
   - Méthodes pour gérer les paiements

### SQL

1. **sql/add_payment_fields.sql**
   - Script de migration pour ajouter les champs de paiement

## API Endpoints

### PaiementController

#### POST /controller/PaiementController.php?action=confirmer
Confirme un paiement

**Body:**
```json
{
  "numero_commande": "CMD-2025-123456",
  "methode_paiement": "card",
  "transaction_id": "TXN-1234567890",
  "statut_paiement": "paye"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Paiement confirmé",
  "transaction_id": "TXN-1234567890"
}
```

#### GET /controller/PaiementController.php?action=infos&numero=CMD-2025-123456
Récupère les informations de paiement

**Response:**
```json
{
  "success": true,
  "data": {
    "methode_paiement": "card",
    "statut_paiement": "paye",
    "date_paiement": "2025-12-05 14:30:00",
    "transaction_id": "TXN-1234567890"
  }
}
```

#### POST /controller/PaiementController.php?action=rembourser
Rembourse une commande

**Body:**
```json
{
  "numero_commande": "CMD-2025-123456"
}
```

## Sécurité

### Mesures implémentées

1. **Validation côté client**
   - Formatage automatique du numéro de carte
   - Validation de la date d'expiration
   - Vérification du CVV

2. **Validation côté serveur**
   - Vérification des méthodes de paiement
   - Validation des données de commande
   - Protection contre les injections SQL (PDO)

3. **Données sensibles**
   - Les numéros de carte ne sont PAS stockés
   - Seul l'ID de transaction est conservé
   - Communication HTTPS recommandée en production

### Recommandations pour la production

1. **Intégration réelle**
   - Utiliser Stripe, PayPal API, ou autre gateway
   - Implémenter 3D Secure pour les cartes
   - Ajouter des webhooks pour les notifications

2. **Sécurité renforcée**
   - Certificat SSL/TLS obligatoire
   - Tokenisation des cartes bancaires
   - Logs d'audit des transactions
   - Rate limiting sur les tentatives de paiement

3. **Conformité**
   - PCI DSS pour les paiements par carte
   - RGPD pour les données personnelles
   - Mentions légales et CGV

## Personnalisation

### Ajouter une nouvelle méthode de paiement

1. **Base de données**
```sql
ALTER TABLE commandes 
MODIFY COLUMN methode_paiement ENUM('card', 'paypal', 'virement', 'nouvelle_methode');
```

2. **Frontend (paiement.html)**
```html
<div class="payment-option" data-method="nouvelle_methode">
  <i class="fas fa-icon"></i>
  <h4>Nouvelle Méthode</h4>
</div>
```

3. **JavaScript (paiement.js)**
```javascript
// Ajouter la logique spécifique
if (methodePaiementSelectionnee === 'nouvelle_methode') {
    // Traitement spécifique
}
```

4. **Backend (PaiementController.php)**
```php
$methodesValides = ['card', 'paypal', 'virement', 'nouvelle_methode'];
```

## Tests

### Données de test

**Carte bancaire (simulation)**
- Numéro : 4242 4242 4242 4242
- Date : 12/25
- CVV : 123
- Nom : TEST USER

**Virement bancaire**
- IBAN : FR76 1234 5678 9012 3456 7890 123
- BIC : BNPAFRPPXXX

## Dépannage

### Problème : Le paiement ne se confirme pas

**Solution :**
1. Vérifier que les champs de paiement existent dans la base de données
2. Exécuter le script `sql/add_payment_fields.sql`
3. Vérifier les logs dans `logs/commande_errors.log`

### Problème : Redirection vers paiement échoue

**Solution :**
1. Vérifier que `localStorage` est activé dans le navigateur
2. Vérifier la console JavaScript pour les erreurs
3. S'assurer que le panier n'est pas vide

### Problème : Transaction ID non généré

**Solution :**
1. Vérifier que `PaiementController.php` est accessible
2. Vérifier les permissions du fichier
3. Consulter les logs PHP

## Support

Pour toute question ou problème :
- Consulter la documentation complète dans `/docs`
- Vérifier les logs dans `/logs`
- Contacter le support technique

---

**Version :** 1.0  
**Date :** Décembre 2025  
**Auteur :** PeaceConnect Development Team
