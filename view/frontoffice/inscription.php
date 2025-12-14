<?php
// 1. INCLUSION DES MODÈLES
require_once '../../model/InscriptionModel.php';
require_once '../../model/Mailer_events.php';
require_once '../../config.php';

// 2. RÉCUPÉRER LES PARAMÈTRES
$event_title = $_GET['event'] ?? 'Événement';
$event_date = $_GET['date'] ?? 'Date non spécifiée';
$event_lieu = $_GET['lieu'] ?? 'Lieu non spécifié';

// 3. TRAITEMENT DU FORMULAIRE
$inscription_reussie = false;
$email = ''; // Pour afficher dans le message de succès
$message_erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $evenement = trim($_POST['evenement'] ?? $event_title);
    
    // Validation
    if (empty($nom) || empty($email) || empty($telephone)) {
        $message_erreur = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_erreur = 'Adresse email invalide.';
    } elseif (!preg_match('/^[0-9]{8}$/', $telephone)) {
        $message_erreur = 'Le téléphone doit contenir 8 chiffres.';
    } else {
        try {
            // Utiliser TON modèle InscriptionModel
            $model = new InscriptionModel();
            $token = $model->createInscription($nom, $email, $telephone, $evenement);
            
            if ($token) {
                // Envoyer l'email de vérification
                $mailer = new Mailer();
                if ($mailer->sendVerificationEmail($email, $nom, $token)) {
                    $inscription_reussie = true;
                } else {
                    $message_erreur = 'Inscription créée mais l\'email n\'a pas pu être envoyée. Consultez le journal mail_error.log.';
                }
            } else {
                $message_erreur = 'Erreur lors de l\'enregistrement dans la base de données.';
            }
        } catch (Exception $e) {
            $message_erreur = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - PeaceConnect</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="./assets_events/css/bootstrap.css">
    <link rel="stylesheet" href="./assets_events/css/aos.css">
    <link rel="stylesheet" href="./assets_events/css/flatpickr.min.css">
    <link rel="stylesheet" href="./assets_events/css/style.css">
    <link rel="stylesheet" href="./assets_events/css/inscription.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <!-- =============== NAVBAR =============== -->
    <nav class="site-nav">
        <div class="container">
            <div class="menu-bg-wrap">
                <div class="site-navigation">
                    <div class="row g-0 align-items-center">
                        <div class="col-2">
                            <a href="events.php" class="logo m-0 float-start">PeaceConnect</a>
                        </div>
                        <div class="col-8 text-center">
                            <ul class="js-clone-nav d-none d-lg-inline-block text-start site-menu mx-auto">
                                <li><a href="events.php">Événements</a></li>
                                <li><a href="calendar.php">Calendrier</a></li>
                            </ul>
                        </div>
                        <div class="col-2 text-end">
                            <a href="#" class="burger ms-auto float-end site-menu-toggle js-menu-toggle d-inline-block d-lg-none light">
                                <span></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- =============== HERO SECTION =============== -->
    <div class="inscription-hero">
        <h1><i class="fas fa-user-plus"></i>Rejoignez Notre Communauté</h1>
        <p class="inscription-hero-subtitle">Inscription aux Événements</p>
        <p class="inscription-hero-description">
            Complétez votre inscription pour participer à nos événements de volontariat. 
            Ensemble, créons un impact positif dans notre communauté.
        </p>
    </div>

    <section class="section-inscription">
        <div class="container py-5">
            
            <!-- INFOS ÉVÉNEMENT -->
            <div class="event-info-header" id="eventInfo">
                <div class="row align-items-center">
                    <div class="col-md-9">
                        <h2 id="eventTitle">
                            <i class="fas fa-calendar-check me-2"></i>
                            <?= htmlspecialchars($event_title) ?>
                        </h2>
                        <p class="mb-0">
                            <i class="fas fa-calendar-day me-1"></i> 
                            <strong>Date :</strong> <span id="eventDate"><?= htmlspecialchars($event_date) ?></span>
                            <br class="d-md-none">
                            <span class="d-none d-md-inline"> • </span>
                            <i class="fas fa-map-marker-alt me-1"></i>
                            <strong>📍 Lieu :</strong> <span id="eventLieu"><?= htmlspecialchars($event_lieu) ?></span>
                        </p>
                    </div>
                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                        <span class="badge p-3" style="background: #59886b; color: white; font-size: 1rem;">
                            <i class="fas fa-users me-1"></i> Rejoignez-nous
                        </span>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="inscription-card">
                        <h2 class="text-center mb-4">
                            <i class="fas fa-user-plus me-2"></i>Formulaire d'Inscription
                        </h2>
                        
                        <!-- MESSAGE D'ERREUR -->
                        <?php if (!empty($message_erreur) && !$inscription_reussie): ?>
                        <div class="alert alert-danger text-center mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($message_erreur) ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- MESSAGE DE SUCCÈS -->
                        <?php if ($inscription_reussie): ?>
                        <div id="successMessage" class="alert alert-success text-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-check-circle fa-2x me-3"></i>
                                <div>
                                    <h4 class="alert-heading mb-2">✅ Inscription En Attente de Confirmation</h4>
                                    <p class="mb-0">Presque terminé ! Vérifiez votre email.</p>
                                    <p class="mt-2">Un lien de confirmation a été envoyé à :<br><strong><?= htmlspecialchars($email) ?></strong></p>
                                    <p style="font-size: 13px; opacity: 0.8;">Vérifiez votre dossier spam si vous ne le trouvez pas.</p>
                                    <p style="font-size: 13px; opacity: 0.8;"><i class="fas fa-info-circle"></i> Le lien expire dans 24 heures.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="events.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-calendar-alt me-2"></i>Voir tous les événements
                            </a>
                        </div>
                        
                        <?php else: ?>
                        
                        <!-- FORMULAIRE -->
                        <form id="inscriptionForm" method="POST" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-user me-2"></i>Nom complet *
                                </label>
                                <input type="text" name="nom" class="form-control" 
                                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" 
                                       placeholder="Votre nom et prénom" required>
                                <div class="invalid-feedback">
                                    Veuillez entrer votre nom complet.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Adresse email *
                                </label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                                       placeholder="exemple@email.com" required>
                                <div class="invalid-feedback">
                                    Veuillez entrer une adresse email valide.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-phone me-2"></i>Téléphone *
                                </label>
                                <input type="tel" name="telephone" class="form-control" 
                                       value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" 
                                       placeholder="8 chiffres (ex: 12345678)" 
                                       pattern="[0-9]{8}" required>
                                <div class="invalid-feedback">
                                    Veuillez entrer un numéro de téléphone valide (8 chiffres).
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Format : 8 chiffres sans espaces
                                </small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt me-2"></i>Événement
                                </label>
                                <input type="text" name="evenement" id="evenementField" class="form-control bg-light" 
                                       value="<?= htmlspecialchars($event_title) ?>" readonly>
                                <small class="form-text text-muted">
                                    Vous vous inscrivez à cet événement spécifique.
                                </small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Confirmer mon inscription
                                </button>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="events.php" class="text-decoration-none">
                                    <i class="fas fa-arrow-left me-1"></i> Retour aux événements
                                </a>
                            </div>
                        </form>
                        
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <h5 class="mb-3">PeaceConnect</h5>
                    <p class="mb-0" style="opacity: 0.8;">
                        <i class="fas fa-heart text-danger me-1"></i>
                        Ensemble pour un monde meilleur
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">
                        © <?= date('Y') ?> PeaceConnect. Tous droits réservés.
                        <br>
                        <small style="opacity: 0.6;">Fait avec passion pour un monde meilleur</small>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="./assets_events/js/bootstrap.bundle.min.js"></script>
    <script src="./assets_events/js/aos.js"></script>
    <script>
        // Initialiser AOS
        AOS.init({
            duration: 800,
            once: true
        });
        
        // Validation Bootstrap
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
        
        // Animation de l'info événement
        document.addEventListener('DOMContentLoaded', function() {
            const eventInfo = document.getElementById('eventInfo');
            if (eventInfo) {
                eventInfo.style.opacity = '0';
                eventInfo.style.transform = 'translateY(-20px)';
                
                setTimeout(function() {
                    eventInfo.style.transition = 'all 0.6s ease-out';
                    eventInfo.style.opacity = '1';
                    eventInfo.style.transform = 'translateY(0)';
                }, 300);
            }
        });
    </script>

    <!-- =============== FOOTER =============== -->
    <footer style="background: #2c3e50; color: white; padding: 40px 0; margin-top: 60px;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 style="color: #59886b; font-weight: 700; margin-bottom: 20px;">PeaceConnect</h4>
                    <p style="color: #bdc3c7; line-height: 1.8;">
                        Rejoignez notre communauté de volontaires et participez à des événements qui font la différence.
                    </p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 style="color: #59886b; font-weight: 600; margin-bottom: 20px;">Navigation</h5>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px;"><a href="events.php" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">📅 Événements</a></li>
                        <li style="margin-bottom: 10px;"><a href="calendar.php" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">📆 Calendrier</a></li>
                        <li style="margin-bottom: 10px;"><a href="map.php" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">🗺️ Carte</a></li>
                        <li style="margin-bottom: 10px;"><a href="inscription.php" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">✍️ S'inscrire</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 style="color: #59886b; font-weight: 600; margin-bottom: 20px;">Contact</h5>
                    <p style="color: #bdc3c7; margin-bottom: 10px;">
                        <i class="fas fa-envelope" style="color: #59886b; margin-right: 10px;"></i>
                        contact@peaceconnect.tn
                    </p>
                    <p style="color: #bdc3c7; margin-bottom: 10px;">
                        <i class="fas fa-phone" style="color: #59886b; margin-right: 10px;"></i>
                        +216 XX XXX XXX
                    </p>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 30px 0;">
            <div class="text-center" style="color: #95a5a6;">
                <p style="margin: 0;">&copy; 2025 PeaceConnect. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

</body>
</html>
