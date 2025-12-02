<?php
require_once '../../model/InscriptionModel.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inscriptionModel = new InscriptionModel();
    
    try {
        $success = $inscriptionModel->createInscription(
            $_POST['nom'],
            $_POST['email'],
            $_POST['telephone'],
            $_POST['evenement']
        );
        
        if ($success) {
            header('Location: inscription_manage.php?success=1');
            exit;
        }
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ajouter Inscription - PeaceConnect Admin</title>
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
                    <h1 class="h3 mb-4 text-gray-800">Ajouter une inscription</h1>

                    <?= $message ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Nouvelle inscription</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nom complet *</label>
                                            <input type="text" name="nom" class="form-control" required 
                                                   value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email *</label>
                                            <input type="email" name="email" class="form-control" required
                                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Téléphone</label>
                                            <input type="text" name="telephone" class="form-control" 
                                                   placeholder="8 chiffres"
                                                   value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Événement *</label>
                                            <input type="text" name="evenement" class="form-control" required
                                                   value="<?= htmlspecialchars($_POST['evenement'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Enregistrer
                                    </button>
                                    <a href="inscription_manage.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Retour
                                    </a>
                                </div>
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