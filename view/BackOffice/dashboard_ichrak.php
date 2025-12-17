<?php
// View: Dashboard Backend
// Affichage du tableau de bord administrateur

include_once __DIR__ . '/../../controller/ArticleController.php';
include_once __DIR__ . '/../../controller/CommentaireController.php';
include_once __DIR__ . '/../../controller/NewsletterController.php';

$articleController = new ArticleController();
$newsletterController = new NewsletterController();
$subscriberCount = $newsletterController->getSubscriberCount();
$stats = $articleController->getStats();
$articles = $articleController->index();
$topArticles = $articleController->getTopPosts(3);
$topLabels = [];
$topData = [];
while($row = $topArticles->fetch(PDO::FETCH_ASSOC)) {
    $topLabels[] = $row['titre'];
    $topData[] = $row['like_count'] + $row['comment_count'];
}

$commentaireController = new CommentaireController();
$totalComments = $commentaireController->countAll();

// Messages de succès/erreur
$successMessage = '';
$errorMessage = '';
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case '1':
            $successMessage = 'Article créé avec succès!';
            break;
        case '2':
            $successMessage = 'Article mis à jour avec succès!';
            break;
        case '3':
            $successMessage = 'Article supprimé avec succès!';
            break;
    }
}
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case '1':
            $errorMessage = 'Erreur lors de la création de l\'article.';
            break;
        case '2':
            $errorMessage = 'Erreur lors de la mise à jour de l\'article.';
            break;
        case '3':
            $errorMessage = 'Erreur lors de la suppression de l\'article.';
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

    <title>Blog Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <style>
        .table td {
            vertical-align: middle;
        }
        .article-image-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .article-image-thumb:hover {
            transform: scale(1.1);
        }
        .badge {
            padding: 0.5em 0.8em;
            font-size: 0.85em;
            font-weight: 600;
        }
        .status-badge {
            padding: 0.6em 1em;
            font-size: 0.8em;
            font-weight: 600;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }
        .status-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .badge-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            border: none;
        }
        .badge-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            border: none;
        }
        .badge-info {
            background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
            border: none;
        }
        .badge-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            border: none;
        }
        .badge-secondary {
            background: linear-gradient(135deg, #6b7280, #4b5563) !important;
            border: none;
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
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .modal-header .close {
            color: white;
            opacity: 1;
        }
        #articleContent {
            background: #f8f9fc;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #4e73df;
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
                            <h1 class="h3 mb-0 text-gray-800">📊 Dashboard</h1>
                            <small class="text-muted">Gestion des articles et statistiques</small>
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

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Articles Approuvés -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Articles Approuvés</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['approved']; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Articles Brouillons -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Brouillons (Drafts)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['drafts']; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Commentaires -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Commentaires
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalComments; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Abonnés Newsletter -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Abonnés Newsletter</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $subscriberCount; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Row -->
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Gestion des Commentaires</div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                Modérer et gérer tous les commentaires
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="comments_management.php" class="btn btn-info btn-sm">
                                                <i class="fas fa-comments"></i> Gérer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Nouvel Article</div>
                                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                                Créer un nouvel article
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="form_article.php" class="btn btn-success btn-sm">
                                                <i class="fas fa-plus"></i> Créer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <!-- Area Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Top 3 Articles (Interactions)</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-primary"></i> Likes + Comments
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Articles Table -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <div>
                                <h6 class="m-0 font-weight-bold text-primary">📰 Liste des Articles</h6>
                                <small class="text-muted">Gérez et organisez vos articles</small>
                            </div>
                            <a href="form_article.php" class="btn btn-primary btn-sm shadow-sm">
                                <i class="fas fa-plus fa-sm"></i> Nouvel Article
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Image</th>
                                            <th>Titre</th>
                                            <th>Auteur</th>
                                            <th>Date de création</th>
                                            <th class="text-center">Statut</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $articles->fetch(PDO::FETCH_ASSOC)): ?>
                                        <tr>
                                            <td class="text-center font-weight-bold"><?php echo $row['id']; ?></td>
                                            <td class="text-center">
                                                <?php if($row['image']): ?>
                                                        <?php $thumbUrl = '/PeaceConnect/model/uploads/' . rawurlencode($row['image']); ?>
                                                        <img src="<?php echo htmlspecialchars($thumbUrl); ?>" alt="<?php echo htmlspecialchars($row['titre']); ?>" class="article-image-thumb" onerror="this.onerror=null;this.src='vendor/fontawesome-free/svgs/solid/image.svg'">
                                                    <?php else: ?>
                                                    <div class="text-muted"><i class="far fa-image fa-2x"></i></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold text-primary"><?php echo htmlspecialchars($row['titre']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars(substr($row['contenu'], 0, 60)) . '...'; ?></small>
                                            </td>
                                            <td>
                                                <i class="fas fa-user-circle text-primary"></i> 
                                                <?php echo htmlspecialchars($row['auteur']); ?>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="far fa-calendar"></i> <?php echo date('d/m/Y', strtotime($row['date_creation'])); ?><br>
                                                    <i class="far fa-clock"></i> <?php echo date('H:i', strtotime($row['date_creation'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php 
                                                switch(strtolower($row['statut'])) {
                                                    case 'approuve':
                                                        echo '<span class="badge badge-success status-badge"><i class="fas fa-check-circle"></i> Approuvé</span>';
                                                        break;
                                                    case 'brouillon':
                                                        echo '<span class="badge badge-warning status-badge"><i class="fas fa-edit"></i> Brouillon</span>';
                                                        break;
                                                    case 'en_attente':
                                                        echo '<span class="badge badge-info status-badge"><i class="fas fa-clock"></i> En attente</span>';
                                                        break;
                                                    case 'rejete':
                                                        echo '<span class="badge badge-danger status-badge"><i class="fas fa-times-circle"></i> Rejeté</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge badge-secondary status-badge"><i class="fas fa-question-circle"></i> ' . htmlspecialchars($row['statut']) . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-action-group">
                                                    <button class="btn btn-info btn-sm" onclick="viewArticle(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['titre'])); ?>', '<?php echo htmlspecialchars(addslashes($row['auteur'])); ?>', '<?php echo $row['date_creation']; ?>', '<?php echo htmlspecialchars(addslashes($row['contenu'])); ?>', '<?php echo htmlspecialchars(addslashes($row['image'])); ?>')" title="Visualiser" data-toggle="tooltip">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="../FrontOffice/export_pdf_download.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm" target="_blank" title="Télécharger PDF" data-toggle="tooltip">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                    <a href="form_article.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm" title="Modifier" data-toggle="tooltip">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="../../controller/route_article.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet article ?\n\nCette action est irréversible.')" title="Supprimer" data-toggle="tooltip">
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

    <!-- Article View Modal -->
    <div class="modal fade" id="articleModal" tabindex="-1" role="dialog" aria-labelledby="articleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="articleModalLabel">
                        <i class="fas fa-newspaper"></i> Détails de l'article
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div id="articleImageContainer" class="mb-4 text-center">
                        <!-- Image will be inserted here -->
                    </div>
                    <h2 id="articleTitle" class="mb-3 text-primary font-weight-bold"></h2>
                    <div class="d-flex align-items-center mb-4 text-muted">
                        <div class="mr-4">
                            <i class="fas fa-user-circle"></i> <span id="articleAuthor" class="font-weight-bold"></span>
                        </div>
                        <div>
                            <i class="far fa-calendar-alt"></i> <span id="articleDate"></span>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div id="articleContent" style="white-space: pre-wrap; line-height: 1.8; font-size: 1.05em;"></div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Fermer
                    </button>
                    <a id="editArticleBtn" href="#" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier l'article
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
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
    // Initialize DataTable with French language
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "lengthMenu": "Afficher _MENU_ articles par page",
                "zeroRecords": "Aucun article trouvé",
                "info": "Page _PAGE_ sur _PAGES_",
                "infoEmpty": "Aucun article disponible",
                "infoFiltered": "(filtré de _MAX_ articles au total)",
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
    
    // Function to view article in modal
    function viewArticle(id, titre, auteur, date, contenu, image) {
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
        document.getElementById('articleTitle').innerText = titre;
        document.getElementById('articleAuthor').innerText = auteur;
        document.getElementById('articleDate').innerText = formattedDate;
        document.getElementById('articleContent').innerText = contenu;
        
        // Set image if exists (use absolute path and encode filename)
        var imageContainer = document.getElementById('articleImageContainer');
        if (image && image !== '') {
            var imagePath = '/PeaceConnect/model/uploads/' + encodeURIComponent(image);
            imageContainer.innerHTML = '<img src="' + imagePath + '" class="img-fluid rounded shadow" style="max-height: 400px; object-fit: cover; border-radius: 15px !important;" onerror="this.onerror=null;this.src=\'vendor/fontawesome-free/svgs/solid/image.svg\'">';
        } else {
            imageContainer.innerHTML = '';
        }
        
        // Set edit button link
        document.getElementById('editArticleBtn').href = 'form_article.php?id=' + id;
        
        // Show modal with animation
        $('#articleModal').modal('show');
    }
    
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    // Pie Chart Example
    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: <?php echo json_encode($topLabels); ?>,
        datasets: [{
          data: <?php echo json_encode($topData); ?>,
          backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
          hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
          hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
      },
      options: {
        maintainAspectRatio: false,
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          caretPadding: 10,
        },
        legend: {
          display: true,
          position: 'bottom'
        },
        cutoutPercentage: 80,
      },
    });
    </script>

</body>

</html>
