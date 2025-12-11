# 📊 Export Excel Amélioré - PeaceConnect

## ✨ Nouvelles fonctionnalités

### 🎯 Structure du fichier Excel

Le nouveau fichier Excel est maintenant organisé en **4 sections** claires :

#### 1. **En-tête du rapport** 
```
PEACECONNECT - RAPPORT DES COMMANDES
Date d'export: 06/12/2025 à 15:30:00
Période: Toutes les commandes
```

#### 2. **Statistiques globales**
- Total des commandes
- Revenu total en euros
- Revenu moyen par commande
- Répartition par statut :
  - Commandes en attente
  - Commandes confirmées
  - Commandes livrées
  - Commandes annulées

#### 3. **Tableau détaillé des commandes**
Colonnes disponibles (14 colonnes) :
1. **ID** - Identifiant unique
2. **N° Commande** - Numéro de commande (ex: CMD-20251206-ABC123)
3. **Nom Client** - Nom complet du client
4. **Email Client** - Adresse email
5. **Téléphone** - Numéro de téléphone
6. **Adresse** - Adresse de livraison complète
7. **Total (€)** - Montant total (format français avec virgule)
8. **Statut** - En Attente / Confirmée / Livrée / Annulée
9. **Méthode Paiement** - Carte Bancaire / PayPal / Stripe / Virement Bancaire
10. **Statut Paiement** - Payé / En attente / Échoué / Remboursé
11. **Date Commande** - Date et heure de création (JJ/MM/AAAA HH:MM)
12. **Date Livraison** - Date prévue ou effective de livraison
13. **Nb Produits** - Nombre de produits différents
14. **Quantité Totale** - Quantité totale d'articles

#### 4. **Pied de page**
```
Rapport généré par PeaceConnect
© 2025 PeaceConnect - Tous droits réservés
Contact: info@peaceconnect.org
```

---

## 🚀 Comment utiliser

### Méthode 1 : Via l'interface graphique (Recommandée)
1. Ouvrez : `http://localhost/PeaceConnect/view/back/export_commandes.html`
2. Consultez les statistiques en temps réel
3. Cliquez sur "Télécharger le rapport Excel"

### Méthode 2 : Lien direct
- URL : `http://localhost/PeaceConnect/controller/CommandeController.php?action=export`

### Méthode 3 : Depuis le backoffice
- Allez dans la gestion des commandes
- Cliquez sur le bouton "Exporter"

---

## 📁 Nom du fichier généré

Format : `PeaceConnect_Commandes_JJ-MM-AAAA_HHMMSS.csv`

Exemple : `PeaceConnect_Commandes_06-12-2025_153045.csv`

---

## 💡 Améliorations techniques

### ✅ Format Excel optimisé
- **Encodage UTF-8** avec BOM pour Excel
- **Séparateur point-virgule** (;) pour Excel français
- **Format des nombres** : virgule comme séparateur décimal (49,98 €)
- **Format des dates** : JJ/MM/AAAA HH:MM

### ✅ Données enrichies
- Traduction automatique des statuts en français
- Traduction des méthodes de paiement
- Traduction des statuts de paiement
- Suppression des sauts de ligne dans les adresses

### ✅ Statistiques automatiques
- Calcul automatique du revenu total
- Calcul du revenu moyen
- Comptage par statut de commande
- Résumé en haut du fichier

### ✅ Présentation professionnelle
- Sections clairement séparées
- En-tête avec logo textuel
- Pied de page avec informations de contact
- Séparateurs visuels

---

## 📊 Exemple de structure

```
┌─────────────────────────────────────────┐
│   PEACECONNECT - RAPPORT DES COMMANDES  │
│   Date: 06/12/2025                      │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│   RÉSUMÉ STATISTIQUES                    │
│   Total commandes: 16                    │
│   Revenu total: 767,64 €                │
│   Revenu moyen: 47,98 €                 │
│   En attente: 10                        │
│   Confirmées: 4                         │
│   Livrées: 1                            │
│   Annulées: 1                           │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│   DÉTAIL DES COMMANDES                   │
├────┬──────────┬─────────┬──────────┬────┤
│ ID │ N° Cmd   │ Client  │ Email    │... │
├────┼──────────┼─────────┼──────────┼────┤
│ 21 │ CMD-2025 │ Dhia    │ dhia@... │... │
│ 20 │ CMD-2025 │ Dhia    │ dhia@... │... │
└────┴──────────┴─────────┴──────────┴────┘

┌─────────────────────────────────────────┐
│   Rapport généré par PeaceConnect       │
│   © 2025 PeaceConnect                   │
└─────────────────────────────────────────┘
```

---

## 🎨 Interface d'export

L'interface `export_commandes.html` offre :

### 📈 Statistiques en temps réel
- Total des commandes
- Revenu total
- Revenu moyen
- Nombre de commandes livrées

### 🎯 Informations claires
- Liste complète du contenu du rapport
- Fonctionnalités mises en avant
- Design moderne et attractif

### ⚡ Expérience utilisateur
- Chargement des stats avant export
- Indicateur de génération
- Design responsive
- Animation de téléchargement

---

## 🔧 Personnalisation

### Modifier l'en-tête
Fichier : `controller/CommandeController.php`
Ligne : ~220
```php
fputcsv($out, ['PEACECONNECT - RAPPORT DES COMMANDES'], ';');
```

### Ajouter des colonnes
Fichier : `controller/CommandeController.php`
Ligne : ~258
```php
$headers = [
    'ID',
    'N° Commande',
    // ... vos colonnes
];
```

### Changer le format des dates
Ligne : ~313
```php
$dateCommande = date('d/m/Y H:i', strtotime($r['date_commande']));
```

---

## 📱 Compatibilité

### ✅ Testé avec :
- Microsoft Excel 2016+
- LibreOffice Calc
- Google Sheets (importation)
- Apple Numbers

### ✅ Navigateurs supportés :
- Chrome/Edge (Chromium)
- Firefox
- Safari

---

## 🐛 Résolution de problèmes

### Problème : Caractères mal affichés dans Excel
**Solution :** Le fichier utilise UTF-8 avec BOM, ouvrez avec Excel 2016+

### Problème : Séparateurs incorrects
**Solution :** Le point-virgule (;) est utilisé pour Excel français

### Problème : Dates au format américain
**Solution :** Format JJ/MM/AAAA est appliqué automatiquement

### Problème : Statistiques incorrectes
**Solution :** Rafraîchissez la page `export_commandes.html` avant export

---

## 📞 Support

Pour toute question ou amélioration :
- Email : info@peaceconnect.org
- Documentation : `/docs/`

---

**Dernière mise à jour :** 6 décembre 2025  
**Version :** 2.0  
**Auteur :** PeaceConnect Dev Team
