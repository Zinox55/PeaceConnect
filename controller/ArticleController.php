<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../Model/Article.php';

class ArticleController {
    private $db;
    private $article;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
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
                $target_dir = "../Uploads/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $this->article->image = $_FILES["image"]["name"];
                }
            } else {
                $this->article->image = "";
            }

            if ($this->article->create()) {
                header("Location: ../View/back/dashboard.php");
            } else {
                echo "Unable to create article.";
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
                $target_dir = "../Uploads/";
                $target_file = $target_dir . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $this->article->image = $_FILES["image"]["name"];
                }
            } else {
                // Keep existing image if not updated
                $this->article->image = $_POST['existing_image'];
            }

            if ($this->article->update()) {
                header("Location: ../View/back/dashboard.php");
            } else {
                echo "Unable to update article.";
            }
        }
    }

    public function delete($id) {
        $this->article->id = $id;
        if ($this->article->delete()) {
            header("Location: ../View/back/dashboard.php");
        } else {
            echo "Unable to delete article.";
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
}
?>
