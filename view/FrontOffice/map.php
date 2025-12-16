<?php
session_start();
if (!isset($_SESSION['e'])) {
    header('Location: signin.php');
    exit();
}

// Désactiver l'affichage des erreurs dans le HTML (pour éviter de casser le JSON)
error_reporting(0);
ini_set('display_errors', 0);

require_once '../../model/EventModel.php';

try {
    $eventModel = new EventModel();
    $events = $eventModel->getAllEventsWithCategory();
    
    // Vérifier que $events est un tableau
    if (!is_array($events)) {
        $events = [];
    }
} catch (Exception $e) {
    // En cas d'erreur, tableau vide
    $events = [];
    error_log("Map error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Map - PeaceConnect</title>
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <link rel="stylesheet" href="./assets_events/css/bootstrap.css">
    <link rel="stylesheet" href="./assets_events/css/style.css">
    <link rel="stylesheet" href="./assets_events/css/map.css">
</head>
<body>

    <!-- =============== HEADER =============== -->
    <nav class="site-nav">
        <div class="container">
            <div class="menu-bg-wrap">
                <div class="site-navigation">
                    <div class="row g-0 align-items-center">
                        <div class="col-2">
                            <a href="index.php" class="logo m-0 float-start text-white">PeaceConnect</a>
                        </div>
                        <div class="col-8 text-center">
                            <ul class="js-clone-nav d-none d-lg-inline-block text-start site-menu mx-auto">
                                <li><a href="index.php">Home</a></li>
                                <li><a href="events.php">Events</a></li>
                                <li class="active"><a href="map.php">Map</a></li>
                                <li><a href="contact.html">Contact</a></li>
                                <li><a href="userinfo.php">User</a></li>
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

    <!-- =============== SECTION HÉRO VERTE =============== -->
    <div class="map-hero">
        <h1>Event Map</h1>
        <p class="map-hero-subtitle">Join our volunteer community across Tunisia's 24 governorates</p>
        <p class="map-hero-description">
            At PeaceConnect, we create meaningful events in every corner of Tunisia. 
            From environment to education, from humanitarian aid to health, we work across all 24 governorates to 
            create positive impact. Explore our interactive map and find the event that matches your passions.
        </p>
    </div>

    <!-- =============== CONTENU =============== -->
    <div class="map-container">
        <!-- Éléments cachés (pour compatibilité) -->
        <div class="map-header" style="display:none;">
            <h1>Event Map</h1>
        </div>
        
        <div class="stats-bar" style="display:none;">
            <div class="stat-item">
                <div class="stat-number"><?php echo count($events); ?></div>
                <div class="stat-label">Events</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php 
                    $lieux = array_unique(array_column($events, 'lieu'));
                    echo count($lieux);
                ?></div>
                <div class="stat-label">Locations</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php 
                    $categories = array_filter(array_unique(array_column($events, 'nom_categorie')));
                    echo count($categories);
                ?></div>
                <div class="stat-label">Categories</div>
            </div>
        </div>
        
        <div class="map-wrapper">
            <div id="map"></div>
            
            <div class="events-sidebar">
                <div class="sidebar-header">
                    <h3><i class="fas fa-list"></i> Events</h3>
                </div>
                
                <div id="eventsList">
                    <?php 
                    $categoryColors = [
                        'Environnement' => '#59886b',
                        'Humanitaire' => '#e74c3c',
                        'Éducation' => '#f39c12',
                        'Santé' => '#2ecc71',
                        'Culture' => '#9b59b6'
                    ];
                    
                    foreach ($events as $index => $event): 
                        $categoryName = isset($event['nom_categorie']) && !empty($event['nom_categorie']) ? $event['nom_categorie'] : 'Autre';
                        $categoryColor = isset($categoryColors[$categoryName]) ? $categoryColors[$categoryName] : '#34495e';
                    ?>
                    <div class="event-card" data-event-id="<?php echo htmlspecialchars($event['idEvent']); ?>">
                        <span class="event-card-category" style="background: <?php echo htmlspecialchars($categoryColor); ?>;">
                            <?php echo htmlspecialchars($categoryName); ?>
                        </span>
                        <h4><?php echo htmlspecialchars($event['titre']); ?></h4>
                        <div class="event-card-info">
                            <div class="event-card-info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <span><?php echo htmlspecialchars(date('d/m/Y', strtotime($event['date_event']))); ?></span>
                            </div>
                            <div class="event-card-info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($event['lieu']); ?></span>
                            </div>
                        </div>
                        <a href="inscription.php?event_id=<?php echo htmlspecialchars($event['idEvent']); ?>" class="event-card-btn">
                            <i class="fas fa-user-plus"></i> S'inscrire
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- =============== SCRIPTS =============== -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="./assets_events/js/gouvernorats.js"></script>
    
    <script>
        // Données des événements avec coordonnées GPS précises des gouvernorats
        const eventsData = <?php 
            $mapEvents = [];
            
            // Tableau des coordonnées des gouvernorats de Tunisie
            $gouvernoratsCoords = [
                'Ariana' => [36.8565, 10.1647],
                'Béja' => [36.7256, 9.1844],
                'Ben Arous' => [36.7592, 10.2372],
                'Bizerte' => [37.2744, 9.8739],
                'Gabès' => [33.8869, 10.0994],
                'Gafsa' => [34.4258, 8.7852],
                'Jendouba' => [36.5024, 8.7755],
                'Kairouan' => [35.6781, 9.9197],
                'Kasserine' => [35.1656, 8.8341],
                'Kébili' => [33.7392, 9.8007],
                'Kef' => [36.1761, 8.7139],
                'Mahdia' => [35.5047, 11.0625],
                'Manouba' => [36.8117, 10.3903],
                'Médenine' => [33.3540, 10.5038],
                'Monastir' => [35.7789, 10.8311],
                'Nabeul' => [36.4563, 10.7367],
                'Sfax' => [34.7406, 10.7605],
                'Sidi Bouzid' => [35.0347, 9.4898],
                'Siliana' => [36.0208, 9.3721],
                'Sousse' => [35.8256, 10.6369],
                'Tataouine' => [32.9289, 10.4547],
                'Tozeur' => [33.9197, 8.1353],
                'Tunis' => [36.8065, 10.1957],
                'Zaghouan' => [36.4025, 10.1437]
            ];
            
            if (!empty($events)) {
                foreach ($events as $event) {
                    $lieu = isset($event['lieu']) ? trim($event['lieu']) : 'Tunis';
                    $coords = null;
                    
                    // Chercher les coordonnées exactes dans la liste des gouvernorats
                    foreach ($gouvernoratsCoords as $gov => $govCoords) {
                        if (strcasecmp($lieu, $gov) === 0) {
                            $coords = $govCoords;
                            break;
                        }
                    }
                    
                    // Coordonnées par défaut (Tunis) si lieu non trouvé
                    if (!$coords) {
                        $coords = [36.8065, 10.1957];
                        // Ajouter un léger décalage aléatoire pour éviter la superposition
                        $coords[0] += (mt_rand(-50, 50) / 1000);
                        $coords[1] += (mt_rand(-50, 50) / 1000);
                    }
                    
                    // Trouver l'ID (peut avoir différents noms)
                    $eventId = $event['idEvent'] ?? $event['id'] ?? $event['idevent'] ?? uniqid();
                    
                    $categoryName = isset($event['nom_categorie']) && !empty($event['nom_categorie']) ? $event['nom_categorie'] : 'Autre';
                    $categoryColors = [
                        'Environnement' => '#59886b',
                        'Humanitaire' => '#e74c3c',
                        'Éducation' => '#f39c12',
                        'Santé' => '#2ecc71',
                        'Culture' => '#9b59b6'
                    ];
                    
                    $mapEvents[] = [
                        'id' => (string)$eventId,
                        'titre' => $event['titre'] ?? 'Sans titre',
                        'description' => $event['description'] ?? '',
                        'date' => $event['date_event'] ?? date('Y-m-d'),
                        'lieu' => $lieu,
                        'category' => $categoryName,
                        'color' => isset($categoryColors[$categoryName]) ? $categoryColors[$categoryName] : '#34495e',
                        'lat' => floatval($coords[0]),
                        'lng' => floatval($coords[1])
                    ];
                }
            }
            echo json_encode($mapEvents, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
        ?>;
        
        // Initialisation de la carte centrée sur la Tunisie
        const map = L.map('map').setView([34.0, 9.5], 7);
        
        // Tile layer OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Stocker les marqueurs
        const markers = {};
        
        // Ajouter les marqueurs pour chaque événement
        eventsData.forEach(function(event) {
            // Créer une icône personnalisée
            const customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: `<div class="custom-marker" style="background: ${event.color};"><i class="fas fa-heart"></i></div>`,
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                popupAnchor: [0, -40]
            });
            
            // Créer le marqueur
            const marker = L.marker([event.lat, event.lng], { icon: customIcon }).addTo(map);
            
            // Formater la date
            const eventDate = new Date(event.date);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = eventDate.toLocaleDateString('fr-FR', options);
            
            // Contenu du popup
            const popupContent = `
                <div class="popup-header">
                    <span class="popup-category">${event.category}</span>
                    <h3 class="popup-title">${event.titre}</h3>
                </div>
                <div class="popup-body">
                    <div class="popup-info-item">
                        <div class="popup-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="popup-info-content">
                            <h5>Date</h5>
                            <p>${formattedDate}</p>
                        </div>
                    </div>
                    <div class="popup-info-item">
                        <div class="popup-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="popup-info-content">
                            <h5>📍 Lieu</h5>
                            <p>${event.lieu}</p>
                        </div>
                    </div>
                    <div class="popup-info-item">
                        <div class="popup-icon">
                            <i class="fas fa-align-left"></i>
                        </div>
                        <div class="popup-info-content">
                            <h5>Description</h5>
                            <p>${event.description.substring(0, 100)}...</p>
                        </div>
                    </div>
                    <a href="inscription.php?event_id=${event.id}" class="popup-btn">
                        <i class="fas fa-user-plus"></i> Register for this event
                    </a>
                </div>
            `;
            
            marker.bindPopup(popupContent, {
                maxWidth: 300,
                className: 'custom-popup'
            });
            
            // Stocker le marqueur
            markers[event.id] = marker;
        });
        
        // Interaction avec la sidebar
        document.querySelectorAll('.event-card').forEach(function(card) {
            card.addEventListener('click', function(e) {
                // Ne pas déclencher si on clique sur le bouton
                if (e.target.classList.contains('event-card-btn') || e.target.closest('.event-card-btn')) {
                    return;
                }
                
                const eventId = this.getAttribute('data-event-id');
                const marker = markers[eventId];
                
                if (marker) {
                    // Retirer la classe active de toutes les cartes
                    document.querySelectorAll('.event-card').forEach(c => c.classList.remove('active'));
                    
                    // Ajouter la classe active à la carte cliquée
                    this.classList.add('active');
                    
                    // Centrer la carte sur le marqueur et ouvrir le popup
                    map.setView(marker.getLatLng(), 12);
                    marker.openPopup();
                }
            });
        });
    </script>

    <!-- =============== FOOTER =============== -->
    <footer style="background: #2c3e50; color: white; padding: 40px 0; margin-top: 60px;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 style="color: #59886b; font-weight: 700; margin-bottom: 20px;">PeaceConnect</h4>
                    <p style="color: #bdc3c7; line-height: 1.8;">
                        Join our volunteer community and participate in events that make a difference.
                    </p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 style="color: #59886b; font-weight: 600; margin-bottom: 20px;">Navigation</h5>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px;"><a href="events.php" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">📅 Events</a></li>
                        <li style="margin-bottom: 10px;"><a href="calendar.php" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">📆 Calendar</a></li>
                        <li style="margin-bottom: 10px;"><a href="map.php" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">🗺️ Map</a></li>
                        <li style="margin-bottom: 10px;"><a href="inscription.php" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">✍️ Register</a></li>
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
                <p style="margin: 0;">&copy; 2025 PeaceConnect. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script>
// Animation au scroll
document.addEventListener('DOMContentLoaded', function() {
    // Bouton retour en haut
    const backToTop = document.createElement('a');
    backToTop.href = '#';
    backToTop.className = 'back-to-top';
    backToTop.innerHTML = '<i class="fas fa-chevron-up"></i>';
    document.body.appendChild(backToTop);
    
    // Scroll event
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
        
        // Animation des stats au scroll
        const stats = document.querySelectorAll('.map-hero-stat');
        stats.forEach(stat => {
            const rect = stat.getBoundingClientRect();
            if (rect.top < window.innerHeight * 0.8) {
                stat.style.opacity = '1';
                stat.style.transform = 'translateY(0)';
            }
        });
    });
    
    // Smooth scroll pour retour en haut
    backToTop.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Effet hover amélioré sur les cartes
    const eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.zIndex = '10';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
    });
    
    // Initial animation for stats
    const stats = document.querySelectorAll('.map-hero-stat');
    stats.forEach((stat, index) => {
        stat.style.opacity = '0';
        stat.style.transform = 'translateY(20px)';
        stat.style.transitionDelay = `${index * 0.1}s`;
        
        setTimeout(() => {
            stat.style.opacity = '1';
            stat.style.transform = 'translateY(0)';
        }, 500 + index * 100);
    });
});
</script>

</body>
</html>

