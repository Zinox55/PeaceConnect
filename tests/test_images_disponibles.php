<?php
/**
 * Script de test pour vérifier les images disponibles
 * Ouvrez ce fichier dans votre navigateur pour voir toutes les images
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test Images Disponibles</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #5F9E7F; }
        .section { margin: 30px 0; padding: 20px; background: #f8f9fa; border-radius: 8px; }
        .images-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
        .image-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); text-align: center; }
        .image-card img { width: 100%; height: 150px; object-fit: cover; border-radius: 5px; margin-bottom: 10px; }
        .image-card .name { font-size: 12px; color: #666; word-break: break-all; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 5px; font-size: 12px; margin-top: 5px; }
        .status.ok { background: #d4edda; color: #155724; }
        .status.error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ Test des Images Disponibles</h1>
        
        <div class="info">
            <strong>📍 Chemins testés :</strong><br>
            - Images fixes : <code>view/assets/img/</code><br>
            - Images uploadées : <code>view/assets/img/produits/</code>
        </div>

        <?php
        // Dossier des images fixes
        $imgDir = 'view/assets/img/';
        $produitsDir = 'view/assets/img/produits/';
        
        // Lister les images fixes
        echo '<div class="section">';
        echo '<h2>📁 Images Fixes (view/assets/img/)</h2>';
        echo '<div class="images-grid">';
        
        if (is_dir($imgDir)) {
            $files = scandir($imgDir);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !is_dir($imgDir . $file)) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $exists = file_exists($imgDir . $file);
                        echo '<div class="image-card">';
                        echo '<img src="' . $imgDir . $file . '" alt="' . htmlspecialchars($file) . '" onerror="this.src=\'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'150\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'150\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' fill=\'%23999\'%3EErreur%3C/text%3E%3C/svg%3E\'">';
                        echo '<div class="name">' . htmlspecialchars($file) . '</div>';
                        echo '<div class="status ' . ($exists ? 'ok' : 'error') . '">' . ($exists ? '✓ Existe' : '✗ Manquant') . '</div>';
                        echo '</div>';
                    }
                }
            }
        } else {
            echo '<p>❌ Dossier introuvable</p>';
        }
        
        echo '</div></div>';
        
        // Lister les images uploadées
        echo '<div class="section">';
        echo '<h2>📁 Images Uploadées (view/assets/img/produits/)</h2>';
        echo '<div class="images-grid">';
        
        if (is_dir($produitsDir)) {
            $files = scandir($produitsDir);
            $count = 0;
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !is_dir($produitsDir . $file)) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $exists = file_exists($produitsDir . $file);
                        echo '<div class="image-card">';
                        echo '<img src="' . $produitsDir . $file . '" alt="' . htmlspecialchars($file) . '" onerror="this.src=\'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'150\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'150\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' fill=\'%23999\'%3EErreur%3C/text%3E%3C/svg%3E\'">';
                        echo '<div class="name">' . htmlspecialchars($file) . '</div>';
                        echo '<div class="status ' . ($exists ? 'ok' : 'error') . '">' . ($exists ? '✓ Existe' : '✗ Manquant') . '</div>';
                        echo '</div>';
                        $count++;
                    }
                }
            }
            echo '</div>';
            echo '<p style="margin-top: 20px; color: #666;"><strong>Total :</strong> ' . $count . ' images uploadées</p>';
        } else {
            echo '<p>❌ Dossier introuvable</p>';
        }
        
        echo '</div>';
        
        // Connexion à la base de données pour voir les produits
        require_once 'config.php';
        try {
            $db = config::getConnexion();
            $query = "SELECT id, nom, image FROM produits ORDER BY id";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<div class="section">';
            echo '<h2>🗄️ Produits dans la Base de Données</h2>';
            echo '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
            echo '<thead><tr style="background: #5F9E7F; color: white;">';
            echo '<th style="padding: 10px; text-align: left;">ID</th>';
            echo '<th style="padding: 10px; text-align: left;">Nom</th>';
            echo '<th style="padding: 10px; text-align: left;">Image (DB)</th>';
            echo '<th style="padding: 10px; text-align: left;">Chemin Calculé</th>';
            echo '<th style="padding: 10px; text-align: center;">Aperçu</th>';
            echo '</tr></thead><tbody>';
            
            foreach ($produits as $p) {
                $rawImage = trim($p['image'] ?? '');
                $imagePath = 'view/assets/img/logo.png';
                
                if ($rawImage) {
                    if (strpos($rawImage, 'produit_') === 0) {
                        $imagePath = 'view/assets/img/produits/' . $rawImage;
                    } else {
                        $imagePath = 'view/assets/img/' . $rawImage;
                    }
                }
                
                $exists = file_exists($imagePath);
                
                echo '<tr style="border-bottom: 1px solid #ddd;">';
                echo '<td style="padding: 10px;">' . $p['id'] . '</td>';
                echo '<td style="padding: 10px;"><strong>' . htmlspecialchars($p['nom']) . '</strong></td>';
                echo '<td style="padding: 10px;"><code>' . htmlspecialchars($rawImage ?: 'NULL') . '</code></td>';
                echo '<td style="padding: 10px;"><code style="font-size: 11px;">' . htmlspecialchars($imagePath) . '</code></td>';
                echo '<td style="padding: 10px; text-align: center;">';
                echo '<img src="' . $imagePath . '" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 2px solid ' . ($exists ? '#28a745' : '#dc3545') . ';" onerror="this.src=\'view/assets/img/logo.png\'">';
                echo '<br><span class="status ' . ($exists ? 'ok' : 'error') . '" style="margin-top: 5px;">' . ($exists ? '✓' : '✗') . '</span>';
                echo '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
            echo '</div>';
            
        } catch (Exception $e) {
            echo '<div class="section">';
            echo '<p style="color: red;">❌ Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
        ?>
        
        <div class="info" style="margin-top: 30px;">
            <strong>💡 Instructions :</strong><br>
            1. Vérifiez que toutes les images s'affichent correctement<br>
            2. Si une image est manquante (bordure rouge), vérifiez le fichier<br>
            3. Exécutez <code>sql/fix_old_images.sql</code> pour corriger les chemins<br>
            4. Ou uploadez de nouvelles images via le back office
        </div>
    </div>
</body>
</html>
