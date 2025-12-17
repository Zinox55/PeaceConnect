<?php
session_start();
if (!isset($_SESSION['e'])) {
    header('Location: signin.php');
    exit();
}

// Activer les erreurs pour debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once '../../model/EventModel.php';
    require_once '../../config.php';

    $eventModel = new EventModel();
    $categories = $eventModel->getAllCategories();
    $categorie_id = isset($_GET['categorie_id']) ? $_GET['categorie_id'] : null;
    $search_term = trim(isset($_GET['q']) ? $_GET['q'] : '');

    // Use the project's config class to obtain the PDO connection
    $pdo = \config::getConnexion();
    if (!$pdo) {
        throw new Exception('Database connection not available (config::getConnexion returned null).');
    }
    $query = "SELECT e.*, c.nom as nom_categorie 
              FROM events e 
              LEFT JOIN categorie c ON e.categorie = c.idCategorie 
              WHERE 1=1";
    $params = [];

    // Filtre catégorie
    if ($categorie_id && is_numeric($categorie_id)) {
        $query .= " AND e.categorie = :categorie_id";
        $params[':categorie_id'] = (int)$categorie_id;
    }

    // Filtre recherche texte
    if (!empty($search_term)) {
        $query .= " AND (e.titre LIKE :search1 OR e.description LIKE :search2)";
        $params[':search1'] = '%' . $search_term . '%';
        $params[':search2'] = '%' . $search_term . '%';
    }

    $query .= " ORDER BY e.date_event ASC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Events - PeaceConnect</title>
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="./assets_events/css/bootstrap.css">
        <link rel="stylesheet" href="fonts/icomoon/style.css">
        <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="./assets_events/css/style.css">
        <link rel="stylesheet" href="./assets_events/css/event.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="hero-navbar.css">
        <style>
            body { background: #ffffff; }
            /* Keep page background white; nav styling now centralized in hero-navbar.css */
            .site-nav { top: 10px; padding-top: 24px; padding-bottom: 20px; }
        </style>
</head>
<body>

    <!-- =============== HEADER (Store style) =============== -->
    <nav class="site-nav" id="siteNav">
        <div class="container">
            <div class="menu-wrap">
                <a href="index.php" class="logo">PeaceConnect</a>
                <ul class="site-menu" id="mainMenu">
                    <li><a href="index.php">Home</a></li>
                    <li class="active"><a href="events.php">Events</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="userinfo.php">Profile</a></li>
                </ul>
                <a href="tel:+33123456789" class="call-us"><span class="icon-phone"></span>+33 1 23 45 67 89</a>
                <div class="burger" id="burger"><span></span></div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu (Store style) -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="close-btn" id="closeMobile">&times;</div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="events.php" class="active">Events</a></li>
            <li><a href="contact.html">Contact</a></li>
            <li><a href="userinfo.php">Profile</a></li>
        </ul>
        <p style="margin-top:30px; font-size:14px; color:#555;">Appelez-nous : <strong>+33 1 23 45 67 89</strong></p>
    </div>

    <!-- =============== VIDEO SIMPLE =============== -->
    <section class="video-hero">
        <video autoplay muted loop playsinline class="hero-video">
            <source src="./assets_events/videos/drone-place.mp4" type="video/mp4">
        </video>
        
        <div class="video-content">
            <p class="video-text">Transform spaces, rebuild communities</p>
        </div>
    </section>




        <!-- SECTION ÉVÉNEMENTS -->
    <section class="events-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- CORRECTION ICI : action vide pour rester sur la même page -->
                                        <form action="" method="GET" class="modern-search-form">
                                                <style>
                                                    /* Force perfect alignment - override external CSS */
                                                    .modern-search-form .search-input-group { margin-bottom: 0 !important; }
                                                    .modern-search-form .search-input-group .form-control,
                                                    .modern-search-form .filter-select,
                                                    .modern-search-form .search-btn { 
                                                        height: 70px !important;
                                                        margin-bottom: 0 !important;
                                                        vertical-align: middle !important;
                                                    }
                                                    .modern-search-form .row { display: flex; align-items: center; }
                                                    .modern-search-form .col-md-6 { display: flex; align-items: center; flex: 2; }
                                                    .modern-search-form .col-md-3 { display: flex; align-items: center; flex: 1; }
                                                    .modern-search-form .search-input-group { width: 100%; }
                                                    .modern-search-form .filter-select { width: 100%; }
                                                    .modern-search-form .search-btn { width: auto; }
                                                </style>
                                                <div class="row g-3">
                            <!-- Champ recherche -->
                            <div class="col-md-6">
                                <div class="search-input-group">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" name="q" class="form-control" 
                                               placeholder="Search for an event, a cause..." 
                                               value="<?= htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : '') ?>">
                                    </div>
                            </div>
                            
                            <!-- Filtre catégorie -->
                            <div class="col-md-3">
                                <select name="categorie_id" class="form-select filter-select">
                                    <option value="">All categories</option>
                                    <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['idCategorie'] ?>" 
                                            <?= (isset($_GET['categorie_id']) ? $_GET['categorie_id'] : '') == $cat['idCategorie'] ? 'selected' : '' ?>>
                                        <?php 
                                        // Ajouter les emojis selon la catégorie
                                        // Compatibility: not all PHP versions support `match()`
                                        $lowerName = mb_strtolower($cat['nom']);
                                        $emojiMap = [
                                            'paix' => '🕊️',
                                            'solidarité' => '🤝',
                                            'éducation' => '📚',
                                            'environnement' => '🌱',
                                            'conférence' => '🎤',
                                        ];
                                        $emoji = isset($emojiMap[$lowerName]) ? $emojiMap[$lowerName] : '🏷️';
                                        echo $emoji . ' ' . htmlspecialchars(ucfirst($cat['nom']));
                                        ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Bouton recherche -->
                            <div class="col-md-3">
                                <button type="submit" class="btn search-btn w-100">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Boutons Calendrier et Carte -->
                    <div class="text-center mt-4" style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                        <a href="calendar.php" class="modal-btn modal-btn-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Calendar
                        </a>
                        <a href="map.php" class="modal-btn modal-btn-primary">
                            <i class="fas fa-map-marked-alt me-2"></i>Location Map
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <!-- En-tête de section -->
            <div class="section-header">
                <h2 class="section-title">
                    <?php if ($categorie_id && $selected_cat = array_filter($categories, function($c) { return $c['idCategorie'] == $categorie_id; })): ?>
                        <?php $cat = reset($selected_cat); ?>
                        Events: <?= htmlspecialchars(ucfirst($cat['nom'])) ?>
                    <?php else: ?>
                        Upcoming Events
                    <?php endif; ?>
                </h2>
                <p class="section-subtitle">
                    <?php if ($categorie_id): ?>
                        Discover our initiatives in this category
                    <?php else: ?>
                        Discover our upcoming charitable initiatives and join an engaged community
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
                                        No events in this category
                                    <?php else: ?>
                                        No scheduled events
                                    <?php endif; ?>
                                </h3>
                                <p class="event-description">
                                    <?php if ($categorie_id): ?>
                                        Come back later for new initiatives in this category.
                                    <?php else: ?>
                                        We're preparing exciting new initiatives. Check back soon!
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
                        '<?= addslashes(isset($event['nom_categorie']) ? $event['nom_categorie'] : 'Général') ?>',
                        './assets_events/images/<?= isset($event['image']) ? $event['image'] : 'default-event.jpg' ?>',
                        'inscription.php?event=<?= urlencode($event['titre']) ?>&date=<?= urlencode(date('d/m/Y', strtotime($event['date_event']))) ?>&lieu=<?= urlencode($event['lieu']) ?>'
                    )">
                        <!-- Image de l'événement -->
                        <div class="event-image">
                            <img src="./assets_events/images/<?= isset($event['image']) ? $event['image'] : 'default-event.jpg' ?>" 
                                 alt="<?= htmlspecialchars($event['titre']) ?>">
                            <div class="event-badge">
                                <?= htmlspecialchars(ucfirst(isset($event['nom_categorie']) ? $event['nom_categorie'] : 'Général')) ?>
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
                                    <i class="fas fa-user-plus me-1"></i>Register
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
                        <i class="fas fa-list me-2"></i>See all events
                    </a>
                <?php else: ?>
                    <button class="load-more-btn" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-2"></i>Refresh events
                    </button>
                <?php endif; ?>
            </div>

            <!-- Spinner de chargement (caché par défaut) -->
            <div class="loading-spinner" style="display: none;">
                <div class="spinner"></div>
                <p>Loading events...</p>
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
                <img id="modern-popup-img" src="" alt="Event image">
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
                    <h5><i class="fas fa-info-circle me-2"></i>Detailed description</h5>
                    <p id="modern-popup-description"></p>
                </div>
                
                <!-- Boutons d'action -->
                <div class="modal-buttons">
                    <a id="modern-popup-link" class="modal-btn modal-btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Register for this event
                    </a>
                    <button class="modal-btn modal-btn-secondary" onclick="closeModernPopup()">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- =============== FOOTER =============== -->
    <div class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="widget">
                        <h3>Navigation</h3>
                        <ul class="list-unstyled float-left links">
                            <li><a href="index.php">Home</a></li>
                            <li><a href="events.php">Events</a></li>
                            <li><a href="list_articles.php">Articles</a></li>
                            <li><a href="index_integrated.php">Store</a></li>
                            <li><a href="indexRanim.php">Donation</a></li>
                            <li><a href="contact.html">Contact</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="widget">
                        <h3>Popular Causes</h3>
                        <ul class="list-unstyled float-left links">
                            <li><a href="indexRanim.php">Food for the Hungry</a></li>
                            <li><a href="indexRanim.php">Education for Children</a></li>
                            <li><a href="indexRanim.php">Support for Livelihood</a></li>
                            <li><a href="indexRanim.php">Medical Mission</a></li>
                            <li><a href="indexRanim.php">Community Health</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="widget">
                        <h3>Quick Links</h3>
                        <ul class="list-unstyled float-left links">
                            <li><a href="index.php#about">About Us</a></li>
                            <li><a href="events.php">Our Events</a></li>
                            <li><a href="index_integrated.php">Shop Products</a></li>
                            <li><a href="userinfo.php">My Profile</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="widget">
                        <h3>Contact</h3>
                        <address>21 Rue el baten, el ghazela, Ariana 2080</address>
                        <ul class="list-unstyled links mb-4">
                            <li><a href="tel:+21671523640">+216 71 523 640</a></li>
                            <li><a href="tel:+21697254985">+216 97 254 985</a></li>
                            <li><a href="mailto:info@peaceconnect.org">info@peaceconnect.org</a></li>
                        </ul>

                        <h3>Connect</h3>
                        <ul class="list-unstyled social">
                            <li><a href="#"><span class="icon-instagram"></span></a></li>
                            <li><a href="#"><span class="icon-twitter"></span></a></li>
                            <li><a href="#"><span class="icon-facebook"></span></a></li>
                            <li><a href="#"><span class="icon-linkedin"></span></a></li>
                            <li><a href="#"><span class="icon-pinterest"></span></a></li>
                            <li><a href="#"><span class="icon-dribbble"></span></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12 text-center">
                    <p class="copyright">Copyright &copy;<script>document.write(new Date().getFullYear());</script>. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="./assets_events/js/bootstrap.bundle.min.js"></script>
    
    <!-- Event JS -->
    <script src="./assets_events/js/event.js?v=<?php echo time(); ?>"></script>

    <!-- Store-style navbar interactions -->
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var burger = document.getElementById('burger');
        var mobile = document.getElementById('mobileMenu');
        var closeBtn = document.getElementById('closeMobile');
        var siteNav = document.getElementById('siteNav');
        var hero = document.querySelector('.video-hero') || document.querySelector('.hero');

        function openMobile(){ mobile.classList.add('open'); }
        function closeMobileMenu(){ mobile.classList.remove('open'); }
        function onScroll(){
            var trigger = hero ? hero.offsetHeight - 80 : 80;
            if(window.scrollY > trigger){ siteNav.classList.add('scrolled'); }
            else { siteNav.classList.remove('scrolled'); }
        }

        burger?.addEventListener('click', openMobile);
        closeBtn?.addEventListener('click', closeMobileMenu);
        Array.prototype.forEach.call(mobile?.querySelectorAll('a') || [], function(a){
            a.addEventListener('click', closeMobileMenu);
        });

        onScroll();
        window.addEventListener('scroll', onScroll);
    });
    </script>

</body>
</html>
