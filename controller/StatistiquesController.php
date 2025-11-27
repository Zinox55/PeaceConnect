<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../model/Commande.php';
require_once __DIR__ . '/../model/Produit.php';
require_once __DIR__ . '/../model/Panier.php';

/**
 * Controller pour les statistiques et analyses
 * Utilise les nouvelles méthodes avec jointures
 */

try {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        // ========================================
        // STATISTIQUES DES COMMANDES
        // ========================================
        
        case 'getStatistiquesCommandes':
            $commande = new Commande();
            $stats = $commande->getStatistiquesGlobales();
            echo json_encode([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistiques récupérées'
            ]);
            break;
            
        case 'getCommandesParStatut':
            $statut = $_GET['statut'] ?? 'en_attente';
            $commande = new Commande();
            $commandes = $commande->lireParStatut($statut);
            echo json_encode([
                'success' => true,
                'data' => $commandes,
                'message' => "Commandes '$statut' récupérées"
            ]);
            break;
            
        case 'getCommandesAvecDetails':
            $commande = new Commande();
            $commandes = $commande->lireToutAvecDetails();
            echo json_encode([
                'success' => true,
                'data' => $commandes,
                'message' => 'Commandes avec détails récupérées'
            ]);
            break;
            
        case 'getCommandeComplete':
            $numero = $_GET['numero'] ?? '';
            if (empty($numero)) {
                throw new Exception("Numéro de commande requis");
            }
            $commande = new Commande();
            $data = $commande->lireCommandeComplete($numero);
            if (!$data) {
                throw new Exception("Commande non trouvée");
            }
            echo json_encode([
                'success' => true,
                'data' => $data,
                'message' => 'Commande complète récupérée'
            ]);
            break;
            
        case 'getResumeCommande':
            $id = $_GET['id'] ?? 0;
            if (!$id) {
                throw new Exception("ID de commande requis");
            }
            $commande = new Commande();
            $resume = $commande->getResume($id);
            echo json_encode([
                'success' => true,
                'data' => $resume,
                'message' => 'Résumé récupéré'
            ]);
            break;
            
        case 'getCommandesClient':
            $email = $_GET['email'] ?? '';
            if (empty($email)) {
                throw new Exception("Email requis");
            }
            $commande = new Commande();
            $commandes = $commande->lireParClient($email);
            echo json_encode([
                'success' => true,
                'data' => $commandes,
                'message' => 'Commandes du client récupérées'
            ]);
            break;
        
        // ========================================
        // STATISTIQUES DES PRODUITS
        // ========================================
        
        case 'getTousProduits':
            $produit = new Produit();
            $produits = $produit->getAllAvecStatistiques();
            echo json_encode([
                'success' => true,
                'data' => $produits,
                'message' => 'Produits avec statistiques récupérés'
            ]);
            break;
            
        case 'getTopProduits':
            $limit = intval($_GET['limit'] ?? 5);
            $produit = new Produit();
            $topVentes = $produit->getTopVentes($limit);
            echo json_encode([
                'success' => true,
                'data' => $topVentes,
                'message' => "Top $limit produits récupérés"
            ]);
            break;
            
        case 'getStatistiquesProduit':
            $id = $_GET['id'] ?? 0;
            if (!$id) {
                throw new Exception("ID de produit requis");
            }
            $produit = new Produit();
            $stats = $produit->getStatistiques($id);
            echo json_encode([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistiques du produit récupérées'
            ]);
            break;
            
        case 'getProduitsNonCommandes':
            $produit = new Produit();
            $produits = $produit->getNonCommandes();
            echo json_encode([
                'success' => true,
                'data' => $produits,
                'message' => 'Produits non commandés récupérés'
            ]);
            break;
        
        // ========================================
        // STATISTIQUES DU PANIER
        // ========================================
        
        case 'getPanierAvecStock':
            $panier = new Panier();
            $articles = $panier->lireToutAvecStock();
            echo json_encode([
                'success' => true,
                'data' => $articles,
                'message' => 'Panier avec stock récupéré'
            ]);
            break;
            
        case 'verifierDisponibilite':
            $panier = new Panier();
            $disponibilite = $panier->verifierDisponibilite();
            echo json_encode([
                'success' => true,
                'data' => $disponibilite,
                'message' => 'Disponibilité vérifiée'
            ]);
            break;
            
        case 'getPanierDetailsComplets':
            $panier = new Panier();
            $details = $panier->getDetailsComplets();
            echo json_encode([
                'success' => true,
                'data' => $details,
                'message' => 'Détails complets du panier récupérés'
            ]);
            break;
        
        // ========================================
        // RAPPORTS ET ANALYSES
        // ========================================
        
        case 'getRapportComplet':
            $commande = new Commande();
            $produit = new Produit();
            
            $rapport = [
                'commandes' => [
                    'total' => 0,
                    'par_statut' => $commande->getStatistiquesGlobales()
                ],
                'produits' => [
                    'top_ventes' => $produit->getTopVentes(5),
                    'non_commandes' => $produit->getNonCommandes(),
                    'tous' => $produit->getAllAvecStatistiques()
                ],
                'date_generation' => date('Y-m-d H:i:s')
            ];
            
            // Calculer le total des commandes
            foreach ($rapport['commandes']['par_statut'] as $stat) {
                $rapport['commandes']['total'] += $stat['nombre_commandes'];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $rapport,
                'message' => 'Rapport complet généré'
            ]);
            break;
            
        case 'getTableauDeBord':
            $commande = new Commande();
            $produit = new Produit();
            $panier = new Panier();
            
            $dashboard = [
                'commandes_stats' => $commande->getStatistiquesGlobales(),
                'top_5_produits' => $produit->getTopVentes(5),
                'produits_rupture' => [],
                'panier_actif' => [
                    'total' => $panier->calculerTotal(),
                    'nb_articles' => $panier->compterArticles()
                ]
            ];
            
            // Récupérer les produits en rupture de stock
            $tous_produits = $produit->getAllAvecStatistiques();
            foreach ($tous_produits as $p) {
                if ($p['etat_stock'] === 'Rupture') {
                    $dashboard['produits_rupture'][] = $p;
                }
            }
            
            echo json_encode([
                'success' => true,
                'data' => $dashboard,
                'message' => 'Tableau de bord chargé'
            ]);
            break;
        
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Action non reconnue',
                'actions_disponibles' => [
                    'Commandes' => [
                        'getStatistiquesCommandes',
                        'getCommandesParStatut',
                        'getCommandesAvecDetails',
                        'getCommandeComplete',
                        'getResumeCommande',
                        'getCommandesClient'
                    ],
                    'Produits' => [
                        'getTousProduits',
                        'getTopProduits',
                        'getStatistiquesProduit',
                        'getProduitsNonCommandes'
                    ],
                    'Panier' => [
                        'getPanierAvecStock',
                        'verifierDisponibilite',
                        'getPanierDetailsComplets'
                    ],
                    'Rapports' => [
                        'getRapportComplet',
                        'getTableauDeBord'
                    ]
                ]
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
