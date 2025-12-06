# 🚀 Quick Start - Système de Paiement

## ⚡ Installation en 3 étapes

### 1️⃣ Base de données (2 min)

```bash
# Dans phpMyAdmin ou terminal MySQL
mysql -u root -p peaceconnect < sql/migration_paiement_v2.sql
```

### 2️⃣ Configuration (3 min)

**Stripe :** (Optionnel - pour paiements réels)
1. Créez un compte sur https://stripe.com
2. Récupérez vos clés test dans Dashboard → API Keys
3. Éditez `view/assets/js/paiement.js` ligne 138 :
   ```javascript
   stripe = Stripe('pk_test_VOTRE_CLE_PUBLIQUE');
   ```
4. Éditez `config/config_paiement.php` :
   ```php
   'stripe' => [
       'publishable_key' => 'pk_test_XXX',
       'secret_key' => 'sk_test_XXX',
   ]
   ```

**PayPal :** (Optionnel - pour paiements réels)
1. Créez un compte sur https://developer.paypal.com
2. Créez une application sandbox
3. Éditez `view/front/paiement.html` ligne 18 :
   ```html
   <script src="https://www.paypal.com/sdk/js?client-id=VOTRE_CLIENT_ID&currency=EUR"></script>
   ```

### 3️⃣ Test (1 min)

Ouvrez dans votre navigateur :
```
http://localhost/PeaceConnect/tests/test_paiement_complet.html
```

✅ Si tous les tests passent → **C'est prêt !**

---

## 🎯 Test rapide

### Carte bancaire (sans configuration)

1. Ajoutez des produits au panier
2. Allez à la page paiement
3. Sélectionnez "Carte Bancaire"
4. Entrez :
   - Numéro : `4242 4242 4242 4242`
   - Expiration : `12/25`
   - CVV : `123`
   - Nom : `TEST`
5. Cliquez "Payer"

→ **Redirection vers confirmation !** ✅

### Virement bancaire (sans configuration)

1. Sélectionnez "Virement Bancaire"
2. Notez les coordonnées IBAN
3. Cliquez "Confirmer"

→ **Commande créée en attente !** ✅

---

## 📁 Fichiers modifiés/ajoutés

### ✨ Nouveaux fichiers
```
config/config_paiement.php
config/config_paiement.php.example
sql/migration_paiement_v2.sql
tests/test_paiement_complet.html
GUIDE_INSTALLATION_PAIEMENT.md
PAIEMENT_README.md
PAIEMENT_QUICK_START.md (ce fichier)
```

### 🔄 Fichiers mis à jour
```
database.sql
controller/PaiementController.php
view/front/paiement.html
view/front/confirmation.html
view/assets/js/paiement.js
```

---

## 🎨 Fonctionnalités

✅ 4 méthodes de paiement (Carte, Stripe, PayPal, Virement)
✅ Interface utilisateur moderne et responsive
✅ Validation formulaires côté client
✅ Sécurité côté serveur
✅ Page de confirmation détaillée
✅ Suivi des transactions
✅ Support multi-devises (EUR par défaut)
✅ Gestion d'erreurs complète
✅ Tests automatisés

---

## 🔗 Liens utiles

| Documentation | Lien |
|---------------|------|
| Guide complet | [GUIDE_INSTALLATION_PAIEMENT.md](GUIDE_INSTALLATION_PAIEMENT.md) |
| README | [PAIEMENT_README.md](PAIEMENT_README.md) |
| Tests | [tests/test_paiement_complet.html](tests/test_paiement_complet.html) |
| Stripe Docs | https://stripe.com/docs |
| PayPal Docs | https://developer.paypal.com/docs |

---

## 🐛 Problème ?

**Tests échouent ?**
→ Consultez [GUIDE_INSTALLATION_PAIEMENT.md](GUIDE_INSTALLATION_PAIEMENT.md) section "Dépannage"

**Erreur Stripe ?**
→ Vérifiez que la clé publique est bien configurée dans `paiement.js`

**Erreur PayPal ?**
→ Vérifiez que le Client ID est bien configuré dans `paiement.html`

**Erreur base de données ?**
→ Exécutez `sql/migration_paiement_v2.sql`

---

## 📞 Besoin d'aide ?

1. ✅ Lancez les tests automatisés
2. 📖 Consultez le guide d'installation
3. 🔍 Vérifiez la console navigateur (F12)
4. 📝 Regardez les logs PHP

---

**Temps d'installation total : ~6 minutes** ⏱️

**Prêt à accepter des paiements !** 🚀💰
