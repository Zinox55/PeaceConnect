<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic PHPMailer</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
        }
        .section {
            background: #252526;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #007acc;
        }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        h1 { color: #4ec9b0; }
        h2 { color: #569cd6; }
        pre {
            background: #1e1e1e;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔍 Diagnostic PHPMailer - PeaceConnect</h1>
    
    <div class="section">
        <h2>📊 Informations PHP</h2>
        <pre><?php
echo "Version PHP: " . phpversion() . "\n";
echo "OS: " . PHP_OS . "\n";
echo "SAPI: " . php_sapi_name() . "\n";
echo "Extensions chargées: " . count(get_loaded_extensions()) . "\n";
        ?></pre>
    </div>
    
    <div class="section">
        <h2>📁 Vérification des Chemins</h2>
        <pre><?php
$paths = [
    'Dossier controller' => __DIR__,
    'Dossier vendor' => __DIR__ . '/vendor',
    'Autoloader' => __DIR__ . '/vendor/autoload.php',
    'PHPMailer.php' => __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php',
    'Exception.php' => __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php',
    'SMTP.php' => __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php',
];

foreach ($paths as $name => $path) {
    $exists = file_exists($path);
    $status = $exists ? '✅' : '❌';
    $color = $exists ? 'success' : 'error';
    echo "<span class='$color'>$status $name</span>\n";
    echo "   Chemin: $path\n";
    if ($exists && is_file($path)) {
        echo "   Taille: " . number_format(filesize($path)) . " octets\n";
    }
    echo "\n";
}
        ?></pre>
    </div>
    
    <div class="section">
        <h2>🔧 Test de Chargement</h2>
        <pre><?php
$autoloadPath = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoloadPath)) {
    echo "<span class='error'>❌ Autoloader non trouvé!</span>\n";
    echo "Chemin recherché: $autoloadPath\n";
} else {
    echo "<span class='success'>✅ Autoloader trouvé</span>\n";
    
    try {
        require_once $autoloadPath;
        echo "<span class='success'>✅ Autoloader chargé avec succès</span>\n\n";
        
        // Vérifier les classes
        $classes = [
            'PHPMailer\\PHPMailer\\PHPMailer',
            'PHPMailer\\PHPMailer\\Exception',
            'PHPMailer\\PHPMailer\\SMTP'
        ];
        
        echo "Classes disponibles:\n";
        foreach ($classes as $class) {
            $exists = class_exists($class);
            $status = $exists ? '✅' : '❌';
            $color = $exists ? 'success' : 'error';
            echo "<span class='$color'>$status $class</span>\n";
        }
        
        // Essayer de créer une instance
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            echo "\n<span class='success'>✅ Tentative de création d'instance...</span>\n";
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            echo "<span class='success'>✅ Instance créée avec succès!</span>\n";
            echo "Version PHPMailer: " . $mail::VERSION . "\n";
        }
        
    } catch (Exception $e) {
        echo "<span class='error'>❌ Erreur: " . $e->getMessage() . "</span>\n";
        echo "Fichier: " . $e->getFile() . "\n";
        echo "Ligne: " . $e->getLine() . "\n";
    } catch (Error $e) {
        echo "<span class='error'>❌ Erreur PHP: " . $e->getMessage() . "</span>\n";
        echo "Fichier: " . $e->getFile() . "\n";
        echo "Ligne: " . $e->getLine() . "\n";
    }
}
        ?></pre>
    </div>
    
    <div class="section">
        <h2>📋 Configuration</h2>
        <pre><?php
$configFile = __DIR__ . '/config_mail.php';
if (file_exists($configFile)) {
    echo "<span class='success'>✅ config_mail.php trouvé</span>\n";
    echo "Chemin: $configFile\n";
} else {
    echo "<span class='warning'>⚠️ config_mail.php non trouvé</span>\n";
    echo "Chemin recherché: $configFile\n";
}

$mailerFile = __DIR__ . '/../model/Mailer.php';
if (file_exists($mailerFile)) {
    echo "\n<span class='success'>✅ Mailer.php trouvé</span>\n";
    echo "Chemin: $mailerFile\n";
} else {
    echo "\n<span class='warning'>⚠️ Mailer.php non trouvé</span>\n";
    echo "Chemin recherché: $mailerFile\n";
}
        ?></pre>
    </div>
    
    <div class="section">
        <h2>✅ Conclusion</h2>
        <pre><?php
$allGood = true;
$issues = [];

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    $allGood = false;
    $issues[] = "Autoloader manquant";
}

if (!file_exists(__DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php')) {
    $allGood = false;
    $issues[] = "Fichiers PHPMailer manquants";
}

if ($allGood) {
    echo "<span class='success'>✅ PHPMailer est correctement installé!</span>\n\n";
    echo "Vous pouvez maintenant:\n";
    echo "1. Configurer controller/config_mail.php\n";
    echo "2. Tester l'envoi d'emails\n";
    echo "3. Utiliser la classe Mailer\n";
} else {
    echo "<span class='error'>❌ Problèmes détectés:</span>\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }
    echo "\nVeuillez réinstaller PHPMailer.\n";
}
        ?></pre>
    </div>
    
    <div style="text-align: center; margin-top: 40px;">
        <a href="../test_phpmailer.html" style="color: #4ec9b0; text-decoration: none; padding: 10px 20px; background: #252526; border-radius: 5px;">
            ← Retour au test visuel
        </a>
    </div>
</body>
</html>
