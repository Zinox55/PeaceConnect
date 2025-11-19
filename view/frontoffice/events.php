<?php
require_once '../../model/EventModel.php';

$eventModel = new EventModel();
$events = $eventModel->getAllEvents();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Événements - PeaceConnect</title>

    <link rel="stylesheet" href="../../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../../assets/css/style.css">

    <style>
        /* MODAL OVERLAY */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.55);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(2px);
        }

        /* POPUP BOÎTE - IMAGE À GAUCHE */
        .modal-box {
            background: white;
            width: 90%;
            max-width: 800px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
            animation: popIn .25s ease-out;
            position: relative;
            display: flex;
            max-height: 90vh;
        }

        @keyframes popIn {
            from { transform: scale(0.85); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }

        /* SECTION IMAGE (50%) */
        .modal-image {
            flex: 0 0 40%;
            position: relative;
        }

        .modal-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* SECTION CONTENU (50%) */
        .modal-content {
            flex: 0 0 60%;
            padding: 25px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .modal-content h2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2c5530;
        }

        .modal-details {
            margin-bottom: 15px;
        }

        .modal-details p {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 8px;
        }

        .modal-description {
            flex-grow: 1;
            margin-bottom: 20px;
        }

        .modal-description h5 {
            color: #2c5530;
            margin-bottom: 10px;
            font-weight: 600;
            border-bottom: 2px solid #59886b;
            padding-bottom: 5px;
            font-size: 1.1rem;
        }

        .modal-description p {
            font-size: 0.9rem;
            line-height: 1.5;
            color: #666;
        }

        .modal-buttons {
            margin-top: auto;
        }

        /* CARTE */
        .event-card {
            cursor: pointer;
            transition: 0.3s;
            border: none;
        }

        .event-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .modal-box {
                flex-direction: column;
                max-width: 95%;
            }
            
            .modal-image {
                flex: 0 0 200px;
            }
            
            .modal-content {
                flex: 1;
            }
        }
    </style>
</head>

<body>

    <!-- HERO -->
    <div class="hero overlay" style="background-image: url('../../assets/images/img_v_6-min.jpg')">
        <div class="container text-center">
            <span class="subheading-white text-white mb-3">Événements</span>
            <h1 class="heading text-white mb-2">Nos événements à venir</h1>
            <p class="text-white-50 lead">
                Découvrez nos opportunités de bénévolat.
            </p>
        </div>
    </div>

    <!-- SECTION ÉVÉNEMENTS -->
    <div class="section bg-light">
        <div class="container">
            <div class="row">

                <?php if (empty($events)): ?>
                    <div class="col-12 text-center py-5">
                        <h3 class="text-muted">Aucun événement à venir</h3>
                    </div>

                <?php else: ?>
                    <?php foreach($events as $event): ?>
                    <div class="col-lg-4 col-md-6 mb-4">

                        <div class="card event-card" onclick="openPopup(
                            '<?= addslashes($event['titre']) ?>',
                            '<?= addslashes(nl2br($event['description'])) ?>',
                            '<?= date('d/m/Y', strtotime($event['date_event'])) ?>',
                            '<?= addslashes($event['lieu']) ?>',
                            '../../assets/images/<?= $event['image'] ?? 'img_v_1-min.jpg' ?>',
                            'inscription.html?event=<?= urlencode($event['titre']) ?>&date=<?= urlencode(date('d/m/Y', strtotime($event['date_event']))) ?>&lieu=<?= urlencode($event['lieu']) ?>'
                        )">

                            <img src="../../assets/images/<?= $event['image'] ?? 'img_v_1-min.jpg' ?>" 
                                 style="height: 250px; object-fit: cover; border-radius:8px 8px 0 0;">

                            <div class="card-body">
                                <span class="date text-muted">
                                    <?= date('d M Y', strtotime($event['date_event'])) ?>
                                </span>

                                <h5 class="card-title mt-2"><?= htmlspecialchars($event['titre']) ?></h5>
                                <p class="card-text"><?= substr($event['description'], 0, 120) ?>...</p>
                            </div>

                        </div>

                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- POPUP -->
    <div id="popup" class="modal-overlay" onclick="outsideClose(event)">
        <div class="modal-box" id="popupContent" onclick="event.stopPropagation()">
            
            <!-- Section Image (50%) -->
            <div class="modal-image">
                <img id="popup-img" src="" alt="Image événement">
            </div>
            
            <!-- Section Contenu (50%) -->
            <div class="modal-content">
                <h2 id="popup-title"></h2>
                
                <div class="modal-details">
                    <p id="popup-date"></p>
                    <p id="popup-lieu"></p>
                </div>
                
                <div class="modal-description">
                    <h5>Description</h5>
                    <p id="popup-description"></p>
                </div>
                
                <div class="modal-buttons">
                    <a id="popup-link" class="btn btn-primary btn-lg w-100">S'inscrire</a>
                </div>
            </div>

        </div>
    </div>

    <script>
        function openPopup(titre, description, date, lieu, image, link) {
            document.getElementById("popup-title").innerText = titre;
            document.getElementById("popup-description").innerHTML = description;
            document.getElementById("popup-date").innerHTML = "<strong>Date :</strong> " + date;
            document.getElementById("popup-lieu").innerHTML = "<strong>Lieu :</strong> " + lieu;
            document.getElementById("popup-img").src = image;
            document.getElementById("popup-link").href = link;

            document.getElementById("popup").style.display = "flex";
        }

        function outsideClose(event) {
            document.getElementById("popup").style.display = "none";
        }
    </script>

</body>
</html>