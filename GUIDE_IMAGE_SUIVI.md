# Guide : Affichage des images de produits dans la page de suivi

## 🎯 Objectif
Afficher l'image exacte du produit commandé dans la page de suivi de commande.

---

## 📋 Architecture de la solution

### 1. **Base de données**
La table `produits` stocke le nom du fichier image :
```sql
CREATE TABLE produits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    image VARCHAR(255),  -- Nom du fichier (ex: produit_1763587200_691e3480c2ecd.jpeg)
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2. **Structure des fichiers**
```
PeaceConnect/
├── view/
│   ├── front/
│   │   └── suivi.html          # Page de suivi
│   └── assets/
│       ├── img/
│       │   ├── produits/       # Dossier des images de produits
│       │   │   ├── produit_1763587200_691e3480c2ecd.jpeg
│       │   │   └── ...
│       │   └── logo.png        # Image par défaut
│       └── js/
│           └── suivi.js        # JavaScript pour afficher les données
├── model/
│   └── Commande.php            # Modèle pour récupérer les commandes
└── controller/
    └── CommandeController.php  # Contrôleur API
```

---

## 🔄 Flux de données

### Étape 1 : Récupération des données depuis la base de données

**Fichier : `model/Commande.php`**
```php
public function lireDetails($commande_id) {
    try {
        $query = "SELECT dc.*, pr.nom, pr.image
                  FROM details_commande dc
                  INNER JOIN produits pr ON dc.produit_id = pr.id
                  WHERE dc.commande_id = :commande_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':commande_id', $commande_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Erreur lecture détails: " . $e->getMessage());
    }
}
```

**Ce qui se passe :**
- La requête JOIN récupère les informations du produit depuis la table `produits`
- Le champ `pr.image` contient le nom du fichier image (ex: `produit_1763587200_691e3480c2ecd.jpeg`)
- Ces données sont retournées au contrôleur

---

### Étape 2 : Transmission via API REST

**Fichier : `controller/CommandeController.php`**
```php
public function suivre() {
    try {
        $numero = isset($_GET['numero']) ? trim($_GET['numero']) : '';
        
        if (empty($numero)) {
            echo json_encode(['success' => false, 'message' => 'Numéro de commande requis']);
            return;
        }
        
        $commande = $this->commande->lireParNumero($numero);
        
        if ($commande) {
            $details = $this->commande->lireDetails($commande['id']);
            echo json_encode([
                'success' => true,
                'commande' => $commande,
                'details' => $details  // ← Contient l'image de chaque produit
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Commande non trouvée']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
```

**Exemple de réponse JSON :**
```json
{
  "success": true,
  "commande": {
    "id": 1,
    "numero_commande": "CMD-2025-757195",
    "nom_client": "Hamdouni Dhia Eddin",
    "total": "29.99"
  },
  "details": [
    {
      "id": 1,
      "commande_id": 1,
      "produit_id": 5,
      "quantite": 1,
      "prix_unitaire": "29.99",
      "nom": "Nourriture pour les Affamés",
      "image": "produit_1763587200_691e3480c2ecd.jpeg"  // ← Nom du fichier
    }
  ]
}
```

---

### Étape 3 : Affichage dans le frontend

**Fichier : `view/assets/js/suivi.js`**
```javascript
function afficherCommande(commande, details) {
    let produitsHTML = '';
    
    details.forEach(detail => {
        // Construction du chemin de l'image
        let imagePath = '../assets/img/logo.png'; // Image par défaut
        
        if (detail.image && detail.image.trim() !== '') {
            // Chemin relatif depuis view/front/suivi.html vers view/assets/img/produits/
            imagePath = `../assets/img/produits/${detail.image}`;
        }
        
        produitsHTML += `
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 80px; height: 80px;">
                    <img src="${imagePath}" 
                         alt="${detail.nom}" 
                         style="width: 100%; height: 100%; object-fit: cover;"
                         onerror="this.onerror=null; this.src='../assets/img/logo.png';">
                </div>
                <div style="flex: 1;">
                    <h5>${detail.nom}</h5>
                    <p>Quantité: ${detail.quantite} × ${detail.prix_unitaire} €</p>
                </div>
            </div>
        `;
    });
    
    // Affichage du HTML généré
    document.querySelector('.suivi-result').innerHTML = produitsHTML;
}
```

---

## 🔍 Chemins relatifs expliqués

### Depuis `view/front/suivi.html` :
```
view/front/suivi.html
    └── ../ (remonte à view/)
        └── assets/
            └── img/
                └── produits/
                    └── produit_1763587200_691e3480c2ecd.jpeg
```

**Chemin final :** `../assets/img/produits/produit_1763587200_691e3480c2ecd.jpeg`

---

## ⚠️ Gestion des erreurs

### Si l'image n'existe pas :
```javascript
onerror="this.onerror=null; this.src='../assets/img/logo.png';"
```

**Explication :**
1. `onerror` se déclenche si l'image ne charge pas
2. `this.onerror=null` évite une boucle infinie
3. `this.src='../assets/img/logo.png'` affiche l'image par défaut

---

## ✅ Vérifications importantes

### 1. Vérifier que l'image existe dans la base de données
```sql
SELECT id, nom, image FROM produits WHERE id = 5;
```

### 2. Vérifier que le fichier existe physiquement
```powershell
ls e:\xampp\htdocs\PeaceConnect\view\assets\img\produits\
```

### 3. Vérifier les permissions du dossier
Le serveur web doit avoir accès en lecture au dossier `produits/`

### 4. Tester l'API
```
GET http://localhost/PeaceConnect/controller/CommandeController.php?action=suivre&numero=CMD-2025-757195
```

---

## 🐛 Débogage

### Console JavaScript (F12)
Ajouter temporairement dans `suivi.js` :
```javascript
console.log('Image reçue depuis API:', detail.image);
console.log('Chemin construit:', imagePath);
```

### Vérifier la réponse de l'API
```javascript
fetch('../../controller/CommandeController.php?action=suivre&numero=CMD-2025-757195')
    .then(response => response.json())
    .then(data => {
        console.log('Données reçues:', data);
        console.log('Images des produits:', data.details.map(d => d.image));
    });
```

---

## 📝 Points clés à retenir

1. ✅ L'image est stockée dans la base de données (table `produits`, champ `image`)
2. ✅ Le JOIN dans la requête SQL récupère l'image depuis la table `produits`
3. ✅ L'API REST retourne le nom du fichier dans le JSON
4. ✅ Le JavaScript construit le chemin relatif correct
5. ✅ Une image par défaut s'affiche si le fichier n'existe pas

---

## 🔧 Code complet résumé

### SQL (déjà en place)
```sql
SELECT dc.*, pr.nom, pr.image
FROM details_commande dc
INNER JOIN produits pr ON dc.produit_id = pr.id
WHERE dc.commande_id = ?
```

### PHP (déjà en place)
```php
$details = $this->commande->lireDetails($commande['id']);
echo json_encode(['details' => $details]);
```

### JavaScript (mis à jour)
```javascript
const imagePath = detail.image 
    ? `../assets/img/produits/${detail.image}` 
    : '../assets/img/logo.png';
```

---

## 🎉 Résultat final

La page de suivi affiche maintenant :
- ✅ L'image exacte du produit commandé
- ✅ Le nom du produit
- ✅ La quantité et le prix
- ✅ Une image par défaut si le fichier est manquant

**La même image qui apparaît dans la page produits apparaît maintenant dans la page de suivi !** 🎯
