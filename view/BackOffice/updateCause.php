<?php
require_once __DIR__ . '/../../controller/CauseController.php';
require_once __DIR__ . '/../../model/cause.php';

$error = "";
$success = "";
$controller = new CauseController();

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: causesTables.php?error=no_id');
    exit;
}

$id_cause = $_GET['id'];

// Get the cause details
$causeData = $controller->showCause($id_cause);

if (!$causeData) {
    header('Location: causesTables.php?error=cause_not_found');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validate required field
    if (!empty($_POST["nom_cause"])) {
        try {
            // Create Cause object with updated values
            $cause = new Cause(
                $id_cause,
                $_POST["nom_cause"],
                !empty($_POST["description"]) ? $_POST["description"] : null
            );

            // Update in database
            $result = $controller->updateCause($cause, $id_cause);
            
            if ($result) {
                $success = "Cause updated successfully!";
                // Refresh the data
                $causeData = $controller->showCause($id_cause);
            } else {
                $error = "Failed to update cause";
            }

        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Cause name is required";
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

    <title>Update Cause - PeaceConnect</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar (centralized) -->
        <?php include 'sidebar.html'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Update Cause #<?= htmlspecialchars($id_cause) ?></h1>
                        <a href="causesTables.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
                        </a>
                    </div>

                    <!-- Success Message -->
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-check-circle"></i> Success!</strong> <?= htmlspecialchars($success) ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Error Message -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle"></i> Error!</strong> <?= htmlspecialchars($error) ?>
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
                                    <h6 class="m-0 font-weight-bold text-primary">Cause Details</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        
                                        <div class="form-group">
                                            <label for="nom_cause">Cause Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-lg" id="nom_cause" name="nom_cause" 
                                                   value="<?= htmlspecialchars($causeData['nom_cause']) ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="8"><?php echo isset($causeData['description']) ? htmlspecialchars($causeData['description']) : ''; ?></textarea>
                                            <small class="form-text text-muted">Describe the purpose and goals of this cause.</small>
                                        </div>

                                        <hr>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fas fa-save"></i> Update Cause
                                            </button>
                                            <a href="causesTables.php" class="btn btn-secondary btn-lg">
                                                <i class="fas fa-times"></i> Cancel
                                            </a>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Cause Info Card -->
                        <div class="col-lg-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Cause Info</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>ID:</strong> <?= htmlspecialchars($causeData['id_cause']) ?></p>
                                    <p><strong>Status:</strong> <span class="badge badge-success">Active</span></p>
                                    
                                    <hr>
                                    
                                    <a href="deleteCause.php?id=<?= $causeData['id_cause'] ?>" 
                                       class="btn btn-danger btn-block"
                                       onclick="return confirm('⚠️ Are you sure you want to delete this cause?\n\nThis action cannot be undone!')">
                                        <i class="fas fa-trash"></i> Delete Cause
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

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
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>