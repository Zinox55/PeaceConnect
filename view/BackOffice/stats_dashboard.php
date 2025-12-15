<?php
// Vérifier si les données existent
if (!isset($data)) {
    die("Données non disponibles");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Statistiques - PeaceConnect Admin</title>
    <link href="../FrontOffice/assets_events/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../FrontOffice/assets_events/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include 'sidebar.html'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include 'topbar.html'; ?>

                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">📊 Tableau de Bord Statistiques</h1>

                    <!-- STATISTIQUES GÉNÉRALES -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Événements Total</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $data['generalStats']['total_events'] ?? 0 ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Inscriptions Total</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $data['generalStats']['total_inscriptions'] ?? 0 ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Événements à Venir</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $data['generalStats']['events_a_venir'] ?? 0 ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Moyenne Inscriptions/Event</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= round($data['generalStats']['moyenne_inscriptions_par_event'] ?? 0, 1) ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GRAPHIQUES -->
                    <div class="row">
                        <!-- TOP 5 ÉVÉNEMENTS (CAMEMBERT) -->
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Répartition des Inscriptions (Top 5)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie">
                                        <canvas id="pieChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- INSCRIPTIONS PAR MOIS -->
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Inscriptions par Mois (12 derniers mois)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-line">
                                        <canvas id="monthlyChart" width="400" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOP 5 ÉVÉNEMENTS TABLEAU -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Top 5 Événements les Plus Populaires</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Événement</th>
                                                    <th>Nombre d'Inscriptions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(empty($data['topEvents'])): ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center">Aucun événement</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach($data['topEvents'] as $index => $event): ?>
                                                    <tr>
                                                        <td><?= $index + 1 ?></td>
                                                        <td><?= htmlspecialchars($event['titre']) ?></td>
                                                        <td>
                                                            <div class="progress">
                                                                <div class="progress-bar bg-success" 
                                                                     role="progressbar" 
                                                                     style="width: <?= min(($event['nb_inscriptions'] / max($data['topEvents'][0]['nb_inscriptions'], 1)) * 100, 100) ?>%"
                                                                     aria-valuenow="<?= $event['nb_inscriptions'] ?>" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="<?= max(array_column($data['topEvents'], 'nb_inscriptions')) ?>">
                                                                    <?= $event['nb_inscriptions'] ?> inscriptions
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../FrontOffice/assets_events/vendor/jquery/jquery.min.js"></script>
    <script src="../FrontOffice/assets_events/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../FrontOffice/assets_events/js/sb-admin-2.min.js"></script>

    <script>
        // Données pour les graphiques
        const topEventsData = <?= json_encode($data['topEvents']) ?>;
        const monthlyData = <?= json_encode($data['inscriptionsByMonth']) ?>;

        // CAMEMBERT - Top 5 Événements
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: topEventsData.map(event => event.titre.substring(0, 15) + (event.titre.length > 15 ? '...' : '')),
                datasets: [{
                    data: topEventsData.map(event => event.nb_inscriptions),
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} inscriptions (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Graphique Inscriptions par Mois
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => {
                    const [year, month] = item.mois.split('-');
                    return `${month}/${year}`;
                }),
                datasets: [{
                    label: 'Inscriptions',
                    data: monthlyData.map(item => item.nb_inscriptions),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
