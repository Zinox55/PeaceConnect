<?php
// Exporter un article en PDF - Version HTML to PDF simple
ob_start(); // Commencer la capture de sortie

include_once __DIR__ . '/../../config.php';
include_once __DIR__ . '/../../Controller/ArticleController.php';

if (!isset($_GET['id'])) {
    die('Article ID is required');
}

$articleController = new ArticleController();
$article = $articleController->edit($_GET['id']);

if (!$article || !$article->id) {
    die('Article not found');
}

// Créer un nom de fichier propre
$filename = preg_replace('/[^a-z0-9]+/i', '-', $article->titre);
$filename = trim($filename, '-');
$filename = substr($filename, 0, 50);
$filename = $filename . '-' . date('Ymd') . '.pdf';

// Nettoyer tout buffer de sortie
ob_end_clean();

// Générer le HTML pour le PDF
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($article->titre); ?></title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 32pt;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 14pt;
        }
        .article-title {
            font-size: 24pt;
            font-weight: bold;
            color: #333;
            margin: 20px 0;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .article-meta {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 11pt;
            color: #666;
        }
        .article-meta strong {
            color: #333;
        }
        .article-image {
            width: 100%;
            max-width: 600px;
            height: auto;
            display: block;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .article-content {
            font-size: 12pt;
            line-height: 1.8;
            text-align: justify;
            margin: 30px 0;
        }
        .article-content p {
            margin: 15px 0;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            font-size: 10pt;
            color: #999;
        }
        @media print {
            .header {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🌟 PeaceConnect</h1>
        <p>Articles et Actualités</p>
    </div>
    
    <div class="article-title">
        <?php echo htmlspecialchars($article->titre); ?>
    </div>
    
    <div class="article-meta">
        <strong>Auteur:</strong> <?php echo htmlspecialchars($article->auteur); ?><br>
        <strong>Publié le:</strong> <?php echo date('d/m/Y à H:i', strtotime($article->date_creation)); ?><br>
        <strong>Statut:</strong> <?php echo ucfirst(htmlspecialchars($article->statut)); ?>
    </div>
    
    <?php if($article->image): ?>
        <img src="<?php echo __DIR__ . '/../../model/uploads/' . $article->image; ?>" alt="<?php echo htmlspecialchars($article->titre); ?>" class="article-image">
    <?php endif; ?>
    
    <div class="article-content">
        <?php 
        $content = nl2br(htmlspecialchars($article->contenu));
        echo $content;
        ?>
    </div>
    
    <div class="footer">
        <p>Document généré par PeaceConnect le <?php echo date('d/m/Y à H:i'); ?></p>
        <p>© <?php echo date('Y'); ?> PeaceConnect - Tous droits réservés</p>
    </div>
    
    <script>
        // Auto-print et fermeture après impression
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html><?php
// Note: Ce fichier génère un HTML qui peut être imprimé en PDF via le navigateur
// L'utilisateur peut utiliser Ctrl+P ou Commande+P pour sauvegarder en PDF
?>
