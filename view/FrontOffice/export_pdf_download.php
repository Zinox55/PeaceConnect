<?php
// View: Export PDF
// Génère un document PDF à partir d'un article

// Empêcher toute sortie avant l'export
ob_start();
ob_clean();

include_once __DIR__ . '/../../config.php';
include_once __DIR__ . '/../../controller/ArticleController.php';

if (!isset($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    die('Article ID is required');
}

$articleController = new ArticleController();
$article = $articleController->edit($_GET['id']);

if (!$article || !$article->id) {
    header('HTTP/1.1 404 Not Found');
    die('Article not found');
}

// Créer le nom de fichier
$filename = preg_replace('/[^a-z0-9]+/i', '-', $article->titre);
$filename = trim($filename, '-');
$filename = substr($filename, 0, 50);
$filename = $filename . '-' . date('Ymd') . '.html';

// Chemin de l'image en base64 pour l'inclure dans le PDF
$imageData = '';
if ($article->image) {
    $imagePath = __DIR__ . '/../../model/uploads/' . $article->image;
    if (file_exists($imagePath)) {
        $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
        $imageContent = file_get_contents($imagePath);
        $imageData = 'data:image/' . $imageType . ';base64,' . base64_encode($imageContent);
    }
}

// Nettoyer le buffer
ob_end_clean();

// Headers pour téléchargement HTML (convertible en PDF par le navigateur)
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article->titre); ?></title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 2cm;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.8;
            color: #2c3e50;
            background: white;
            max-width: 21cm;
            margin: 0 auto;
            padding: 2cm;
        }
        
        .pdf-header {
            background: linear-gradient(135deg, #59886b 0%, #476d56 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            margin: -2cm -2cm 2cm -2cm;
            border-radius: 0;
        }
        
        .pdf-header h1 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .pdf-header p {
            font-size: 16px;
            opacity: 0.95;
        }
        
        .article-title {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin: 30px 0 20px 0;
            padding-bottom: 15px;
            border-bottom: 4px solid #59886b;
            line-height: 1.3;
        }
        
        .article-meta {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 25px 0;
            border-left: 5px solid #59886b;
        }
        
        .article-meta .meta-item {
            display: flex;
            margin: 10px 0;
            font-size: 15px;
        }
        
        .article-meta .meta-label {
            font-weight: 700;
            color: #59886b;
            min-width: 120px;
        }
        
        .article-meta .meta-value {
            color: #555;
        }
        
        .article-image {
            width: 100%;
            max-width: 100%;
            height: auto;
            display: block;
            margin: 30px 0;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        
        .article-content {
            font-size: 15px;
            line-height: 1.9;
            color: #34495e;
            text-align: justify;
            margin: 40px 0;
        }
        
        .article-content p {
            margin: 18px 0;
        }
        
        .pdf-footer {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 3px solid #e0e0e0;
            text-align: center;
        }
        
        .pdf-footer p {
            color: #95a5a6;
            font-size: 13px;
            margin: 8px 0;
        }
        
        .pdf-footer .generation-date {
            font-weight: 600;
            color: #59886b;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #59886b 0%, #476d56 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(89, 136, 107, 0.4);
            z-index: 1000;
            transition: transform 0.2s;
        }
        
        .print-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        
        .instructions {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .instructions h3 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .instructions p {
            color: #856404;
            margin: 5px 0;
        }
        
        .instructions code {
            background: #fff;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Imprimer / Sauvegarder en PDF</button>
    
    <div class="instructions no-print">
        <h3>📄 Comment sauvegarder en PDF ?</h3>
        <p>1. Cliquez sur le bouton "Imprimer" ci-dessus ou appuyez sur <code>Ctrl+P</code> (Windows) / <code>Cmd+P</code> (Mac)</p>
        <p>2. Dans la fenêtre d'impression, sélectionnez <strong>"Enregistrer au format PDF"</strong> ou <strong>"Microsoft Print to PDF"</strong></p>
        <p>3. Cliquez sur "Enregistrer" et choisissez l'emplacement</p>
    </div>
    
    <div class="pdf-header">
        <h1>🌟 PeaceConnect</h1>
        <p>Plateforme d'Articles et d'Actualités</p>
    </div>
    
    <h1 class="article-title"><?php echo htmlspecialchars($article->titre); ?></h1>
    
    <div class="article-meta">
        <div class="meta-item">
            <span class="meta-label">👤 Auteur :</span>
            <span class="meta-value"><?php echo htmlspecialchars($article->auteur); ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">📅 Date de publication :</span>
            <span class="meta-value"><?php echo date('d/m/Y à H:i', strtotime($article->date_creation)); ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">📊 Statut :</span>
            <span class="meta-value"><?php echo ucfirst(htmlspecialchars($article->statut)); ?></span>
        </div>
    </div>
    
    <?php if($imageData): ?>
        <img src="<?php echo $imageData; ?>" alt="<?php echo htmlspecialchars($article->titre); ?>" class="article-image">
    <?php endif; ?>
    
    <div class="article-content">
        <?php echo nl2br(htmlspecialchars($article->contenu)); ?>
    </div>
    
    <div class="pdf-footer">
        <p class="generation-date">📄 Document généré le <?php echo date('d/m/Y à H:i:s'); ?></p>
        <p>PeaceConnect © <?php echo date('Y'); ?> - Tous droits réservés</p>
        <p>www.peaceconnect.com</p>
    </div>
    
    <script>
        // Instructions lors du chargement
        window.addEventListener('load', function() {
            // Message d'aide
            console.log('Pour sauvegarder en PDF: Ctrl+P puis "Enregistrer au format PDF"');
        });
    </script>
</body>
</html>
<?php
exit;
?>