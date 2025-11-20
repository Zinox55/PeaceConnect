<?php
require_once __DIR__ . '/model/Produit.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Images - PeaceConnect</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #5F9E7F; }
        .products-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px; }
        .product-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .product-card img { width: 100%; height: 200px; object-fit: cover; border-radius: 5px; margin-bottom: 10px; }
        .product-card h3 { margin: 10px 0; color: #333; font-size: 1.1rem; }
        .product-info { color: #666; font-size: 0.9rem; margin: 5px 0; }
        .image-path { background: #f0f0f0; padding: 5px; border-radius: 3px; font-family: monospace; font-size: 0.75rem; word-break: break-all; margin-top: 10px; }
        .status { padding: 5px 10px; border-radius: 5px; font-size: 0.85rem; display: inline-block; margin-top: 5px; }
        .status.ok { background: #d4edda; color: #155724; }
        .status.error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ Test Images Produits - PeaceConnect</h1>
        <p>Vérification de l'affichage des images pour tous les produits</p>
        
        <div class="products-grid">
<?php
try {
    $produit = new Produit();
    $produits = $produit->readAll();
    
    foreach ($produits as $p) {
        // Déterminer le chemin de l'image
        $imagePath = '';
        $imageStatus = '';
        $imageFile = '';
        
        if (!empty($p['image'])) {
            if (strpos($p['image'], 'produit_') === 0) {
                // Image uploadée
                $imagePath = "view/assets/img/produits/{$p['image']}";
                $imageFile = __DIR__ . "/view/assets/img/produits/{$p['image']}";
            } else {
                // Image de base
                $imagePath = "view/assets/img/{$p['image']}";
                $imageFile = __DIR__ . "/view/assets/img/{$p['image']}";
            }
            
            // Vérifier si le fichier existe
            if (file_exists($imageFile)) {
                $imageStatus = '<span class="status ok">✓ Fichier existe</span>';
            } else {
                $imageStatus = '<span class="status error">✗ Fichier introuvable</span>';
            }
        } else {
            $imagePath = 'view/assets/img/logo.png';
            $imageStatus = '<span class="status">Pas d\'image</span>';
        }
        
        echo '<div class="product-card">';
        echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($p['nom']) . '" onerror="this.src=\'view/assets/img/logo.png\'">';
        echo '<h3>' . htmlspecialchars($p['nom']) . '</h3>';
        echo '<div class="product-info"><strong>ID:</strong> ' . $p['id'] . '</div>';
        echo '<div class="product-info"><strong>Prix:</strong> ' . number_format($p['prix'], 2) . ' €</div>';
        echo '<div class="product-info"><strong>Stock:</strong> ' . $p['stock'] . ' unités</div>';
        echo '<div class="image-path"><strong>Chemin BDD:</strong> ' . htmlspecialchars($p['image'] ?: 'Aucune') . '</div>';
        echo '<div class="image-path"><strong>Chemin fichier:</strong> ' . htmlspecialchars($imagePath) . '</div>';
        echo $imageStatus;
        echo '</div>';
    }
} catch (Exception $e) {
    echo '<div class="status error">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
        </div>
    </div>
</body>
</html>
