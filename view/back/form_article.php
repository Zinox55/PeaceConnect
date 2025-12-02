<?php
include_once __DIR__ . '/../../Controller/ArticleController.php';

$articleController = new ArticleController();
$article = null;
$isEditing = false;

if (isset($_GET['id'])) {
    $isEditing = true;
    $article = $articleController->edit($_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $isEditing ? 'Modifier Article' : 'Nouvel Article'; ?></title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-blog"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Blog Admin</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <h1 class="h3 mb-0 text-gray-800"><?php echo $isEditing ? 'Modifier Article' : 'Nouvel Article'; ?></h1>
                </nav>

                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <form action="../../Controller/route_article.php" method="POST" enctype="multipart/form-data" novalidate onsubmit="return validateArticleForm()">
                                <input type="hidden" name="action" value="<?php echo $isEditing ? 'update' : 'create'; ?>">
                                <?php if ($isEditing): ?>
                                    <input type="hidden" name="id" value="<?php echo $article->id; ?>">
                                    <input type="hidden" name="existing_image" value="<?php echo $article->image; ?>">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="titre">Titre</label>
                                    <input type="text" class="form-control" id="titre" name="titre" value="<?php echo $isEditing ? $article->titre : ''; ?>">
                                    <small id="titreError" class="text-danger"></small>
                                </div>

                                <div class="form-group">
                                    <label for="auteur">Auteur</label>
                                    <input type="text" class="form-control" id="auteur" name="auteur" value="<?php echo $isEditing ? $article->auteur : ''; ?>">
                                    <small id="auteurError" class="text-danger"></small>
                                </div>

                                <div class="form-group">
                                    <label for="contenu">Contenu</label>
                                    <textarea class="form-control" id="contenu" name="contenu" rows="5"><?php echo $isEditing ? $article->contenu : ''; ?></textarea>
                                    <small id="contenuError" class="text-danger"></small>
                                </div>

                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <input type="file" class="form-control-file" id="image" name="image">
                                    <?php if ($isEditing && $article->image): ?>
                                        <div class="mt-2">
                                            <img src="../../Uploads/<?php echo $article->image; ?>" alt="Current Image" style="max-width: 200px;">
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label>Statut</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="statut" id="statutDraft" value="brouillon" <?php echo (!$isEditing || $article->statut == 'brouillon') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="statutDraft">
                                            Brouillon (Draft)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="statut" id="statutApproved" value="approuve" <?php echo ($isEditing && $article->statut == 'approuve') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="statutApproved">
                                            Publier (Approuvé)
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                <a href="dashboard.php" class="btn btn-secondary">Annuler</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function validateArticleForm() {
        var titre = document.getElementById('titre').value;
        var auteur = document.getElementById('auteur').value;
        var contenu = document.getElementById('contenu').value;
        var isValid = true;

        if (titre.trim() == "") {
            document.getElementById('titreError').innerText = "Titre est requis";
            isValid = false;
        } else {
            document.getElementById('titreError').innerText = "";
        }

        if (auteur.trim() == "") {
            document.getElementById('auteurError').innerText = "Auteur est requis";
            isValid = false;
        } else {
            document.getElementById('auteurError').innerText = "";
        }

        if (contenu.trim() == "") {
            document.getElementById('contenuError').innerText = "Contenu est requis";
            isValid = false;
        } else {
            document.getElementById('contenuError').innerText = "";
        }

        return isValid;
    }
    </script>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>
</html>
