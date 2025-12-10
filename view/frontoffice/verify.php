<?php
require_once '../../model/InscriptionModel.php';
require_once '../../config.php';

$inscriptionModel = new InscriptionModel();
$message = '';
$messageType = ''; // success ou error

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
    
    $inscription = $inscriptionModel->getInscriptionByToken($token);
    
    if (!$inscription) {
        $messageType = 'error';
        $message = 'Token invalide ou expiré. Veuillez vous réinscrire.';
    } else {
        // Vérifier l'inscription
        if ($inscriptionModel->verifyInscription($token)) {
            $messageType = 'success';
            $message = 'Bravo ! Votre inscription a été confirmée avec succès. Vous êtes maintenant inscrit à l\'événement.';
        } else {
            $messageType = 'error';
            $message = 'Une erreur est survenue lors de la confirmation. Veuillez réessayer.';
        }
    }
} else {
    $messageType = 'error';
    $message = 'Accès non autorisé.';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification d'inscription - PeaceConnect</title>
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Work Sans', sans-serif;
        }
        .verify-container {
            background: white;
            border-radius: 10px;
            padding: 50px 30px;
            text-align: center;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        .verify-container.success {
            border-left: 5px solid #28a745;
        }
        .verify-container.error {
            border-left: 5px solid #dc3545;
        }
        .icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .icon.success {
            color: #28a745;
        }
        .icon.error {
            color: #dc3545;
        }
        h1 {
            font-size: 32px;
            margin-bottom: 15px;
            color: #333;
        }
        p {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            padding: 12px 40px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn.primary {
            background-color: #667eea;
            color: white;
        }
        .btn.primary:hover {
            background-color: #764ba2;
            text-decoration: none;
        }
        .btn.secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn.secondary:hover {
            background-color: #5a6268;
            text-decoration: none;
        }
        .details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
        .details p {
            margin: 10px 0;
            font-size: 14px;
        }
        .nav {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .nav a {
            color: #667eea;
            text-decoration: none;
            margin: 0 15px;
            font-size: 14px;
        }
        .nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="verify-container <?= $messageType ?>">
        
        <?php if ($messageType === 'success'): ?>
            <div class="icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Inscription confirmée !</h1>
            <p><?= htmlspecialchars($message) ?></p>
            
            <div class="details">
                <p><strong><i class="fas fa-user"></i> Nom :</strong> <?= htmlspecialchars($inscription['nom']) ?></p>
                <p><strong><i class="fas fa-envelope"></i> Email :</strong> <?= htmlspecialchars($inscription['email']) ?></p>
                <p><strong><i class="fas fa-calendar-alt"></i> Événement :</strong> <?= htmlspecialchars($inscription['evenement']) ?></p>
            </div>
            
            <p style="color: #28a745; margin-top: 20px;">
                <i class="fas fa-info-circle"></i> Un email de confirmation a été envoyé à votre adresse.
            </p>
            
            <a href="events.php" class="btn primary">
                <i class="fas fa-home me-2"></i>Voir les événements
            </a>
            
        <?php else: ?>
            <div class="icon error">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h1>Erreur de vérification</h1>
            <p><?= htmlspecialchars($message) ?></p>
            
            <p style="color: #666; font-size: 14px; margin: 20px 0;">
                <i class="fas fa-lightbulb"></i> Assurez-vous que :
            </p>
            
            <div class="details">
                <p><i class="fas fa-check text-success"></i> Le lien n'a pas expiré (valide 24h)</p>
                <p><i class="fas fa-check text-success"></i> Vous utilisez le lien correct</p>
                <p><i class="fas fa-check text-success"></i> Vous cliquez qu'une seule fois</p>
            </div>
            
            <a href="events.php" class="btn primary">
                <i class="fas fa-list me-2"></i>Voir les événements
            </a>
            <a href="inscription.php" class="btn secondary" style="margin-left: 10px;">
                <i class="fas fa-redo me-2"></i>Nouvelle inscription
            </a>
        <?php endif; ?>
        
        <div class="nav">
            <a href="events.php"><i class="fas fa-calendar"></i> Événements</a>
            <a href="inscription.php"><i class="fas fa-user-plus"></i> S'inscrire</a>
        </div>
        
    </div>

</body>
</html>
