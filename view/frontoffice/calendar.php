<?php
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
    error_log("Calendar error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier des Événements - PeaceConnect</title>
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/calendar.css?v=<?php echo time(); ?>">
    
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    
    <style>
        .calendar-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 40px auto;
            max-width: 1200px;
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .calendar-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .calendar-header h1 {
            background: linear-gradient(135deg, #59886b 0%, #2c5530 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .calendar-header p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        #calendar {
            max-width: 100%;
            margin: 0 auto;
        }
        
        /* Personnalisation FullCalendar */
        .fc {
            font-family: 'Work Sans', sans-serif;
        }
        
        .fc-button {
            background: #59886b !important;
            border: none !important;
            text-transform: uppercase;
            font-weight: 600;
            padding: 10px 20px !important;
            border-radius: 8px !important;
            color: white !important;
        }
        
        .fc-button:hover {
            background: #2c5530 !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(89, 136, 107, 0.4);
        }
        
        .fc-button-active {
            background: #2c5530 !important;
        }
        
        .fc-toolbar-title {
            color: #2c3e50 !important;
            font-size: 1.5rem !important;
            font-weight: 700 !important;
        }
        
        .fc-event {
            border: none !important;
            border-radius: 6px !important;
            padding: 4px 8px !important;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .fc-event::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.3);
            transition: left 0.5s ease;
        }
        
        .fc-event:hover::before {
            left: 100%;
        }
        
        .fc-event:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .fc-daygrid-day-number {
            color: #2c3e50;
            font-weight: 600;
        }
        
        .fc-day-today {
            background-color: rgba(89, 136, 107, 0.1) !important;
        }
        
        .fc-col-header-cell {
            background: #59886b !important;
            color: white !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            padding: 15px 0 !important;
        }
        
        .fc-col-header-cell-cushion {
            color: white !important;
            padding: 8px 4px !important;
            display: block !important;
        }
        
        .fc-daygrid-day-top {
            display: flex !important;
            flex-direction: row !important;
            justify-content: center !important;
        }
        
        .fc-daygrid-day-number {
            color: #2c3e50 !important;
            font-weight: 600 !important;
            padding: 8px !important;
            font-size: 14px !important;
        }
        
        .fc-scrollgrid {
            border: 1px solid #ddd !important;
        }
        
        .fc-theme-standard td, .fc-theme-standard th {
            border: 1px solid #ddd !important;
        }
        
        /* Modal Popup pour détails événement */
        .event-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .event-modal-content {
            background: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 20px;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideDown 0.4s ease;
            overflow: hidden;
        }
        
        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .modal-header-custom {
            background: #59886b;
            color: white;
            padding: 30px;
            position: relative;
        }
        
        .modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .modal-close:hover {
            transform: rotate(90deg);
        }
        
        .modal-body-custom {
            padding: 30px;
        }
        
        .modal-title-custom {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .modal-category {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .event-detail-item {
            margin: 20px 0;
            display: flex;
            align-items: flex-start;
        }
        
        .event-detail-icon {
            width: 40px;
            height: 40px;
            background: #59886b;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .event-detail-content h4 {
            font-size: 0.85rem;
            color: #7f8c8d;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .event-detail-content p {
            font-size: 1.1rem;
            color: #2c3e50;
            margin: 0;
        }
        
        .modal-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-modal {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: block;
        }
        
        .btn-inscribe {
            background: #59886b;
            color: white;
        }
        
        .btn-inscribe:hover {
            background: #2c5530;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(89, 136, 107, 0.4);
            color: white;
        }
        
        .btn-details {
            background: #ecf0f1;
            color: #2c3e50;
        }
        
        .btn-details:hover {
            background: #bdc3c7;
            transform: translateY(-3px);
            color: #2c3e50;
        }
        
        .legend-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }
        
        @media (max-width: 768px) {
            .calendar-container {
                padding: 15px;
                margin: 20px 10px;
            }
            
            .calendar-header h1 {
                font-size: 1.8rem;
            }
            
            .event-modal-content {
                margin: 10% 10px;
            }
            
            .modal-actions {
                flex-direction: column;
            }
        }
    </style>
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
                                <li><a href="inscription.php">S'inscrire</a></li>
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
    <div class="calendar-hero">
        <h1><i class="fas fa-calendar-alt"></i>Calendrier des Événements</h1>
        <p class="calendar-hero-subtitle">Planification Interactive</p>
        <p class="calendar-hero-description">
            Explorez notre calendrier complet d'événements de volontariat à travers toute la Tunisie. 
            Sélectionnez une date pour découvrir les opportunités disponibles et rejoignez notre communauté engagée.
        </p>
    </div>

    <!-- =============== CALENDRIER =============== -->
    <div class="container">
        <div class="calendar-container">
            
            <div id="calendar"></div>
            
            <div class="legend-container">
                <div class="legend-item">
                    <div class="legend-color" style="background: #3498db;"></div>
                    <span><strong>Environnement</strong></span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #e74c3c;"></div>
                    <span><strong>Humanitaire</strong></span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #f39c12;"></div>
                    <span><strong>Éducation</strong></span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #2ecc71;"></div>
                    <span><strong>Santé</strong></span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #9b59b6;"></div>
                    <span><strong>Culture</strong></span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #34495e;"></div>
                    <span><strong>Autre</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- =============== MODAL POPUP =============== -->
    <div id="eventModal" class="event-modal">
        <div class="event-modal-content">
            <div class="modal-header-custom">
                <span class="modal-close">&times;</span>
                <h2 class="modal-title-custom" id="modalTitle"></h2>
                <span class="modal-category" id="modalCategory"></span>
            </div>
            <div class="modal-body-custom">
                <div class="event-detail-item">
                    <div class="event-detail-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="event-detail-content">
                        <h4>Date de l'événement</h4>
                        <p id="modalDate"></p>
                    </div>
                </div>
                
                <div class="event-detail-item">
                    <div class="event-detail-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="event-detail-content">
                        <h4>Lieu</h4>
                        <p id="modalLieu"></p>
                    </div>
                </div>
                
                <div class="event-detail-item">
                    <div class="event-detail-icon">
                        <i class="fas fa-align-left"></i>
                    </div>
                    <div class="event-detail-content">
                        <h4>Description</h4>
                        <p id="modalDescription"></p>
                    </div>
                </div>
                
                <div class="modal-actions">
                    <a href="#" id="modalInscriptionLink" class="btn-modal btn-inscribe">
                        <i class="fas fa-user-plus"></i> S'inscrire à cet événement
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- =============== SCRIPTS =============== -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Calendar script loaded');
            var calendarEl = document.getElementById('calendar');
            console.log('Calendar element:', calendarEl);
            
            // Fonction pour obtenir la couleur selon la catégorie
            function getCategoryColor(categoryName) {
                const colors = {
                    'Environnement': '#3498db',
                    'Humanitaire': '#e74c3c',
                    'Éducation': '#f39c12',
                    'Santé': '#2ecc71',
                    'Culture': '#9b59b6'
                };
                return colors[categoryName] || '#34495e';
            }
            
            // Données des événements depuis PHP
            var eventsData = <?php 
                $calendarEvents = [];
                if (!empty($events)) {
                    foreach ($events as $event) {
                        // Trouver l'ID (peut avoir différents noms)
                        $eventId = $event['idEvent'] ?? $event['id'] ?? $event['idevent'] ?? uniqid();
                        
                        $categoryName = isset($event['nom_categorie']) && !empty($event['nom_categorie']) ? $event['nom_categorie'] : 'Non classé';
                        
                        $calendarEvents[] = [
                            'id' => (string)$eventId,
                            'title' => $event['titre'] ?? 'Sans titre',
                            'start' => $event['date_event'] ?? date('Y-m-d'),
                            'description' => $event['description'] ?? '',
                            'lieu' => $event['lieu'] ?? '',
                            'category' => $categoryName,
                            'backgroundColor' => '#34495e',
                            'borderColor' => 'transparent'
                        ];
                    }
                }
                echo json_encode($calendarEvents, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
            ?>;
            
            // Appliquer les couleurs
            eventsData.forEach(function(event) {
                event.backgroundColor = getCategoryColor(event.category);
            });
            
            console.log('Events data:', eventsData);
            
            // Initialisation FullCalendar
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    list: 'Liste'
                },
                events: eventsData,
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    
                    // Remplir le modal avec les données
                    document.getElementById('modalTitle').textContent = info.event.title;
                    document.getElementById('modalCategory').textContent = info.event.extendedProps.category;
                    
                    // Formater la date
                    const eventDate = new Date(info.event.start);
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    document.getElementById('modalDate').textContent = eventDate.toLocaleDateString('fr-FR', options);
                    
                    document.getElementById('modalLieu').textContent = info.event.extendedProps.lieu;
                    document.getElementById('modalDescription').textContent = info.event.extendedProps.description;
                    
                    // Lien vers inscription
                    document.getElementById('modalInscriptionLink').href = 'inscription.php?event_id=' + info.event.id;
                    
                    // Afficher le modal
                    document.getElementById('eventModal').style.display = 'block';
                },
                height: 'auto',
                eventDisplay: 'block',
                displayEventTime: false
            });
            
            console.log('Rendering calendar...');
            calendar.render();
            console.log('Calendar rendered successfully!');
            
            // Fermer le modal
            document.querySelector('.modal-close').onclick = function() {
                document.getElementById('eventModal').style.display = 'none';
            };
            
            window.onclick = function(event) {
                if (event.target == document.getElementById('eventModal')) {
                    document.getElementById('eventModal').style.display = 'none';
                }
            };
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
