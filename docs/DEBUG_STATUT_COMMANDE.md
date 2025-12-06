# 🔍 Debug : Changement de Statut des Commandes

## 🎯 Problème

Le statut des commandes ne change pas à "livrée" dans le back office. Seuls "confirmée" et "annulée" fonctionnent.

## 🧪 Test avec logs de debug

J'ai ajouté des logs de debug dans le code. Voici comment tester :

### Étape 1 : Ouvrir la console
1. Allez dans le back office (produits.html ou dashboard.html)
2. Appuyez sur **F12** pour ouvrir la console
3. Allez dans l'onglet **Console**

### Étape 2 : Tester le changement de statut
1. Cliquez sur le bouton "Marquer livrée" (icône camion 🚚)
2. Confirmez l'action
3. Regardez les logs dans la console

### Étape 3 : Analyser les logs

Vous devriez voir :
```
🔄 Changement de statut: {id: 1, nouveauStatut: "livree"}
📤 Envoi: {commande_id: 1, statut: "livree"}
📥 Response status: 200
📥 Response data: {success: true, message: "Statut mis à jour"}
```

## 🔍 Diagnostics possibles

### Cas 1 : Erreur JavaScript
**Symptôme** : Rien ne se passe, pas de logs
**Cause** : Erreur JavaScript avant l'appel
**Solution** : Vérifiez les erreurs dans la console

### Cas 2 : Erreur serveur
**Symptôme** : `success: false` dans la réponse
**Cause** : Erreur PHP côté serveur
**Solution** : Vérifiez le message d'erreur

### Cas 3 : Statut invalide
**Symptôme** : Message "Statut invalide"
**Cause** : Le statut "livree" n'est pas reconnu
**Solution** : Vérifiez la base de données

### Cas 4 : Bouton désactivé
**Symptôme** : Le bouton est grisé
**Cause** : La commande est déjà livrée
**Solution** : Normal, c'est le comportement attendu

## 🔧 Vérifications à faire

### 1. Vérifier la base de données
```sql
-- Voir les statuts possibles
SHOW COLUMNS FROM commandes LIKE 'statut';

-- Devrait afficher :
-- ENUM('en_attente', 'confirmee', 'livree', 'annulee')
```

### 2. Vérifier une commande spécifique
```sql
SELECT id, numero_commande, statut, date_commande, date_livraison 
FROM commandes 
WHERE id = 1;
```

### 3. Tester manuellement le changement
```sql
UPDATE commandes 
SET statut = 'livree', date_livraison = NOW() 
WHERE id = 1;
```

Si cette requête fonctionne, le problème vient du code PHP/JavaScript.

## 🐛 Problèmes connus

### Problème 1 : Colonne date_livraison manquante
**Erreur** : `Unknown column 'date_livraison'`
**Solution** : Exécutez la migration
```sql
ALTER TABLE commandes 
ADD COLUMN date_livraison TIMESTAMP NULL DEFAULT NULL 
AFTER date_commande;
```

### Problème 2 : Cache navigateur
**Symptôme** : Ancien code JavaScript exécuté
**Solution** : Videz le cache (Ctrl+Shift+Delete)

### Problème 3 : Erreur de syntaxe SQL
**Symptôme** : Erreur SQL dans les logs
**Solution** : Vérifiez le modèle Commande.php

## 📝 Code à vérifier

### JavaScript (view/back/produits.html)
```javascript
async function changerStatutCommande(id, nouveauStatut) {
  // Avec logs de debug maintenant
  console.log('🔄 Changement de statut:', { id, nouveauStatut });
  // ...
}
```

### PHP (controller/CommandeController.php)
```php
public function mettreAJourStatut() {
    $data = json_decode(file_get_contents("php://input"), true);
    // ...
    $this->commande->mettreAJourStatut($data['commande_id'], $data['statut']);
}
```

### Modèle (model/Commande.php)
```php
public function mettreAJourStatut($commande_id, $statut) {
    if ($statut === 'livree') {
        $query = "UPDATE commandes SET statut = :statut, date_livraison = NOW() WHERE id = :id";
    } else {
        $query = "UPDATE commandes SET statut = :statut WHERE id = :id";
    }
    // ...
}
```

## ✅ Test complet

1. **Créer une commande de test**
2. **Ouvrir le back office**
3. **Ouvrir la console (F12)**
4. **Cliquer sur "Marquer livrée"**
5. **Vérifier les logs**
6. **Vérifier que le statut change**
7. **Vérifier que la date de livraison est enregistrée**

## 🎯 Résultat attendu

Après avoir cliqué sur "Marquer livrée" :
- ✅ Message de confirmation
- ✅ Statut change à "Livrée"
- ✅ Badge vert "Livrée" affiché
- ✅ Date de livraison enregistrée
- ✅ Bouton "Marquer livrée" désactivé
- ✅ Statistiques mises à jour

## 📞 Si le problème persiste

1. **Copiez les logs de la console**
2. **Vérifiez la structure de la base de données**
3. **Testez la requête SQL manuellement**
4. **Vérifiez les permissions PHP**

Les logs de debug vous montreront exactement où le problème se situe !
