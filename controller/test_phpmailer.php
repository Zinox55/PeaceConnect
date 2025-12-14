<?php
/**
 * Test de l'installation PHPMailer
 * Ce fichier vérifie que PHPMailer est correctement installé et configuré
 */

// Désactiver l'affichage des erreurs pour éviter de casser le JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Capturer toutes les sorties
ob_start();

header('Content-Type: application/json; charset=utf-8');

// Charger l'autoloader
$autoloadPath = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoloadPath)) {
    echo json_encode([
        'success' => false,
        'message' => '❌ Autoloader non trouvé',
        'path' => $autoloadPath
    ], JSON_PRETTY_PRINT);
    exit;
}

require_once $autoloadPath;

// Vérifier que les classes PHPMailer sont disponibles
try {
    // Test 1: Vérifier que les classes existent
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        throw new Exception('Classe PHPMailer non trouvée');
    }
    
    if (!class_exists('PHPMailer\PHPMailer\Exception')) {
        throw new Exception('Classe Exception non trouvée');
    }
    
    if (!class_exists('PHPMailer\PHPMailer\SMTP')) {
        throw new Exception('Classe SMTP non trouvée');
    }
    
    // Test 2: Créer une instance de PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    // Test 3: Vérifier la configuration
    $configFile = __DIR__ . '/config_mail.php';
    $configExists = file_exists($configFile);
    
    // Test 4: Vérifier le modèle Mailer
    $mailerFile = __DIR__ . '/../model/Mailer.php';
    $mailerExists = file_exists($mailerFile);
    
    // Résultat des tests
    $result = [
        'success' => true,
        'message' => '✅ PHPMailer est correctement installé!',
        'tests' => [
            'autoloader' => [
                'status' => '✅ OK',
                'path' => $autoloadPath
            ],
            'classes' => [
                'PHPMailer' => '✅ Disponible',
                'Exception' => '✅ Disponible',
                'SMTP' => '✅ Disponible'
            ],
            'instance' => [
                'status' => '✅ Instance créée avec succès',
                'version' => $mail::VERSION
            ],
            'configuration' => [
                'config_mail.php' => $configExists ? '✅ Trouvé' : '⚠️ Non trouvé',
                'path' => $configFile
            ],
            'model' => [
                'Mailer.php' => $mailerExists ? '✅ Trouvé' : '⚠️ Non trouvé',
                'path' => $mailerFile
            ]
        ],
        'next_steps' => [
            '1. Configurer config_mail.php avec vos identifiants SMTP',
            '2. Tester l\'envoi d\'email via EmailController.php',
            '3. Utiliser la classe Mailer pour envoyer des emails'
        ]
    ];
    
    // Nettoyer le buffer de sortie
    $output = ob_get_clean();
    
    // Si du contenu a été capturé, l'ajouter au résultat
    if (!empty($output)) {
        $result['debug_output'] = $output;
    }
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Nettoyer le buffer
    ob_end_clean();
    
    echo json_encode([
        'success' => false,
        'message' => '❌ Erreur lors du test',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Error $e) {
    // Capturer aussi les erreurs PHP 7+
    ob_end_clean();
    
    echo json_encode([
        'success' => false,
        'message' => '❌ Erreur PHP',
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
