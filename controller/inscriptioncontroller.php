<?php
// Activer les erreurs pour debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // CHEMIN ABSOLU POUR ÊTRE SÛR
    require_once __DIR__ . '/../model/InscriptionModel.php';
    require_once __DIR__ . '/../model/Mailer_events.php';
} catch (Exception $e) {
    die("Erreur de chargement: " . $e->getMessage());
}

class InscriptionController {
    private $model;
    private $mailer;
    
    public function __construct() {
        try {
            $this->model = new InscriptionModel();
            $this->mailer = new Mailer();
        } catch (Exception $e) {
            die("Erreur d'initialisation: " . $e->getMessage());
        }
    }
    
    // TRAITEMENT FORMULAIRE FRONTOFFICE
    public function processInscription() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            $evenement = trim($_POST['evenement'] ?? '');
            
            if (empty($nom) || empty($email) || empty($evenement)) {
                echo "error: Tous les champs obligatoires doivent être remplis";
                exit;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "error: L'adresse email n'est pas valide";
                exit;
            }
            
            if (!empty($telephone) && !preg_match('/^\d{8}$/', $telephone)) {
                echo "error: Le numéro de téléphone doit contenir 8 chiffres";
                exit;
            }
            
            try {
                // Créer l'inscription (retourne le token)
                $token = $this->model->createInscription($nom, $email, $telephone, $evenement);
                
                if ($token) {
                    // Envoyer l'email de vérification
                    if ($this->mailer->sendVerificationEmail($email, $nom, $token)) {
                        echo "success: Veuillez vérifier votre email pour confirmer votre inscription";
                    } else {
                        echo "error: Inscription créée mais l'email n'a pas pu être envoyée. Vérifiez le journal mail_error.log";
                    }
                } else {
                    echo "error: Une erreur est survenue lors de l'inscription";
                }
                
            } catch (Exception $e) {
                echo "error: " . $e->getMessage();
            }
        } else {
            echo "error: Méthode non autorisée";
        }
    }
    
    // ... (le reste du code reste identique)
}

// ROUTAGE AUTOMATIQUE
if (isset($_GET['action'])) {
    $controller = new InscriptionController();
    
    switch ($_GET['action']) {
        case 'create':
            $controller->create();
            break;
        case 'edit':
            $controller->edit($_GET['id']);
            break;
        case 'delete':
            $controller->delete($_GET['id']);
            break;
        case 'process':
            $controller->processInscription();
            break;
        default:
            $controller->index();
    }
}
?>
