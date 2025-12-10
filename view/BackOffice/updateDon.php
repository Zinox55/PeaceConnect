<?php
require_once __DIR__ . '/../../controller/DonController.php';
require_once __DIR__ . '/../../controller/CauseController.php';
require_once __DIR__ . '/../../model/don.php';

$error = "";
$success = "";
$controller = new DonController();
$causeController = new CauseController();

// Get all causes for dropdown
$listCauses = $causeController->listCauses();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: tables.php?error=no_id');
    exit;
}

$id_don = $_GET['id'];

// Get the donation details
$donData = $controller->showDon($id_don);

if (!$donData) {
    header('Location: tables.php?error=donation_not_found');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validate required fields
    if (
        !empty($_POST["montant"]) &&
        !empty($_POST["donateur_nom"]) &&
        !empty($_POST["donateur_email"]) &&
        !empty($_POST["methode_paiement"]) &&
        !empty($_POST["cause"])  
    ) {
        try {
            // Create Don object with updated values
            $don = new Don(
                $id_don,
                floatval($_POST["montant"]),
                !empty($_POST["devise"]) ? $_POST["devise"] : 'DT',
                !empty($_POST["date_don"]) ? new DateTime($_POST["date_don"]) : new DateTime($donData['date_don']),
                $_POST["donateur_nom"],
                !empty($_POST["message"]) ? $_POST["message"] : '',
                $_POST["methode_paiement"],
                !empty($_POST["transaction_id"]) ? $_POST["transaction_id"] : null,
                $_POST["donateur_email"],
                intval($_POST["cause"])  
            );

            // Update in database
            $controller->updateDon($don, $id_don);
            
            $success = "Donation updated successfully!";
            
            // Refresh the data
            $donData = $controller->showDon($id_don);

        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Update Donation - PeaceConnect</title>

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

            <li class="nav-item active">
                <a class="nav-link" href="tables.php">
                    <i class="fas fa-fw fa-hand-holding-heart"></i>
                    <span>Donations</span></a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

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
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Update Donation #<?= htmlspecialchars($id_don) ?></h1>
                        <a href="tables.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
                        </a>
                    </div>

                    <!-- Success Message -->
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> <?= htmlspecialchars($success) ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Error Message -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> <?= htmlspecialchars($error) ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Update Form -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Donation Details</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        
                                        <!-- ⭐ NEW: Cause Selection Field -->
                                        <div class="form-group row">
                                            <label for="cause" class="col-sm-3 col-form-label">Cause *</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" id="cause" name="cause" required>
                                                    <option value="">-- Select a Cause --</option>
                                                    <?php 
                                                    if ($listCauses && $listCauses->rowCount() > 0) {
                                                        while ($cause = $listCauses->fetch(PDO::FETCH_ASSOC)) {
                                                            $selected = ($donData['cause'] == $cause['id_cause']) ? 'selected' : '';
                                                            echo "<option value='{$cause['id_cause']}' $selected>{$cause['nom_cause']}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="donateur_nom" class="col-sm-3 col-form-label">Donor Name *</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="donateur_nom" name="donateur_nom" 
                                                       value="<?= htmlspecialchars($donData['donateur_nom']) ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="donateur_email" class="col-sm-3 col-form-label">Email *</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" id="donateur_email" name="donateur_email" 
                                                       value="<?= htmlspecialchars($donData['donateur_email']) ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="montant" class="col-sm-3 col-form-label">Amount *</label>
                                            <div class="col-sm-9">
                                                <input type="number" step="0.01" class="form-control" id="montant" name="montant" 
                                                       value="<?= htmlspecialchars($donData['montant']) ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="devise" class="col-sm-3 col-form-label">Currency</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="devise" name="devise" 
                                                       value="<?= htmlspecialchars($donData['devise']) ?>" placeholder="DT, USD, EUR">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="methode_paiement" class="col-sm-3 col-form-label">Payment Method *</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" id="methode_paiement" name="methode_paiement" required>
                                                    <option value="">-- Select --</option>
                                                    <option value="card" <?= $donData['methode_paiement'] == 'card' ? 'selected' : '' ?>>Card</option>
                                                    <option value="paypal" <?= $donData['methode_paiement'] == 'paypal' ? 'selected' : '' ?>>PayPal</option>
                                                    <option value="cash" <?= $donData['methode_paiement'] == 'cash' ? 'selected' : '' ?>>Cash</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="date_don" class="col-sm-3 col-form-label">Donation Date</label>
                                            <div class="col-sm-9">
                                                <input type="datetime-local" class="form-control" id="date_don" name="date_don" 
                                                       value="<?= date('Y-m-d\TH:i', strtotime($donData['date_don'])) ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="transaction_id" class="col-sm-3 col-form-label">Transaction ID</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="transaction_id" name="transaction_id" 
                                                       value="<?= htmlspecialchars($donData['transaction_id'] ?? '') ?>" placeholder="Optional">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="message" class="col-sm-3 col-form-label">Message</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Optional message from donor"><?= htmlspecialchars($donData['message'] ?? '') ?></textarea>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="form-group row">
                                            <div class="col-sm-9 offset-sm-3">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Update Donation
                                                </button>
                                                <a href="tables.php" class="btn btn-secondary">
                                                    <i class="fas fa-times"></i> Cancel
                                                </a>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Donation Info Card -->
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Donation Info</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>ID:</strong> <?= htmlspecialchars($donData['id_don']) ?></p>
                                    <p><strong>Created:</strong> <?= date('M d, Y H:i', strtotime($donData['date_don'])) ?></p>
                                    <p><strong>Status:</strong> <span class="badge badge-success">Active</span></p>
                                    
                                    <hr>
                                    
                                    <a href="deleteDon.php?id=<?= $donData['id_don'] ?>" 
                                       class="btn btn-danger btn-block"
                                       onclick="return confirm('Are you sure you want to delete this donation?')">
                                        <i class="fas fa-trash"></i> Delete Donation
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; PeaceConnect <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>

        </div>

    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>