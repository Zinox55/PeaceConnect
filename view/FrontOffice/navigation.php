<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PeaceConnect - Navigation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
        .links { display: grid; gap: 15px; margin-top: 20px; }
        .link-group { background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; }
        .link-group h3 { margin-top: 0; color: #667eea; }
        a { display: inline-block; background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px 10px 5px 0; }
        a:hover { background: #5a6fd8; }
        .status { padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🕊️ PeaceConnect - Navigation & Setup</h1>
        
        <div class="status info">
            <strong>Current Path:</strong> <?php echo $_SERVER['REQUEST_URI']; ?><br>
            <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?><br>
            <strong>Current Directory:</strong> <?php echo __DIR__; ?>
        </div>
        
        <div class="links">
            <div class="link-group">
                <h3>🏠 Main Application</h3>
                <a href="../index_articles.php">Homepage Articles</a>
                <a href="FrontOffice/index.php">Frontend</a>
                <a href="BackOffice/dashboard_ichrak.php">Admin Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>