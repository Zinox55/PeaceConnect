# Installation du Système de Paiement

## Étapes d'installation

### 1. Mise à jour de la base de données

Exécutez le script SQL pour ajouter les champs de paiement :

```bash
mysql -u votre_utilisateur -p peaceconnect < sql/add_payment_fields.sql
```

Ou via phpMyAdmin :
1. Ouvrez phpMyAdmin
2. Sélectionnez la base de données `peaceconnect`
3. Allez dans l'onglet "SQL"
4. Copiez et exécutez le contenu de `sql/add_payment_fields.sql`

### 2. Vérification des fichiers

Assurez-vous que les fichiers suivants sont présents :

**Frontend :**
- ✅ `view/front/paiement.html`
- ✅ `view/front/confirmation.html`
- ✅ `view/assets/js/paiement.js`
- ✅ `view/assets/js/commande.js` (modifié)

**Backend :**
- ✅ `controller/PaiementController.php`
- ✅ `model/Commande.php` (modifié)

**SQL :**
- ✅ `sql/add_payment_fields.sql`

**Documentation :**
- ✅ `docs/PAIEMENT_GUIDE.md`

### 3. Test du système

1. **Ajouter des produits au panier**
   ```
   http://localhost/peaceconnect/view/front/produits.html
   ```

2. **Accéder au panier**
   ```
   http://localhost/peaceconnect/view/front/panier.html
   ```

3. **Remplir le formulaire de commande**
   ```
   http://localhost/peaceconnect/view/front/commande.html
   ```

4. **Choisir un mode de paiement**
   ```
   http://localhost/peaceconnect/view/front/paiement.html
   ```

5. **Vérifier la confirmation**
   ```
   http://localhost/peaceconnect/view/front/confirmation.html
   ```

### 4. Données de test

**Carte bancaire (simulation) :**
- Numéro : `4242 4242 4242 4242`
- Date d'expiration : `12/25`
- CVV : `123`
- Nom : `TEST USER`

**Virement bancaire :**
- Les informations s'affichent automatiquement
- Le statut reste "en_attente" jusqu'à confirmation manuelle

## Vérification de l'installation

### Vérifier la base de données

```sql
-- Vérifier que les colonnes existent
DESCRIBE commandes;

-- Devrait afficher :
-- methode_paiement
-- statut_paiement
-- date_paiement
-- transaction_id
```

### Vérifier les permissions

```bash
# Les fichiers doivent être accessibles en lecture
ls -la controller/PaiementController.php
ls -la view/front/paiement.html
ls -la view/assets/js/paiement.js
```

### Tester l'API

```bash
# Test de l'endpoint de paiement
curl -X GET "http://localhost/peaceconnect/controller/PaiementController.php?action=infos&numero=CMD-2025-123456"
```

## Fonctionnalités

### ✅ Implémenté

- [x] Page de sélection du mode de paiement
- [x] Formulaire de carte bancaire avec validation
- [x] Support PayPal (simulation)
- [x] Support virement bancaire
- [x] Page de confirmation
- [x] Stockage des informations de paiement
- [x] Génération d'ID de transaction
- [x] Mise à jour automatique du statut
- [x] API de gestion des paiements

### 🔄 À améliorer (Production)

- [ ] Intégration réelle Stripe/PayPal
- [ ] 3D Secure pour les cartes
- [ ] Webhooks pour les notifications
- [ ] Certificat SSL/TLS
- [ ] Tokenisation des cartes
- [ ] Conformité PCI DSS
- [ ] Tests unitaires
- [ ] Logs d'audit détaillés

## Dépannage

### Erreur : "Colonnes non trouvées"

**Solution :** Exécutez le script SQL de migration
```bash
mysql -u root -p peaceconnect < sql/add_payment_fields.sql
```

### Erreur : "localStorage non défini"

**Solution :** Vérifiez que JavaScript est activé et que vous n'êtes pas en navigation privée

### Erreur : "Panier vide"

**Solution :** Ajoutez des produits au panier avant d'accéder à la page de paiement

### Erreur : "PaiementController.php non trouvé"

**Solution :** Vérifiez le chemin et les permissions du fichier
```bash
chmod 644 controller/PaiementController.php
```

## Configuration

### Modifier les méthodes de paiement disponibles

Éditez `view/front/paiement.html` pour ajouter/supprimer des options de paiement.

### Personnaliser les messages

Éditez `view/assets/js/paiement.js` pour modifier les messages d'erreur et de succès.

### Changer les coordonnées bancaires

Éditez la section "virementInfo" dans `view/front/paiement.html`.

## Support

Pour plus d'informations, consultez :
- 📖 [Guide complet du paiement](docs/PAIEMENT_GUIDE.md)
- 📖 [Documentation générale](docs/README.md)
- 📖 [Guide de démarrage rapide](docs/DÉMARRAGE_RAPIDE.md)

---

**Installation réussie !** 🎉

Vous pouvez maintenant tester le système de paiement complet.
