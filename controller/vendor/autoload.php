<?php
/**
 * Autoloader pour PHPMailer
 * Ce fichier charge automatiquement les classes PHPMailer
 */

spl_autoload_register(function ($class) {
    // Namespace de base pour PHPMailer
    $prefix = 'PHPMailer\\PHPMailer\\';
    
    // Vérifier si la classe utilise le namespace PHPMailer
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Obtenir le nom de la classe relative
    $relative_class = substr($class, $len);
    
    // Remplacer le namespace par le chemin du fichier
    $file = __DIR__ . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';
    
    // Si le fichier existe, le charger
    if (file_exists($file)) {
        require $file;
    }
});
