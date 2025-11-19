<?php
// CORRECTION : Chemin vers le model
require_once '../../model/EventModel.php';

$eventModel = new EventModel();
$event = null;

// Récupérer l'événement à modifier
if (isset($_GET['id'])) {
    $event = $eventModel->getEventById($_GET['id']);
}

if (!$event) {
    header('Location: events_manage.php?error=Événement non trouvé');
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $eventModel->updateEvent(
        $_POST['id'],
        $_POST['titre'],
        $_POST['description'],
        $_POST['date_event'],
        $_POST['lieu'],
        $_POST['image']
    );
    
    if ($result) {
        header('Location: events_manage.php?success=1');
        exit;
    } else {
        $error = "Erreur lors de la modification de l'événement";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'Événement - PeaceConnect Admin</title>
    <link href="../../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include 'sidebar.html'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'topbar.html'; ?>

                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Modifier l'Événement</h1>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $event['id'] ?>">
                                
                                <div class="form-group">
                                    <label>Titre *</label>
                                    <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($event['titre']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($event['description']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Date de l'événement *</label>
                                    <input type="date" name="date_event" class="form-control" value="<?= $event['date_event'] ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Lieu *</label>
                                    <input type="text" name="lieu" class="form-control" value="<?= htmlspecialchars($event['lieu']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Image (nom du fichier)</label>
                                    <input type="text" name="image" class="form-control" value="<?= htmlspecialchars($event['image'] ?? '') ?>" placeholder="ex: img_v_1-min.jpg">
                                </div>

                                <button type="submit" class="btn btn-primary">Modifier l'événement</button>
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
</body>
</html>