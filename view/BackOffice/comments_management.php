<?php
// View: Comments Management Backend
// Gestion des commentaires dans le dashboard administrateur

include_once __DIR__ . '/../../controller/CommentaireController.php';

$commentaireController = new CommentaireController();
$comments = $commentaireController->index();

// Messages de succès/erreur
$successMessage = '';
$errorMessage = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case '2':
            $successMessage = 'Commentaire mis à jour avec succès!';
            break;
        case '3':
            $successMessage = 'Commentaire supprimé avec succès!';
            break;
    }
}
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case '2':
            $errorMessage = 'Erreur lors de la mise à jour du commentaire.';
            break;
        case '3':
            $errorMessage = 'Erreur lors de la suppression du commentaire.';
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

    <title>Gestion des Commentaires - Blog Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <style>
        .table td {
            vertical-align: middle;
        }
        .comment-content {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .comment-content:hover {
            white-space: normal;
            overflow: visible;
        }
        .badge {
            padding: 0.5em 0.8em;
            font-size: 0.85em;
            font-weight: 600;
        }
        .btn-action-group {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .author-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            margin-right: 10px;
        }
        .comment-author {
            display: flex;
            align-items: center;
        }
    </style>
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
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div>
                            <h1 class="h3 mb-0 text-gray-800">💬 Gestion des Commentaires</h1>
                            <small class="text-muted">Modérer et gérer tous les commentaires</small>
                        </div>
                        <div class="text-muted">
                            <i class="far fa-calendar-alt"></i> <?php echo date('d M Y'); ?>
                        </div>
                    </div>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Messages de succès/erreur -->
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Succès!</strong> <?php echo $successMessage; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($errorMessage): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Erreur!</strong> <?php echo $errorMessage; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- Comments Table -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <div>
                                <h6 class="m-0 font-weight-bold text-primary">💬 Liste des Commentaires</h6>
                                <small class="text-muted">Gérez et modérez tous les commentaires</small>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Auteur</th>
                                            <th>Commentaire</th>
                                            <th>Article</th>
                                            <th>Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $comments->fetch(PDO::FETCH_ASSOC)): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold"><?php echo $row['id']; ?></td>
                                            <td>
                                                <div class="comment-author">
                                                    <div class="author-avatar">
                                                        <?php echo strtoupper(substr($row['auteur'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <div class="font-weight-bold"><?php echo htmlspecialchars($row['auteur']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="comment-content" title="<?php echo htmlspecialchars($row['contenu']); ?>">
                                                    <?php echo htmlspecialchars(substr($row['contenu'], 0, 100)) . (strlen($row['contenu']) > 100 ? '...' : ''); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-primary font-weight-bold">
                                                    <?php echo htmlspecialchars(isset($row['article_titre']) ? $row['article_titre'] : 'Article supprimé'); ?>
                                                </div>
                                                <small class="text-muted">ID: <?php echo $row['article_id']; ?></small>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="far fa-calendar"></i> <?php echo date('d/m/Y', strtotime($row['date_creation'])); ?><br>
                                                    <i class="far fa-clock"></i> <?php echo date('H:i', strtotime($row['date_creation'])); ?>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-action-group">
                                                    <button class="btn btn-info btn-sm" onclick="viewComment(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['auteur'])); ?>', '<?php echo htmlspecialchars(addslashes($row['contenu'])); ?>', '<?php echo $row['date_creation']; ?>', '<?php echo htmlspecialchars(addslashes(isset($row['article_titre']) ? $row['article_titre'] : 'Article supprimé')); ?>')" title="Visualiser" data-toggle="tooltip">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="edit_comment.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm" title="Modifier" data-toggle="tooltip">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="../../controller/route_comment.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce commentaire ?\n\nCette action est irréversible.')" title="Supprimer" data-toggle="tooltip">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
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
                        <span>Copyright &copy; Blog Project 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Comment View Modal -->
    <div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title" id="commentModalLabel">
                        <i class="fas fa-comment"></i> Détails du commentaire
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="author-avatar" style="width: 50px; height: 50px; font-size: 20px;" id="modalAuthorAvatar"></div>
                        <div>
                            <h4 id="modalAuthor" class="mb-1 text-primary font-weight-bold"></h4>
                            <small class="text-muted">
                                <i class="far fa-calendar-alt"></i> <span id="modalDate"></span>
                            </small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">Article concerné:</h6>
                        <p id="modalArticle" class="font-weight-bold text-dark"></p>
                    </div>
                    <hr>
                    <div>
                        <h6 class="text-muted">Contenu du commentaire:</h6>
                        <div id="modalContent" style="background: #f8f9fc; padding: 20px; border-radius: 8px; border-left: 4px solid #4e73df; white-space: pre-wrap; line-height: 1.6;"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Fermer
                    </button>
                    <a id="editCommentBtn" href="#" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier le commentaire
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    
    <script>
    // Initialize DataTable with French language
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "lengthMenu": "Afficher _MENU_ commentaires par page",
                "zeroRecords": "Aucun commentaire trouvé",
                "info": "Page _PAGE_ sur _PAGES_",
                "infoEmpty": "Aucun commentaire disponible",
                "infoFiltered": "(filtré de _MAX_ commentaires au total)",
                "search": "Rechercher:",
                "paginate": {
                    "first": "Premier",
                    "last": "Dernier",
                    "next": "Suivant",
                    "previous": "Précédent"
                }
            },
            "order": [[0, "desc"]],
            "pageLength": 10
        });
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
    
    // Function to view comment in modal
    function viewComment(id, auteur, contenu, date, article) {
        // Format date
        var dateObj = new Date(date);
        var formattedDate = dateObj.toLocaleDateString('fr-FR', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Set modal content
        document.getElementById('modalAuthor').innerText = auteur;
        document.getElementById('modalDate').innerText = formattedDate;
        document.getElementById('modalArticle').innerText = article;
        document.getElementById('modalContent').innerText = contenu;
        
        // Set author avatar
        document.getElementById('modalAuthorAvatar').innerText = auteur.charAt(0).toUpperCase();
        
        // Set edit button link
        document.getElementById('editCommentBtn').href = 'edit_comment.php?id=' + id;
        
        // Show modal
        $('#commentModal').modal('show');
    }
    </script>

</body>

</html>