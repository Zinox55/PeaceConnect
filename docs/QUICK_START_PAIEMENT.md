# 🚀 Démarrage Rapide - Système de Paiement

## ⚡ Installation en 3 minutes

### 1️⃣ Mise à jour de la base de données

**Option automatique (recommandée) :**
```
http://localhost/peaceconnect/update_database.php
```
Cliquez sur le lien et suivez les instructions.

**Option manuelle :**
```bash
mysql -u root -p peaceconnect < sql/add_payment_fields.sql
```

### 2️⃣ Vérification

```
http://localhost/peaceconnect/tests/test_paiement.php
```
Tous les tests doivent être verts ✅

### 3️⃣ Test complet

1. Ajoutez des produits au panier
2. Cliquez sur "Passer commande"
3. Remplissez le formulaire
4. Choisissez "Carte bancaire"
5. Utilisez ces données de test :
   - **Numéro :** 4242 4242 4242 4242
   - **Date :** 12/25
   - **CVV :** 123
   - **Nom :** TEST USER
6. Validez et admirez ! 🎉

## 📁 Fichiers créés

```
✅ controller/PaiementController.php      # API de paiement
✅ view/front/paiement.html               # Page de paiement
✅ view/front/confirmation.html           # Page de confirmation
✅ view/assets/js/paiement.js             # Logique frontend
✅ sql/add_payment_fields.sql             # Migration DB
✅ docs/PAIEMENT_GUIDE.md                 # Documentation complète
✅ tests/test_paiement.php                # Tests automatiques
✅ update_database.php                    # Mise à jour auto
```

## 🎯 Flux utilisateur

```
🛒 Panier
    ↓
📝 Formulaire client (nom, email, adresse)
    ↓
💳 Choix du paiement (carte/PayPal/virement)
    ↓
✅ Confirmation avec numéro de commande
    ↓
📦 Suivi de commande
```

## 💳 Méthodes disponibles

| Méthode | Statut | Délai |
|---------|--------|-------|
| 💳 Carte bancaire | Payé immédiatement | Instantané |
| 💰 PayPal | Payé immédiatement | Instantané |
| 🏦 Virement | En attente | 2-3 jours |

## 🔧 Personnalisation rapide

### Changer les coordonnées bancaires
Éditez `view/front/paiement.html` ligne ~120 :
```html
<p><strong>IBAN :</strong> FR76 XXXX XXXX XXXX</p>
<p><strong>BIC :</strong> XXXXXXXXX</p>
```

### Ajouter une méthode de paiement
1. Modifiez la base de données
2. Ajoutez l'option dans `paiement.html`
3. Ajoutez la logique dans `paiement.js`

## 📊 Voir les paiements

**Back office :**
```
http://localhost/peaceconnect/view/back/commandes.html
```

Les colonnes affichent :
- Méthode de paiement
- Statut du paiement
- Date de paiement
- ID de transaction

## 🐛 Problèmes courants

### ❌ "Colonnes non trouvées"
**Solution :** Exécutez `update_database.php`

### ❌ "Panier vide"
**Solution :** Ajoutez des produits avant de commander

### ❌ "localStorage non défini"
**Solution :** Désactivez la navigation privée

## 📚 Documentation

- 📖 [Guide complet](docs/PAIEMENT_GUIDE.md) - Tout savoir sur le système
- 📖 [Installation détaillée](INSTALLATION_PAIEMENT.md) - Guide pas à pas
- 📖 [README](README.md) - Documentation générale
- 🧪 [Tests](tests/test_paiement.php) - Vérifier l'installation

## ✨ Fonctionnalités

✅ 3 méthodes de paiement  
✅ Validation en temps réel  
✅ Formatage automatique  
✅ Génération d'ID de transaction  
✅ Page de confirmation  
✅ Suivi des paiements  
✅ Export CSV avec paiements  
✅ Sécurisé (PDO, validation)  

## 🎊 C'est tout !

Votre système de paiement est prêt à l'emploi.

**Besoin d'aide ?**
- 📖 Consultez la [documentation complète](docs/PAIEMENT_GUIDE.md)
- 🧪 Lancez les [tests](tests/test_paiement.php)
- 🔄 Utilisez [update_database.php](update_database.php)

---

**Bon développement ! 🚀**
