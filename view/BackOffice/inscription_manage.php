<?php
require_once '../../model/InscriptionModel.php';

$inscriptionModel = new InscriptionModel();
$message = '';

// SUPPRESSION
if (isset($_GET['delete_id'])) {
    if ($inscriptionModel->deleteInscription($_GET['delete_id'])) {
        $message = '<div class="alert alert-success">Inscription supprimée avec succès!</div>';
    } else {
        $message = '<div class="alert alert-danger">Erreur lors de la suppression!</div>';
    }
}

// SUCCÈS AJOUT/MODIFICATION
if (isset($_GET['success'])) {
    $message = '<div class="alert alert-success">Opération effectuée avec succès!</div>';
}

if (isset($_GET['deleted'])) {
    $message = '<div class="alert alert-success">Inscription supprimée avec succès!</div>';
}

$inscriptions = $inscriptionModel->getAllInscriptions();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gestion des Inscriptions - PeaceConnect Admin</title>
    <link href="../FrontOffice/assets_events/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../FrontOffice/assets_events/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include 'sidebar.html'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'topbar.html'; ?>

                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Gestion des Inscriptions</h1>

                    <?= $message ?>

                    <!-- CARTES STATISTIQUES -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Inscriptions</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $inscriptionModel->getTotalInscriptions() ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BOUTON AJOUTER -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-primary">Liste des inscriptions</h6>
                                <a href="inscription_add.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Ajouter une inscription
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Événement</th>
                                            <th>Date d'inscription</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($inscriptions)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Aucune inscription trouvée</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach($inscriptions as $insc): ?>
                                            <tr>
                                                <td><?= $insc['id'] ?></td>
                                                <td><?= htmlspecialchars($insc['nom']) ?></td>
                                                <td><?= htmlspecialchars($insc['email']) ?></td>
                                                <td><?= htmlspecialchars($insc['telephone']) ?: 'Non renseigné' ?></td>
                                                <td><?= htmlspecialchars($insc['evenement']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($insc['date_inscription'])) ?></td>
                                                <td>
                                                    <a href="inscription_edit.php?id=<?= $insc['id'] ?>" 
                                                       class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i> Modifier
                                                    </a>
                                                    <a href="inscription_manage.php?delete_id=<?= $insc['id'] ?>" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription?')">
                                                        <i class="fas fa-trash"></i> Supprimer
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../FrontOffice/assets_events/vendor/jquery/jquery.min.js"></script>
    <script src="../FrontOffice/assets_events/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../FrontOffice/assets_events/js/sb-admin-2.min.js"></script>
</body>
</html>
