<?php
// Controller: ArticleController
// Gère la logique métier des articles

include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Article.php';

class ArticleController {
    private $db;
    private $article;

    public function __construct() {
<<<<<<< HEAD
        // Use existing config class to obtain PDO connection
        require_once __DIR__ . '/../config.php';
        $this->db = \config::getConnexion();
        if (!$this->db) {
            throw new Exception('Database connection not available in ArticleController');
        }
=======
        // Use existing app config PDO connection (no Database class in project)
        $this->db = config::getConnexion();
>>>>>>> 6245d086736825f2cb2f6a6b2578b13165bd9af8
        $this->article = new Article($this->db);
    }

    public function index() {
        return $this->article->readAll();
    }

    public function indexFront() {
        return $this->article->readAllApproved();
    }

    public function create() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->article->titre = trim($_POST['titre']);
            $this->article->contenu = trim($_POST['contenu']);
            $this->article->auteur = trim($_POST['auteur']); // Should ideally come from session
            $this->article->date_creation = date('Y-m-d H:i:s');
            $this->article->statut = trim($_POST['statut']); // 'brouillon' or 'approuve'

            // Image Upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = __DIR__ . "/../model/uploads/";
                $image_name = time() . '_' . basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $image_name;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $this->article->image = $image_name;
                }
            } else {
                $this->article->image = "";
            }

            if ($this->article->create()) {
                // Envoyer notification aux abonnés si l'article est approuvé
                if (strtolower(trim($_POST['statut'])) == 'approuve') {
                    $this->notifySubscribersNewArticle(
                        $this->db->lastInsertId(),
                        $this->article->titre,
                        $this->article->contenu,
                        $this->article->auteur
                    );
                }
                
                header("Location: ../view/BackOffice/dashboard_ichrak.php?success=1");
                exit();
            } else {
                header("Location: ../view/BackOffice/form_article.php?error=1");
                exit();
            }
        }
    }

    public function edit($id) {
        $this->article->id = $id;
        $this->article->readOne();
        return $this->article;
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->article->id = $_POST['id'];
            $this->article->titre = trim($_POST['titre']);
            $this->article->contenu = trim($_POST['contenu']);
            $this->article->auteur = trim($_POST['auteur']);
            $this->article->statut = trim($_POST['statut']);

            // Image Upload (only if new image is selected)
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $target_dir = __DIR__ . "/../model/uploads/";
                $image_name = time() . '_' . basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $image_name;
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $this->article->image = $image_name;
                }
            } else {
                // Keep existing image if not updated
                $this->article->image = $_POST['existing_image'];
            }

            if ($this->article->update()) {
                header("Location: ../view/BackOffice/dashboard_ichrak.php?success=2");
                exit();
            } else {
                header("Location: ../view/BackOffice/form_article.php?id=" . $_POST['id'] . "&error=2");
                exit();
            }
        }
    }

    public function delete($id) {
        $this->article->id = $id;
        if ($this->article->delete()) {
            header("Location: ../view/BackOffice/dashboard_ichrak.php?success=3");
            exit();
        } else {
            header("Location: ../view/BackOffice/dashboard_ichrak.php?error=3");
            exit();
        }
    }

    public function getStats() {
        $approved = $this->article->countByStatus('approuve');
        $drafts = $this->article->countByStatus('brouillon');
        return ['approved' => $approved, 'drafts' => $drafts];
    }

    public function getTopPosts($limit = 3) {
        return $this->article->getTopPosts($limit);
    }

    // Fonction pour notifier les abonnés d'un nouvel article
    private function notifySubscribersNewArticle($articleId, $titre, $contenu, $auteur) {
        include_once __DIR__ . '/NewsletterController.php';
        $newsletterController = new NewsletterController();
        
        // Créer un extrait de l'article (150 caractères)
        $excerpt = strlen($contenu) > 150 ? substr($contenu, 0, 150) . '...' : $contenu;
        
        // Envoyer les notifications
        $newsletterController->notifySubscribers($articleId, $titre, $excerpt, $auteur);
    }
}
?>
