<?php
// View: Edit Comment Backend
// Formulaire d'édition d'un commentaire

include_once __DIR__ . '/../../controller/CommentaireController.php';

$commentaireController = new CommentaireController();

// Vérifier si un ID est fourni
if (!isset($_GET['id'])) {
    header("Location: comments_management.php");
    exit();
}

$commentId = $_GET['id'];
$comment = $commentaireController->edit($commentId);

if (!$comment) {
    header("Location: comments_management.php?error=1");
    exit();
}

// Messages d'erreur
$errorMessage = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case '2':
            $errorMessage = 'Erreur lors de la mise à jour du commentaire.';
            break;
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

    <title>Modifier Commentaire - Blog Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    
    <style>
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e3e6f0;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-secondary {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 24px;
            margin-right: 20px;
        }
        .comment-info {
            background: #f8f9fc;
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid #4e73df;
            margin-bottom: 30px;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard_ichrak.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-blog"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Blog Admin</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard_ichrak.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Gestion
            </div>

            <!-- Nav Item - Articles -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard_ichrak.php">
                    <i class="fas fa-fw fa-newspaper"></i>
                    <span>Articles</span></a>
            </li>
            
             <!-- Nav Item - Commentaires -->
            <li class="nav-item active">
                <a class="nav-link" href="comments_management.php">
                    <i class="fas fa-fw fa-comments"></i>
                    <span>Commentaires</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div>
                            <h1 class="h3 mb-0 text-gray-800">✏️ Modifier le Commentaire</h1>
                            <small class="text-muted">Éditer le contenu du commentaire</small>
                        </div>
                        <div class="text-muted">
                            <a href="comments_management.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Retour à la liste
                            </a>
                        </div>
                    </div>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Message d'erreur -->
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Erreur!</strong> <?php echo $errorMessage; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            
                            <!-- Comment Info Card -->
                            <div class="comment-info">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="author-avatar">
                                        <?php echo strtoupper(substr($comment['auteur'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h4 class="mb-1 text-primary font-weight-bold"><?php echo htmlspecialchars($comment['auteur']); ?></h4>
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt"></i> 
                                            <?php echo date('d M Y à H:i', strtotime($comment['date_creation'])); ?>
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-2">Article concerné:</h6>
                                    <p class="font-weight-bold text-dark mb-0">
                                        <i class="fas fa-newspaper text-primary"></i> 
                                        <?php echo htmlspecialchars($comment['article_titre'] ?? 'Article supprimé'); ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Edit Form Card -->
                            <div class="card shadow">
                                <div class="card-header py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <h6 class="m-0 font-weight-bold">
                                        <i class="fas fa-edit"></i> Formulaire de Modification
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <form action="../../controller/route_comment.php" method="POST" novalidate onsubmit="return validateForm()">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                                        
                                        <div class="form-group mb-4">
                                            <label for="auteur" class="font-weight-bold text-gray-800">
                                                <i class="fas fa-user"></i> Nom de l'auteur *
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="auteur" 
                                                   name="auteur" 
                                                   value="<?php echo htmlspecialchars($comment['auteur']); ?>" 
                                                   required>
                                            <small id="auteurError" class="text-danger"></small>
                                        </div>
                                        
                                        <div class="form-group mb-4">
                                            <label for="contenu" class="font-weight-bold text-gray-800">
                                                <i class="fas fa-comment"></i> Contenu du commentaire *
                                            </label>
                                            <textarea name="contenu" 
                                                      id="contenu" 
                                                      rows="6" 
                                                      class="form-control" 
                                                      placeholder="Contenu du commentaire..." 
                                                      required><?php echo htmlspecialchars($comment['contenu']); ?></textarea>
                                            <small id="contenuError" class="text-danger"></small>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Vous pouvez modifier le contenu du commentaire tout en préservant son authenticité.
                                            </small>
                                        </div>
                                        
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-primary btn-lg mr-3">
                                                <i class="fas fa-save"></i> Enregistrer les modifications
                                            </button>
                                            <a href="comments_management.php" class="btn btn-secondary btn-lg">
                                                <i class="fas fa-times"></i> Annuler
                                            </a>
                                        </div>
                                    </form>
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
                        <span>Copyright &copy; Blog Project 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    
    <script>
    function validateForm() {
        var auteur = document.getElementById('auteur').value;
        var contenu = document.getElementById('contenu').value;
        var isValid = true;

        // Reset error messages
        document.getElementById('auteurError').innerText = "";
        document.getElementById('contenuError').innerText = "";

        // Validate author
        if (auteur.trim() == "") {
            document.getElementById('auteurError').innerText = "Le nom de l'auteur est requis";
            isValid = false;
        } else if (auteur.trim().length < 2) {
            document.getElementById('auteurError').innerText = "Le nom doit contenir au moins 2 caractères";
            isValid = false;
        }

        // Validate content
        if (contenu.trim() == "") {
            document.getElementById('contenuError').innerText = "Le contenu du commentaire est requis";
            isValid = false;
        } else if (contenu.trim().length < 5) {
            document.getElementById('contenuError').innerText = "Le commentaire doit contenir au moins 5 caractères";
            isValid = false;
        } else if (contenu.length > 1000) {
            document.getElementById('contenuError').innerText = "Le commentaire ne peut pas dépasser 1000 caractères";
            isValid = false;
        }

        return isValid;
    }

    // Real-time validation
    document.getElementById('auteur').addEventListener('input', function() {
        var auteur = this.value;
        var errorElement = document.getElementById('auteurError');
        
        if (auteur.trim().length > 0 && auteur.trim().length < 2) {
            errorElement.innerText = "Le nom doit contenir au moins 2 caractères";
        } else {
            errorElement.innerText = "";
        }
    });

    document.getElementById('contenu').addEventListener('input', function() {
        var contenu = this.value;
        var errorElement = document.getElementById('contenuError');
        
        if (contenu.trim().length > 0 && contenu.trim().length < 5) {
            errorElement.innerText = "Le commentaire doit contenir au moins 5 caractères";
        } else if (contenu.length > 1000) {
            errorElement.innerText = "Le commentaire ne peut pas dépasser 1000 caractères";
        } else {
            errorElement.innerText = "";
        }
    });
    </script>

</body>

</html>