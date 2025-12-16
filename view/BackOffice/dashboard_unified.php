<?php
// Dashboard unifié utilisant le même layout SB Admin 2
?>
<!DOCTYPE html>
<html lang="fr">
<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>Dashboard Unifié - PeaceConnect Admin</title>
		<!-- mêmes assets que les autres dashboards -->
		<link href="../FrontOffice/assets_events/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
		<link href="../FrontOffice/assets_events/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
		<div id="wrapper">
				<?php include 'sidebar.html'; ?>

				<div id="content-wrapper" class="d-flex flex-column">
						<div id="content">
								<?php include 'topbar.html'; ?>

								<div class="container-fluid">
										<h1 class="h3 mb-4 text-gray-800">Dashboard Unifié</h1>

										<!-- Hub: accès par chemins (aucun aperçu inline) -->

										<div class="row">
												<div class="col-xl-6 col-md-6 mb-4">
													<div class="card shadow h-100">
														<div class="card-body">
															<h5 class="card-title"><i class="fas fa-users"></i> Utilisateurs</h5>
															<p class="card-text">Gérez les utilisateurs et leurs rôles.</p>
														<a class="btn btn-primary" href="/PeaceConnect/view/BackOffice/blank.php">Ouvrir Users</a>
														</div>
													</div>
												</div>
												<div class="col-xl-6 col-md-6 mb-4">
														<div class="card shadow h-100">
																<div class="card-body">
																		<h5 class="card-title"><i class="fas fa-calendar-alt"></i> Events & Inscriptions</h5>
																		<p class="card-text">Accédez au tableau avancé des événements et inscriptions.</p>
														<a class="btn btn-primary" href="/PeaceConnect/view/BackOffice/index_events.php">Ouvrir index_events</a>
																</div>
														</div>
												</div>
												<div class="col-xl-6 col-md-6 mb-4">
														<div class="card shadow h-100">
																<div class="card-body">
																		<h5 class="card-title"><i class="fas fa-newspaper"></i> Articles & Blog</h5>
																		<p class="card-text">Gérez les articles, commentaires, et statistiques du blog.</p>
															<a class="btn btn-primary" href="/PeaceConnect/view/BackOffice/dashboard_ichrak.php">Ouvrir dashboard_ichrak</a>
																</div>
														</div>
												</div>
												<div class="col-xl-6 col-md-6 mb-4">
														<div class="card shadow h-100">
																<div class="card-body">
																		<h5 class="card-title"><i class="fas fa-box"></i> Produits & Commandes</h5>
																		<p class="card-text">Gestion des produits et des commandes.</p>
															<a class="btn btn-primary" href="/PeaceConnect/view/BackOffice/dashboard.html">Ouvrir Produits & Commandes</a>
																</div>
														</div>
												</div>
												<div class="col-xl-6 col-md-6 mb-4">
														<div class="card shadow h-100">
																<div class="card-body">
																		<h5 class="card-title"><i class="fas fa-hand-holding-heart"></i> Dons & Causes</h5>
																		<p class="card-text">Suivez et administrez les dons et les causes.</p>
															<a class="btn btn-primary" href="/PeaceConnect/view/BackOffice/indexRanim.php">Ouvrir Dons & Causes</a>
																</div>
														</div>
												</div>
												<div class="col-xl-6 col-md-6 mb-4">
														<div class="card shadow h-100">
																<div class="card-body">
																		<h5 class="card-title"><i class="fas fa-chart-line"></i> Statistiques</h5>
																		<p class="card-text">Visualisez les graphiques et indicateurs clés.</p>
															<a class="btn btn-primary" href="/PeaceConnect/view/BackOffice/stats_dashboard.php">Ouvrir stats_dashboard</a>
																</div>
														</div>
												</div>
										</div>
								</div>
						</div>
				</div>
		</div>

		<!-- JS mêmes assets -->
		<script src="../FrontOffice/assets_events/vendor/jquery/jquery.min.js"></script>
		<script src="../FrontOffice/assets_events/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
		<script src="../FrontOffice/assets_events/vendor/jquery-easing/jquery.easing.min.js"></script>
		<script src="../FrontOffice/assets_events/js/sb-admin-2.min.js"></script>
</body>
</html>
