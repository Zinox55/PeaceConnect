<?php
/**
 * Test simple pour diagnostiquer les problèmes
 */

// Désactiver l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

$result = [
    'success' => true,
    'message' => 'Test simple réussi',
    'php_version' => phpversion(),
    'tests' => []
];

// Test 1: Vérifier le chemin du vendor
$vendorPath = __DIR__ . DIRECTORY_SEPARATOR . 'vendor';
$result['tests']['vendor_path'] = [
    'path' => $vendorPath,
    'exists' => file_exists($vendorPath),
    'is_dir' => is_dir($vendorPath)
];

// Test 2: Vérifier l'autoloader
$autoloadPath = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$result['tests']['autoload'] = [
    'path' => $autoloadPath,
    'exists' => file_exists($autoloadPath),
    'is_readable' => is_readable($autoloadPath)
];

// Test 3: Vérifier les fichiers PHPMailer
$phpmailerFiles = [
    'PHPMailer.php' => __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php',
    'Exception.php' => __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Exception.php',
    'SMTP.php' => __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php'
];

$result['tests']['phpmailer_files'] = [];
foreach ($phpmailerFiles as $name => $path) {
    $result['tests']['phpmailer_files'][$name] = [
        'path' => $path,
        'exists' => file_exists($path),
        'size' => file_exists($path) ? filesize($path) : 0
    ];
}

// Test 4: Essayer de charger l'autoloader
try {
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
        $result['tests']['autoload_loaded'] = true;
        
        // Test 5: Vérifier si les classes sont disponibles
        $result['tests']['classes'] = [
            'PHPMailer' => class_exists('PHPMailer\\PHPMailer\\PHPMailer'),
            'Exception' => class_exists('PHPMailer\\PHPMailer\\Exception'),
            'SMTP' => class_exists('PHPMailer\\PHPMailer\\SMTP')
        ];
        
        // Test 6: Essayer de créer une instance
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            try {
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $result['tests']['instance'] = [
                    'created' => true,
                    'version' => $mail::VERSION
                ];
            } catch (Exception $e) {
                $result['tests']['instance'] = [
                    'created' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
    } else {
        $result['tests']['autoload_loaded'] = false;
        $result['tests']['error'] = 'Autoloader not found';
    }
} catch (Exception $e) {
    $result['success'] = false;
    $result['error'] = $e->getMessage();
    $result['file'] = $e->getFile();
    $result['line'] = $e->getLine();
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
