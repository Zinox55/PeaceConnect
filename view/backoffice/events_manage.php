<?php
require_once '../../model/EventModel.php';
require_once '../../model/InscriptionModel.php';

$eventModel = new EventModel();
$inscriptionModel = new InscriptionModel();
$events = $eventModel->getAllEventsWithCategory();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Événements - PeaceConnect Admin</title>
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
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Gestion des Événements</h1>
                    </div>

                    <!-- Messages -->
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> Opération effectuée avec succès!
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-calendar-alt"></i> Liste des Événements
                                        <span class="badge badge-primary badge-pill ml-2"><?= count($events) ?></span>
                                    </h6>
                                    <div>
                                        <a href="event_add.php" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus"></i> Ajouter
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($events)): ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-calendar-times fa-3x text-gray-300 mb-3"></i>
                                            <h5 class="text-gray-500">Aucun événement créé</h5>
                                            <p class="text-gray-500">Cliquez sur le bouton "Ajouter" pour créer votre premier événement</p>
                                        </div>
                                    <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Titre</th>
                                                    <th>Date</th>
                                                    <th>Lieu</th>
                                                    <th>Catégorie</th>
                                                    <th>Inscrits</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($events as $event): 
                                                    $inscriptions = $inscriptionModel->getInscriptionsByEvent($event['titre']);
                                                ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($event['titre']) ?></strong>
                                                        <?php if ($event['image']): ?>
                                                            <br><small class="text-muted"><i class="fas fa-image"></i> <?= $event['image'] ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-calendar-day text-primary"></i>
                                                        <?= date('d/m/Y', strtotime($event['date_event'])) ?>
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                                        <?= htmlspecialchars($event['lieu']) ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-secondary">
                                                            <i class="fas fa-tag"></i> <?= htmlspecialchars($event['nom_categorie'] ?? 'Non catégorisé') ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-info badge-pill">
                                                            <i class="fas fa-users"></i> <?= count($inscriptions) ?> inscrits
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="event_edit.php?id=<?= $event['id'] ?>" class="btn btn-warning btn-sm" title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="event_inscrits.php?event=<?= urlencode($event['titre']) ?>" class="btn btn-info btn-sm" title="Voir les inscrits">
                                                                <i class="fas fa-users"></i>
                                                            </a>
                                                            <button onclick="deleteEvent(<?= $event['id'] ?>, '<?= htmlspecialchars($event['titre']) ?>')" class="btn btn-danger btn-sm" title="Supprimer">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>PeaceConnect Admin &copy; <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap & JavaScript -->
    <script src="../../assets/vendor/jquery/jquery.min.js"></script>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../../assets/js/sb-admin-2.min.js"></script>
    
    <script>
    function deleteEvent(eventId, eventTitle) {
        if (confirm(`Êtes-vous sûr de vouloir supprimer l'événement "${eventTitle}" ?\nCette action est irréversible.`)) {
            fetch('../../controller/EventController.php?action=delete&id=' + eventId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Message de succès avec auto-dismiss
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show';
                        alert.innerHTML = `
                            <i class="fas fa-check-circle"></i> Événement supprimé avec succès
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        `;
                        document.querySelector('.container-fluid').prepend(alert);
                        
                        // Rafraîchir après 1 seconde
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Erreur réseau: ' + error);
                });
        }
    }

    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    </script>
</body>
</html>