<?php
header('Content-Type: text/plain; charset=utf-8');

echo "=== TEST ULTRA SIMPLE ===\n\n";

echo "1. Version PHP: " . phpversion() . "\n\n";

echo "2. Dossier actuel: " . __DIR__ . "\n\n";

$autoload = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
echo "3. Chemin autoload: $autoload\n";
echo "   Existe? " . (file_exists($autoload) ? 'OUI' : 'NON') . "\n\n";

$phpmailer = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
echo "4. Chemin PHPMailer.php: $phpmailer\n";
echo "   Existe? " . (file_exists($phpmailer) ? 'OUI' : 'NON') . "\n";
echo "   Taille: " . (file_exists($phpmailer) ? filesize($phpmailer) . ' octets' : 'N/A') . "\n\n";

if (file_exists($autoload)) {
    echo "5. Chargement de l'autoloader...\n";
    require_once $autoload;
    echo "   Chargé!\n\n";
    
    echo "6. Test des classes:\n";
    echo "   PHPMailer: " . (class_exists('PHPMailer\\PHPMailer\\PHPMailer') ? 'DISPONIBLE' : 'MANQUANTE') . "\n";
    echo "   Exception: " . (class_exists('PHPMailer\\PHPMailer\\Exception') ? 'DISPONIBLE' : 'MANQUANTE') . "\n";
    echo "   SMTP: " . (class_exists('PHPMailer\\PHPMailer\\SMTP') ? 'DISPONIBLE' : 'MANQUANTE') . "\n\n";
    
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        echo "7. Création d'une instance...\n";
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            echo "   Instance créée!\n";
            echo "   Version: " . $mail::VERSION . "\n\n";
            echo "=== SUCCÈS TOTAL ===\n";
        } catch (Exception $e) {
            echo "   ERREUR: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "5. ERREUR: Autoloader non trouvé!\n";
}
?>
