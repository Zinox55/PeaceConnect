<?php
require_once '../../model/EventModel.php';
require_once '../../model/InscriptionModel.php';

$eventModel = new EventModel();
$inscriptionModel = new InscriptionModel();

$totalEvents = $eventModel->getTotalEvents();
$totalInscriptions = $inscriptionModel->getTotalInscriptions();
$recentInscriptions = $inscriptionModel->getRecentInscriptions(5);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - PeaceConnect Admin</title>
    <!-- CORRECTION : assets fusionnés -->
    <link href="../assets_events/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../assets_events/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include 'sidebar.html'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'topbar.html'; ?>

                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Événements</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalEvents ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Inscrits</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalInscriptions ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Inscriptions récentes</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Nom</th>
                                                    <th>Email</th>
                                                    <th>Événement</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($recentInscriptions as $inscrit): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($inscrit['nom']) ?></td>
                                                    <td><?= htmlspecialchars($inscrit['email']) ?></td>
                                                    <td><?= htmlspecialchars($inscrit['evenement']) ?></td>
                                                    <td><?= date('d/m/Y H:i', strtotime($inscrit['date_inscription'])) ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CORRECTION : assets fusionnés -->
    <script src="../assets_events/vendor/jquery/jquery.min.js"></script>
    <script src="../assets_events/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets_events/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../assets_events/js/sb-admin-2.min.js"></script>
</body>
</html>
