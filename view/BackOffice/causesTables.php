<?php
require_once __DIR__ . "/../../controller/CauseController.php";

$controller = new CauseController();
$listCauses = $controller->listCauses(); // Fetch all causes
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>PeaceConnect - Causes Management</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("addCauseForm");
    
    if (!form) {
        console.error("Form not found!");
        return;
    }
    
    form.addEventListener("submit", function(event) {
        const nomCause = document.getElementById("nom_cause").value.trim();
        
        if (nomCause === "") {
            alert("❌ Le nom de la cause est obligatoire!");
            event.preventDefault();
            return false;
        }
        
        if (nomCause.length < 3) {
            alert("❌ Le nom de la cause doit contenir au moins 3 caractères!");
            event.preventDefault();
            return false;
        }
        
        if (nomCause.length > 100) {
            alert("❌ Le nom de la cause ne peut pas dépasser 100 caractères!");
            event.preventDefault();
            return false;
        }
        
        const description = document.getElementById("description").value.trim();
        if (description.length > 500) {
            alert("❌ La description ne peut pas dépasser 500 caractères!");
            event.preventDefault();
            return false;
        }
        
        return true;
    });
});
</script>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="sidebar-brand-text mx-3">PeaceConnect</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="indexRanim.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Management
            </div>

            <!-- Nav Item - Donations -->
            <li class="nav-item">
                <a class="nav-link" href="tables.php">
                    <i class="fas fa-fw fa-hand-holding-heart"></i>
                    <span>Donations</span></a>
            </li>

            <!-- Nav Item - Causes -->
            <li class="nav-item active">
                <a class="nav-link" href="causesTables.php">
                    <i class="fas fa-fw fa-bullhorn"></i>
                    <span>Causes</span></a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="searchDon.php">
                    <i class="fas fa-fw fa-search"></i>
                    <span>Search Donations</span></a>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

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
                        <div>
                            <h1 class="h3 mb-0 text-gray-800">Causes Management</h1>
                            <p class="mb-0">View and manage all causes for donations.</p>
                        </div>
                        <button class="btn btn-success btn-icon-split shadow-sm" data-toggle="modal" data-target="#addCauseModal">
                            <span class="icon text-white-50">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="text">Add New Cause</span>
                        </button>
                    </div>

                    <!-- Success/Error Messages -->
                    <?php if (isset($_GET['added']) && $_GET['added'] == 'success'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-check-circle"></i> Success!</strong> 
                            Cause <strong><?= htmlspecialchars($_GET['cause'] ?? 'cause') ?></strong> added successfully!
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'success'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-check-circle"></i> Success!</strong> Cause deleted successfully.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle"></i> Error!</strong> <?= htmlspecialchars($_GET['error']) ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Causes Table -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">All Causes</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">ID</th>
                                            <th>Cause Name</th>
                                            <th>Description</th>
                                            <th style="width: 120px;">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php 
                                        if ($listCauses && $listCauses->rowCount() > 0) {
                                            while ($cause = $listCauses->fetch(PDO::FETCH_ASSOC)) { 
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($cause['id_cause']) ?></td>
                                                <td><strong><?= htmlspecialchars($cause['nom_cause']) ?></strong></td>
                                                <td>
                                                    <?php 
                                                    $description = htmlspecialchars($cause['description']);
                                                    echo $description ? (strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description) : '<em class="text-muted">No description</em>';
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="updateCause.php?id=<?= $cause['id_cause'] ?>" 
                                                           class="btn btn-sm btn-warning" 
                                                           title="Edit Cause"
                                                           data-toggle="tooltip">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="deleteCause.php?id=<?= $cause['id_cause'] ?>" 
                                                           class="btn btn-sm btn-danger" 
                                                           title="Delete Cause"
                                                           data-toggle="tooltip"
                                                           onclick="return confirm('⚠️ Are you sure you want to delete the cause: <?= htmlspecialchars($cause['nom_cause']) ?>?\n\nThis action cannot be undone!')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php 
                                            }
                                        } else {
                                        ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                    <strong>No causes found</strong>
                                                    <p class="mb-0">Causes will appear here once they are created.</p>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
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

    <!-- Add Cause Modal -->
    <div class="modal fade" id="addCauseModal" tabindex="-1" role="dialog" aria-labelledby="addCauseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addCauseModalLabel">
                        <i class="fas fa-bullhorn"></i> Add New Cause
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="addCauseBackoffice.php" id="addCauseForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nom_cause">Cause Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nom_cause" name="nom_cause" 
                                   placeholder="e.g., Education for Children" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5" 
                                      placeholder="Describe the cause and its purpose..."></textarea>
                            <small class="form-text text-muted">Optional - Provide details about this cause.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Add Cause
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

    <!-- Enable tooltips -->
    <script>
        $(document).ready(function() {
            // Enable Bootstrap tooltips
            $('[data-toggle="tooltip"]').tooltip();
            
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>

    <style>
        /* Action buttons styling */
        .btn-group .btn {
            margin: 0 2px;
        }
        
        /* Hover effect for table rows */
        #dataTable tbody tr:hover {
            background-color: #f8f9fc;
        }
        
        /* Empty state styling */
        .text-muted .fa-inbox {
            color: #d1d3e2;
        }
        
        /* Modal styling */
        .modal-header.bg-success {
            background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
        }
        
        /* Add button icon split styling */
        .btn-icon-split .icon {
            padding: 0.5rem 0.75rem;
            border-right: 1px solid rgba(255,255,255,0.3);
        }
        
        .btn-icon-split .text {
            padding: 0.5rem 0.75rem;
        }
        
        /* Form required asterisk */
        .text-danger {
            color: #e74a3b !important;
        }
        
        /* Modal backdrop */
        .modal-backdrop.show {
            opacity: 0.7;
        }
    </style>

</body>

</html>