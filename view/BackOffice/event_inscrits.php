<?php
// CORRECTION : Chemin vers le model
require_once '../../model/InscriptionModel.php';

$inscriptionModel = new InscriptionModel();
$event_name = isset($_GET['event']) ? $_GET['event'] : '';
$inscriptions = $inscriptionModel->getInscriptionsByEvent($event_name);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrits à l'événement - PeaceConnect Admin</title>
    <link href="../FrontOffice/assets_events/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../FrontOffice/assets_events/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include 'sidebar1.html'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'topbar.html'; ?>

                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Inscrits à l'événement</h1>
                    <h4 class="text-primary"><?= htmlspecialchars($event_name) ?></h4>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Liste des inscrits (<?= count($inscriptions) ?>)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Date d'inscription</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($inscriptions as $inscrit): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($inscrit['nom']) ?></td>
                                            <td><?= htmlspecialchars($inscrit['email']) ?></td>
                                            <td><?php echo isset($inscrit['telephone']) ? htmlspecialchars($inscrit['telephone']) : 'Non renseigné'; ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($inscrit['date_inscription'])) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <a href="events_manage.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="../FrontOffice/assets_events/vendor/jquery/jquery.min.js"></script>
    <script src="../FrontOffice/assets_events/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../FrontOffice/assets_events/js/sb-admin-2.min.js"></script>
</body>
</html>
