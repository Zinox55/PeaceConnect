<?php
require_once __DIR__ . '/../../controller/DonController.php';
require_once __DIR__ . '/../../controller/CauseController.php';

$donController = new DonController();
$causeController = new CauseController();

// Get statistics
$db = config::getConnexion();

// Total donations
$totalDons = $db->query("SELECT COUNT(*) as total FROM don")->fetch()['total'];

// Total amount
$totalAmount = $db->query("SELECT SUM(montant) as total FROM don")->fetch()['total'] ?? 0;

// Total causes
$totalCauses = $db->query("SELECT COUNT(*) as total FROM cause")->fetch()['total'];

// Recent donations
$recentDons = $db->query("SELECT d.*, c.nom_cause FROM don d 
                          LEFT JOIN cause c ON d.cause = c.id_cause 
                          ORDER BY d.date_don DESC LIMIT 5")->fetchAll();

// Donations by payment method
$paymentMethods = $db->query("SELECT methode_paiement, COUNT(*) as count, SUM(montant) as total 
                               FROM don 
                               GROUP BY methode_paiement")->fetchAll();

// Top causes
$topCauses = $db->query("SELECT c.nom_cause, COUNT(d.id_don) as don_count, SUM(d.montant) as total_amount 
                         FROM cause c 
                         LEFT JOIN don d ON c.id_cause = d.cause 
                         GROUP BY c.id_cause, c.nom_cause 
                         ORDER BY total_amount DESC 
                         LIMIT 5")->fetchAll();

// Monthly donations (last 6 months)
$monthlyStats = $db->query("SELECT DATE_FORMAT(date_don, '%Y-%m') as month, 
                            COUNT(*) as count, 
                            SUM(montant) as total 
                            FROM don 
                            WHERE date_don >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                            GROUP BY month 
                            ORDER BY month DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - PeaceConnect</title>

    <!-- Custom fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    
    <style>
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div class="sidebar-brand-text mx-3">PeaceConnect</div>
            </a>

            <hr class="sidebar-divider my-0">

            <!-- Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <!-- Donations Section -->
            <div class="sidebar-heading">Management</div>

            <li class="nav-item">
                <a class="nav-link" href="tables.php">
                    <i class="fas fa-fw fa-donate"></i>
                    <span>Donations</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="causesTables.php">
                    <i class="fas fa-fw fa-heart"></i>
                    <span>Causes</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <li class="nav-item">
                <a class="nav-link" href="../FrontOffice/index.php">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Front Office</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link" href="#" data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                                <i class="fas fa-user-circle fa-2x"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End Topbar -->

                <!-- Page Content -->
                <div class="container-fluid">

                    <!-- Statistics Cards -->
                    <div class="row">
                        
                        <!-- Total Donations Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Donations
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalDons ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-donate fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Amount Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Amount
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= number_format($totalAmount, 2) ?> DT
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Causes Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Active Causes
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalCauses ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-heart fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Average Donation Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Average Donation
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $totalDons > 0 ? number_format($totalAmount / $totalDons, 2) : 0 ?> DT
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

                    <div class="row">
                        
                        <!-- Recent Donations Table -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Recent Donations</h6>
                                    <a href="tables.php" class="btn btn-sm btn-primary">View All</a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Donor</th>
                                                    <th>Amount</th>
                                                    <th>Cause</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentDons as $don): ?>
                                                <tr>
                                                    <td>#<?= $don['id_don'] ?></td>
                                                    <td><?= htmlspecialchars($don['donateur_nom']) ?></td>
                                                    <td><strong><?= number_format($don['montant'], 2) ?> <?= strtoupper($don['devise']) ?></strong></td>
                                                    <td><span class="badge badge-info"><?= htmlspecialchars($don['nom_cause']) ?></span></td>
                                                    <td><?= date('M d, Y', strtotime($don['date_don'])) ?></td>
                                                    <td>
                                                        <a href="exportDonPDF.php?id=<?= $don['id_don'] ?>" class="btn btn-sm btn-success" target="_blank">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Methods Chart -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Payment Methods</h6>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($paymentMethods as $method): ?>
                                    <div class="mb-3">
                                        <div class="small mb-1">
                                            <strong><?= ucfirst($method['methode_paiement']) ?></strong>
                                            <span class="float-right"><?= $method['count'] ?> donations</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" role="progressbar" 
                                                 style="width: <?= ($method['count'] / $totalDons) * 100 ?>%">
                                                <?= number_format($method['total'], 0) ?> DT
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Causes -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Top Causes by Donations</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Cause</th>
                                                    <th>Number of Donations</th>
                                                    <th>Total Amount</th>
                                                    <th>Progress</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $maxAmount = $topCauses[0]['total_amount'] ?? 1;
                                                foreach ($topCauses as $cause): 
                                                    $percentage = ($cause['total_amount'] / $maxAmount) * 100;
                                                ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($cause['nom_cause']) ?></strong></td>
                                                    <td><?= $cause['don_count'] ?></td>
                                                    <td><strong><?= number_format($cause['total_amount'], 2) ?> DT</strong></td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar bg-success" style="width: <?= $percentage ?>%">
                                                                <?= round($percentage) ?>%
                                                            </div>
                                                        </div>
                                                    </td>
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
                <!-- End Page Content -->

            </div>
            <!-- End Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; PeaceConnect <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>

        </div>
        <!-- End Content Wrapper -->

    </div>
    <!-- End Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>
</html>
