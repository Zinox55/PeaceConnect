<?php
require_once '../../model/EventModel.php';
require_once '../../config.php';

$eventModel = new EventModel();
$categories = $eventModel->getAllCategories();
$categorie_id = $_GET['categorie_id'] ?? null;

if ($categorie_id && is_numeric($categorie_id)) {
    $pdo = getPDO();
    $query = "SELECT e.*, c.nom as nom_categorie 
              FROM events e 
              LEFT JOIN categorie c ON e.categorie = c.idCategorie 
              WHERE e.categorie = :categorie_id
              ORDER BY e.date_event ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':categorie_id' => (int)$categorie_id]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $events = $eventModel->getAllEventsWithCategory();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Événements - PeaceConnect</title>
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/event.css">
</head>
<body>

    <!-- =============== HEADER =============== -->
    <nav class="site-nav">
        <div class="container">
            <div class="menu-bg-wrap">
                <div class="site-navigation">
                    <div class="row g-0 align-items-center">
                        <div class="col-2">
                            <a href="../../index.php" class="logo m-0 float-start text-white">PeaceConnect</a>
                        </div>
                        <div class="col-8 text-center">
                            <ul class="js-clone-nav d-none d-lg-inline-block text-start site-menu mx-auto">
                                <li><a href="../../index.php">Accueil</a></li>
                                <li class="active"><a href="events.php">Événements</a></li>
                                <li><a href="about.php">À propos</a></li>
                                <li><a href="contact.php">Contact</a></li>
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

    <!-- =============== VIDEO SIMPLE =============== -->
    <section class="video-hero">
        <video autoplay muted loop playsinline class="hero-video">
            <source src="../../assets/videos/drone-place.mp4" type="video/mp4">
        </video>
        
        <div class="video-content">
            <p class="video-text">Transformer les espaces, reconstruire les communautés</p>
            <a href="#events" class="video-btn">
                <i class="fas fa-hands-helping me-2"></i>Voir nos missions
            </a>
        </div>
    </section>





    











        <!-- SECTION ÉVÉNEMENTS -->
    <section class="events-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- CORRECTION ICI : action vide pour rester sur la même page -->
                    <form action="" method="GET" class="modern-search-form">
                        <div class="row g-3 align-items-end">
                            <!-- Champ recherche -->
                            <div class="col-md-6">
                                <div class="search-input-group">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" name="q" class="form-control" 
                                           placeholder="Rechercher un événement, une cause..." 
                                           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <!-- Filtre catégorie -->
                            <div class="col-md-3">
                                <select name="categorie_id" class="form-select filter-select">
                                    <option value="">Toutes catégories</option>
                                    <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['idCategorie'] ?>" 
                                            <?= ($_GET['categorie_id'] ?? '') == $cat['idCategorie'] ? 'selected' : '' ?>>
                                        <?php 
                                        // Ajouter les emojis selon la catégorie
                                        $emoji = match(strtolower($cat['nom'])) {
                                            'paix' => '🕊️',
                                            'solidarité' => '🤝',
                                            'éducation' => '📚',
                                            'environnement' => '🌱',
                                            'conférence' => '🎤',
                                            default => '🏷️'
                                        };
                                        echo $emoji . ' ' . htmlspecialchars(ucfirst($cat['nom']));
                                        ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Bouton recherche -->
                            <div class="col-md-3">
                                <button type="submit" class="btn search-btn w-100">
                                    <i class="fas fa-search me-2"></i>Rechercher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="container">
            <!-- En-tête de section -->
            <div class="section-header">
                <h2 class="section-title">
                    <?php if ($categorie_id && $selected_cat = array_filter($categories, fn($c) => $c['idCategorie'] == $categorie_id)): ?>
                        <?php $cat = reset($selected_cat); ?>
                        Événements : <?= htmlspecialchars(ucfirst($cat['nom'])) ?>
                    <?php else: ?>
                        Événements à Venir
                    <?php endif; ?>
                </h2>
                <p class="section-subtitle">
                    <?php if ($categorie_id): ?>
                        Découvrez nos initiatives dans cette catégorie
                    <?php else: ?>
                        Découvrez nos prochaines initiatives caritatives et rejoignez une communauté engagée
                    <?php endif; ?>
                </p>
            </div>

            <!-- Grille d'événements -->
            <div class="events-grid">
                <?php if (empty($events)): ?>
                    <!-- Carte vide stylée -->
                    <div class="col-12">
                        <div class="modern-event-card" style="max-width: 500px; margin: 0 auto; cursor: default;">
                            <div class="event-content text-center py-5">
                                <i class="fas fa-calendar-plus fa-4x text-primary mb-4" style="opacity: 0.5;"></i>
                                <h3 class="event-title">
                                    <?php if ($categorie_id): ?>
                                        Aucun événement dans cette catégorie
                                    <?php else: ?>
                                        Aucun événement programmé
                                    <?php endif; ?>
                                </h3>
                                <p class="event-description">
                                    <?php if ($categorie_id): ?>
                                        Revenez plus tard pour de nouvelles initiatives dans cette catégorie.
                                    <?php else: ?>
                                        Nous préparons de nouvelles initiatives passionnantes. Revenez bientôt !
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <?php foreach($events as $event): ?>
                    <div class="modern-event-card" onclick="openModernPopup(
                        '<?= addslashes($event['titre']) ?>',
                        '<?= addslashes(nl2br($event['description'])) ?>',
                        '<?= date('d/m/Y', strtotime($event['date_event'])) ?>',
                        '<?= addslashes($event['lieu']) ?>',
                        '<?= addslashes($event['nom_categorie'] ?? 'Général') ?>',
                        '../../assets/images/<?= $event['image'] ?? 'default-event.jpg' ?>',
                        'inscription.php?event=<?= urlencode($event['titre']) ?>&date=<?= urlencode(date('d/m/Y', strtotime($event['date_event']))) ?>&lieu=<?= urlencode($event['lieu']) ?>'
                    )">
                        <!-- Image de l'événement -->
                        <div class="event-image">
                            <img src="../../assets/images/<?= $event['image'] ?? 'default-event.jpg' ?>" 
                                 alt="<?= htmlspecialchars($event['titre']) ?>">
                            <div class="event-badge">
                                <?= htmlspecialchars(ucfirst($event['nom_categorie'] ?? 'Général')) ?>
                            </div>
                        </div>
                        
                        <!-- Contenu de la carte -->
                        <div class="event-content">
                            <div class="event-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?= date('d M Y', strtotime($event['date_event'])) ?>
                            </div>
                            
                            <h3 class="event-title"><?= htmlspecialchars($event['titre']) ?></h3>
                            <p class="event-description">
                                <?= strlen($event['description']) > 120 ? substr($event['description'], 0, 120) . '...' : $event['description'] ?>
                            </p>
                            
                            <div class="event-meta">
                                <div class="event-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($event['lieu']) ?>
                                </div>
                                <a href="inscription.php?event=<?= urlencode($event['titre']) ?>&date=<?= urlencode(date('d/m/Y', strtotime($event['date_event']))) ?>&lieu=<?= urlencode($event['lieu']) ?>" 
                                   class="event-cta" onclick="event.stopPropagation()">
                                    <i class="fas fa-user-plus me-1"></i>S'inscrire
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Bouton Actualiser -->
            <div class="load-more-container">
                <?php if ($categorie_id): ?>
                    <a href="events.php" class="load-more-btn">
                        <i class="fas fa-list me-2"></i>Voir tous les événements
                    </a>
                <?php else: ?>
                    <button class="load-more-btn" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-2"></i>Actualiser les événements
                    </button>
                <?php endif; ?>
            </div>

            <!-- Spinner de chargement (caché par défaut) -->
            <div class="loading-spinner" style="display: none;">
                <div class="spinner"></div>
                <p>Chargement des événements...</p>
            </div>
        </div>
    </section>

    <!-- MODAL DÉTAILS -->
    <div id="modernPopup" class="modal-overlay" onclick="closeModernPopup()">
        <div class="modal-box" id="modernPopupContent" onclick="event.stopPropagation()">
            <button class="modal-close" onclick="closeModernPopup()">
                <i class="fas fa-times"></i>
            </button>
            
            <!-- Image de l'événement -->
            <div class="modal-image">
                <img id="modern-popup-img" src="" alt="Image événement">
            </div>
            
            <!-- Contenu du modal -->
            <div class="modal-content">
                <h2 id="modern-popup-title" class="modal-title"></h2>
                
                <!-- Détails -->
                <div class="modal-details">
                    <div class="modal-detail">
                        <i class="fas fa-calendar-alt"></i>
                        <span id="modern-popup-date"></span>
                    </div>
                    <div class="modal-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <span id="modern-popup-lieu"></span>
                    </div>
                    <div class="modal-detail">
                        <i class="fas fa-tag"></i>
                        <span id="modern-popup-categorie"></span>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="modal-description">
                    <h5><i class="fas fa-info-circle me-2"></i>Description détaillée</h5>
                    <p id="modern-popup-description"></p>
                </div>
                
                <!-- Boutons d'action -->
                <div class="modal-buttons">
                    <a id="modern-popup-link" class="modal-btn modal-btn-primary">
                        <i class="fas fa-user-plus me-2"></i>S'inscrire à cet événement
                    </a>
                    <button class="modal-btn modal-btn-secondary" onclick="closeModernPopup()">
                        <i class="fas fa-times me-2"></i>Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- =============== FOOTER =============== -->
    <div class="site-footer">
        <div class="footer-waves"></div>
        <div class="container">
            <div class="row">
                <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="widget">
                        <h3>Navigation</h3>
                        <ul class="list-unstyled float-left links">
                            <li><a href="../../index.php">Accueil</a></li>
                            <li><a href="events.php">Événements</a></li>
                            <li><a href="inscription.php">S'inscrire</a></li>
                            <li><a href="contact.php">Contact</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="widget">
                        <h3>Nos Causes</h3>
                        <ul class="list-unstyled float-left links">
                            <?php foreach($categories as $cat): ?>
                            <li>
                                <a href="events.php?categorie_id=<?= $cat['idCategorie'] ?>">
                                    <?= htmlspecialchars(ucfirst($cat['nom'])) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="widget">
                        <h3>Contact</h3>
                        <address>PeaceConnect - 123 Rue de la Paix, 75000 Paris</address>
                        <ul class="list-unstyled links mb-4">
                            <li><a href="tel:+33123456789"><i class="fas fa-phone me-2"></i>+33 1 23 45 67 89</a></li>
                            <li><a href="mailto:contact@peaceconnect.org"><i class="fas fa-envelope me-2"></i>contact@peaceconnect.org</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="widget">
                        <h3>Suivez-nous</h3>
                        <ul class="list-unstyled social">
                            <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                            <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <p class="copyright">
                        <i class="fas fa-heart text-danger me-1"></i>
                        © <?php echo date('Y'); ?> PeaceConnect. Tous droits réservés.
                        <span class="d-block mt-2" style="font-size: 0.9rem; opacity: 0.7;">
                            Fait avec passion pour un monde meilleur
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    
    <!-- Event JS -->
    <script src="../../assets/js/event.js"></script>

</body>
</html>