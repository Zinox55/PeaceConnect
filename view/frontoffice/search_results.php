<?php
// Vérifier si les données existent
if (!isset($data)) {
    header('Location: events.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche - PeaceConnect</title>

    <link rel="stylesheet" href="../../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../../assets/css/style.css">


    <style>
        /* REPRENDRE TOUT LE STYLE EXISTANT DE events.php */
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

        .event-card {
            cursor: pointer;
            transition: 0.3s;
            border: none;
        }

        .event-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }

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

        /* BARRE DE RECHERCHE */
        .search-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        .search-form .form-label {
            font-weight: 600;
            color: #2c5530;
            margin-bottom: 8px;
        }

        .results-info {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <!-- HERO RECHERCHE -->
    <div class="hero overlay" style="background-image: url('../../assets/images/img_v_6-min.jpg')">
        <div class="container text-center">
            <span class="subheading-white text-white mb-3">Recherche</span>
            <h1 class="heading text-white mb-2">Résultats de recherche</h1>
            <p class="text-white-50 lead">
                <?= $data['totalResults'] ?> événement(s) trouvé(s)
            </p>
        </div>
    </div>

    <!-- BARRE DE RECHERCHE -->
    <div class="section bg-white py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form action="search.php" method="GET" class="search-form">
                        <div class="row g-3 align-items-end">
                            <!-- Champ recherche -->
                            <div class="col-md-4">
                                <label class="form-label">Rechercher</label>
                                <input type="text" name="q" class="form-control" placeholder="Titre, description..." 
                                       value="<?= htmlspecialchars($data['searchTerm']) ?>">
                            </div>
                            
                            <!-- Filtre catégorie -->
                            <div class="col-md-3">
                                <label class="form-label">Catégorie</label>
                                <select name="categorie" class="form-select">
                                    <option value="">Toutes</option>
                                    <?php foreach($data['categories'] as $categorie): ?>
                                    <option value="<?= htmlspecialchars($categorie) ?>" 
                                        <?= $data['selectedCategorie'] == $categorie ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categorie) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Filtre date -->
                            <div class="col-md-3">
                                <label class="form-label">Date</label>
                                <select name="date" class="form-select">
                                    <option value="all" <?= $data['selectedDateFilter'] == 'all' ? 'selected' : '' ?>>Toutes les dates</option>
                                    <option value="future" <?= $data['selectedDateFilter'] == 'future' ? 'selected' : '' ?>>À venir</option>
                                    <option value="past" <?= $data['selectedDateFilter'] == 'past' ? 'selected' : '' ?>>Passés</option>
                                </select>
                            </div>
                            
                            <!-- Bouton -->
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Rechercher</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- RÉSULTATS -->
    <div class="section bg-light">
        <div class="container">
            <!-- INFO RÉSULTATS -->
            <div class="results-info">
                <h5 class="mb-2">
                    <?= $data['totalResults'] ?> résultat(s) pour 
                    "<?= htmlspecialchars($data['searchTerm']) ?>"
                    <?= $data['selectedCategorie'] ? ' dans ' . htmlspecialchars($data['selectedCategorie']) : '' ?>
                </h5>
                <a href="events.php" class="btn btn-outline-primary btn-sm">Voir tous les événements</a>
            </div>

            <div class="row">
                <?php if (empty($data['events'])): ?>
                    <div class="col-12 text-center py-5">
                        <h3 class="text-muted">Aucun événement trouvé</h3>
                        <p class="text-muted">Essayez de modifier vos critères de recherche</p>
                        <a href="events.php" class="btn btn-primary">Voir tous les événements</a>
                    </div>

                <?php else: ?>
                    <?php foreach($data['events'] as $event): ?>
                    <div class="col-lg-4 col-md-6 mb-4">

                        <div class="card event-card" onclick="openPopup(
                            '<?= addslashes($event['titre']) ?>',
                            '<?= addslashes(nl2br($event['description'])) ?>',
                            '<?= date('d/m/Y', strtotime($event['date_event'])) ?>',
                            '<?= addslashes($event['lieu']) ?>',
                            '../../assets/images/<?= $event['image'] ?? 'img_v_1-min.jpg' ?>',
                            'inscription.php?event=<?= urlencode($event['titre']) ?>&date=<?= urlencode(date('d/m/Y', strtotime($event['date_event']))) ?>&lieu=<?= urlencode($event['lieu']) ?>'
                        )">

                            <img src="../../assets/images/<?= $event['image'] ?? 'img_v_1-min.jpg' ?>" 
                                 style="height: 250px; object-fit: cover; border-radius:8px 8px 0 0;">

                            <div class="card-body">
                                <span class="date text-muted">
                                    <?= date('d M Y', strtotime($event['date_event'])) ?>
                                </span>

                                <h5 class="card-title mt-2"><?= htmlspecialchars($event['titre']) ?></h5>
                                <p class="card-text"><?= substr($event['description'], 0, 120) ?>...</p>
                                
                                <?php if(isset($event['nb_inscriptions'])): ?>
                                <small class="text-muted">
                                    <i class="fas fa-users"></i> <?= $event['nb_inscriptions'] ?> inscription(s)
                                </small>
                                <?php endif; ?>
                            </div>

                        </div>

                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- POPUP (MÊME CODE QUE events.php) -->
    <div id="popup" class="modal-overlay" onclick="outsideClose(event)">
        <div class="modal-box" id="popupContent" onclick="event.stopPropagation()">
            
            <div class="modal-image">
                <img id="popup-img" src="" alt="Image événement">
            </div>
            
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