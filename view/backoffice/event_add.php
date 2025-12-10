<?php
// CORRECTION : Chemin vers le model
require_once '../../model/EventModel.php';

$eventModel = new EventModel();
$categories = $eventModel->getAllCategories();

// Liste des 24 gouvernorats de Tunisie
$gouvernorats = [
    "Ariana", "Béja", "Ben Arous", "Bizerte", "Gabès", "Gafsa", "Jendouba", "Kairouan",
    "Kasserine", "Kébili", "Kef", "Mahdia", "Manouba", "Médenine", "Monastir", "Nabeul",
    "Sfax", "Sidi Bouzid", "Siliana", "Sousse", "Tataouine", "Tozeur", "Tunis", "Zaghouan"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valider que le lieu est un gouvernorat valide
    $lieu = trim($_POST['lieu']);
    if (!in_array($lieu, $gouvernorats)) {
        $error = "Le lieu doit être l'un des 24 gouvernorats de Tunisie";
    } else {
        $result = $eventModel->createEvent(
            $_POST['titre'],
            $_POST['description'],
            $_POST['date_event'],
            $lieu,
            $_POST['image'],
            $_POST['categorie'] // NOUVEAU
        );
        
        if ($result) {
            header('Location: events_manage.php?success=1');
            exit;
        } else {
            $error = "Erreur lors de la création de l'événement";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Événement - PeaceConnect Admin</title>
    <link href="../../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../../assets/css/gouvernorats.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include 'sidebar.html'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'topbar.html'; ?>

                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Ajouter un Événement</h1>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label>Titre *</label>
                                    <input type="text" name="titre" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" rows="4"></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Date de l'événement *</label>
                                    <input type="date" name="date_event" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Lieu (Gouvernorat de Tunisie) *</label>
                                    <input type="text" id="lieu_input" name="lieu" class="form-control" placeholder="Commencez à taper (ex: Tu, Be, So...)" required autocomplete="off">
                                    <small class="form-text text-muted" style="display: block; margin-top: 5px;">Sélectionnez l'un des 24 gouvernorats de Tunisie</small>
                                </div>

                                <div class="form-group">
                                    <label>Catégorie</label>
                                    <select name="categorie" class="form-control">
                                        <option value="">-- Sélectionnez une catégorie --</option>
                                        <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['idCategorie'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Image (nom du fichier)</label>
                                    <input type="text" name="image" class="form-control" placeholder="ex: img_v_1-min.jpg">
                                </div>

                                <button type="submit" class="btn btn-primary">Créer l'événement</button>
                                <a href="events_manage.php" class="btn btn-secondary">Annuler</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/vendor/jquery/jquery.min.js"></script>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/sb-admin-2.min.js"></script>
    <script src="../../assets/js/gouvernorats.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initGouvernoratAutocomplete('lieu_input', 'lieu_suggestions');
            
            // Valider le formulaire
            document.querySelector('form').addEventListener('submit', function(e) {
                const lieuInput = document.getElementById('lieu_input');
                if (!isValidGouvernorat(lieuInput.value.trim())) {
                    e.preventDefault();
                    lieuInput.classList.add('is-invalid');
                    alert('Veuillez sélectionner un gouvernorat valide dans la liste');
                    return false;
                }
            });
        });
    </script>
</body>
</html>