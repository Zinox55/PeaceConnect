<?php
/**
 * Configuration des services de paiement
 * 
 * IMPORTANT : Ne jamais commit ce fichier avec de vraies clés API
 * Utilisez des variables d'environnement en production
 */

return [
    // Configuration Stripe
    'stripe' => [
        'publishable_key' => 'pk_test_YOUR_PUBLISHABLE_KEY_HERE',
        'secret_key' => 'sk_test_YOUR_SECRET_KEY_HERE',
        'webhook_secret' => 'whsec_YOUR_WEBHOOK_SECRET_HERE',
        'currency' => 'eur',
        'success_url' => 'http://localhost/PeaceConnect/view/FrontOffice/confirmation.html',
        'cancel_url' => 'http://localhost/PeaceConnect/view/FrontOffice/paiement.html',
    ],
    
    // Configuration PayPal
    'paypal' => [
        'mode' => 'sandbox', // 'sandbox' ou 'live'
        'client_id' => 'YOUR_PAYPAL_CLIENT_ID_HERE',
        'client_secret' => 'YOUR_PAYPAL_CLIENT_SECRET_HERE',
        'currency' => 'EUR',
        'return_url' => 'http://localhost/PeaceConnect/view/FrontOffice/confirmation.html',
        'cancel_url' => 'http://localhost/PeaceConnect/view/FrontOffice/paiement.html',
    ],
    
    // Configuration Virement bancaire
    'virement' => [
        'nom_banque' => 'Banque PeaceConnect',
        'titulaire' => 'PeaceConnect Association',
        'iban' => 'FR76 XXXX XXXX XXXX XXXX XXXX XXX',
        'bic' => 'BNPAFRPPXXX',
        'reference_format' => 'CMD-{NUMERO_COMMANDE}',
        'delai_traitement' => '2-3 jours ouvrés',
    ],
    
    // Configuration générale
    'general' => [
        'modes_actifs' => ['card', 'paypal', 'virement', 'stripe'],
        'montant_minimum' => 5.00,
        'montant_maximum' => 10000.00,
        'frais_transaction' => [
            'card' => 0.029, // 2.9%
            'paypal' => 0.034, // 3.4%
            'virement' => 0, // Gratuit
            'stripe' => 0.029, // 2.9%
        ],
        'devise_defaut' => 'EUR',
        'pays_defaut' => 'FR',
    ],
    
    // Paramètres de sécurité
    'securite' => [
        'verification_3ds' => true,
        'sauvegarde_carte' => false,
        'timeout_session' => 1800, // 30 minutes
        'max_tentatives' => 3,
        'ip_whitelist' => [], // Vide = tous autorisés
    ],
    
    // Notifications
    'notifications' => [
        'email_admin' => 'admin@peaceconnect.com',
        'email_support' => 'support@peaceconnect.com',
        'notifier_succes' => true,
        'notifier_echec' => true,
        'notifier_remboursement' => true,
    ],
];
