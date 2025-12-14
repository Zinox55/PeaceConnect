<?php
/**
 * Configuration Email pour PeaceConnect
 * 
 * IMPORTANT: Pour utiliser Gmail avec PHPMailer:
 * 1. Activer la vérification en 2 étapes sur votre compte Gmail
 * 2. Générer un "Mot de passe d'application" dans les paramètres de sécurité Google
 * 3. Utiliser ce mot de passe d'application ici (pas votre mot de passe Gmail normal)
 * 
 * Lien: https://myaccount.google.com/apppasswords
 */

return [
    // Configuration SMTP Gmail
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'secure' => 'tls', // ou 'ssl' pour le port 465
        'username' => 'hamdounidhiaeddine@gmail.com',
        'password' => 'hqqv fzkj vjzd rgmd',
    ],
    
    // Expéditeur par défaut
    'from' => [
        'email' => 'hamdounidhiaeddine@gmail.com',
        'name' => 'PeaceConnect'
    ],
    
    // Email de réponse
    'reply_to' => [
        'email' => 'support@peaceconnect.org',
        'name' => 'Support PeaceConnect'
    ],
    
    // Email administrateur (pour les alertes)
    'admin' => [
        'email' => 'hamdounidhiaeddine@gmail.com',
        'name' => 'Admin PeaceConnect'
    ],
    
    // Options d'envoi
    'options' => [
        'charset' => 'UTF-8',
        'encoding' => 'base64',
        'timeout' => 30,
        'debug' => false // Mettre à true pour debug (désactivé en production pour éviter de casser le JSON)
    ],
    
    // Notifications automatiques
    'notifications' => [
        'stock_alert_enabled' => true,
        'stock_alert_threshold' => 10,
        'order_confirmation_enabled' => true,
        'order_status_update_enabled' => true,
    ]
];
?>
