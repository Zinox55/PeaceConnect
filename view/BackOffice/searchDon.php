<?php
require_once __DIR__ . '/../../controller/CauseController.php';

$causeController = new CauseController();

// Get all causes for the dropdown
$listCauses = $causeController->listCauses();

// Get donations if a cause is selected
$donations = [];
$selectedCause = null;
if (isset($_GET['id_cause']) && !empty($_GET['id_cause'])) {
    $id_cause = intval($_GET['id_cause']);
    $donations = $causeController->afficherDonations($id_cause);
    $selectedCause = $causeController->showCause($id_cause);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Search Donations by Cause - PeaceConnect</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="sidebar-brand-text mx-3">PeaceConnect</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Management</div>
            <li class="nav-item">
                <a class="nav-link" href="tables.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Donations & Causes</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="searchDon.php">
                    <i class="fas fa-fw fa-search"></i>
                    <span>Search by Cause</span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">
                        <i class="fas fa-search"></i> Search Donations by Cause
                    </h1>

                    <!-- Search Form Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Select a Cause</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="searchDon.php">
                                <div class="row align-items-end">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="id_cause">Cause <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-lg" id="id_cause" name="id_cause" required>
                                                <option value="">-- Select a Cause --</option>
                                                <?php 
                                                if ($listCauses && $listCauses->rowCount() > 0) {
                                                    while ($cause = $listCauses->fetch(PDO::FETCH_ASSOC)) {
                                                        $selected = (isset($_GET['id_cause']) && $_GET['id_cause'] == $cause['id_cause']) ? 'selected' : '';
                                                        echo "<option value='{$cause['id_cause']}' $selected>{$cause['nom_cause']}</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                                <i class="fas fa-search"></i> Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Results Card -->
                    <?php if ($selectedCause): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-gradient-primary">
                            <h6 class="m-0 font-weight-bold text-white">
                                <i class="fas fa-bullhorn"></i> Donations for: <?= htmlspecialchars($selectedCause['nom_cause']) ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            
                            <?php if (!empty($selectedCause['description'])): ?>
                            <div class="alert alert-info">
                                <strong>About this cause:</strong> <?= htmlspecialchars($selectedCause['description']) ?>
                            </div>
                            <?php endif; ?>

                            <?php if (count($donations) > 0): ?>
                            
                            <!-- Statistics -->
                            <div class="row mb-4">
                                <div class="col-xl-4 col-md-6 mb-3">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Total Donations</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        <?= count($donations) ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-hand-holding-heart fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6 mb-3">
                                    <div class="card border-left-success shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                        Total Amount</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        <?php 
                                                        $total = array_sum(array_column($donations, 'montant'));
                                                        echo number_format($total, 2) . ' DT';
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6 mb-3">
                                    <div class="card border-left-info shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                        Average Donation</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        <?php 
                                                        $avg = $total / count($donations);
                                                        echo number_format($avg, 2) . ' DT';
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Donations Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Donor Name</th>
                                            <th>Email</th>
                                            <th>Amount</th>
                                            <th>Payment Method</th>
                                            <th>Date</th>
                                            <th>Message</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($donations as $don): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($don['id_don']) ?></td>
                                            <td><strong><?= htmlspecialchars($don['donateur_nom']) ?></strong></td>
                                            <td><?= htmlspecialchars($don['donateur_email']) ?></td>
                                            <td>
                                                <span class="badge badge-success badge-lg">
                                                    <?= htmlspecialchars($don['montant']) ?> <?= htmlspecialchars($don['devise']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $don['methode_paiement'] == 'card' ? 'primary' : ($don['methode_paiement'] == 'paypal' ? 'info' : 'success') ?>">
                                                    <?= htmlspecialchars(ucfirst($don['methode_paiement'])) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($don['date_don'])) ?></td>
                                            <td>
                                                <?php 
                                                $message = htmlspecialchars($don['message']);
                                                echo $message ? (strlen($message) > 50 ? substr($message, 0, 50) . '...' : $message) : '<em class="text-muted">No message</em>';
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php else: ?>
                            <!-- No Donations Found -->
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No donations found for this cause</h4>
                                <p class="text-muted">This cause hasn't received any donations yet.</p>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                    <?php endif; ?>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; PeaceConnect <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>