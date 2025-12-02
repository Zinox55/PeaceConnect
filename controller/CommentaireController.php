<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../Model/Commentaire.php';

class CommentaireController {
    private $db;
    private $commentaire;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->commentaire = new Commentaire($this->db);
    }

    public function index() {
        return $this->commentaire->readAll();
    }

    public function getByArticle($articleId) {
        return $this->commentaire->readByArticle($articleId);
    }

    public function create() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->commentaire->article_id = $_POST['article_id'];
            $this->commentaire->auteur = $_POST['auteur'];
            $this->commentaire->contenu = $_POST['contenu'];
            $this->commentaire->date_creation = date('Y-m-d H:i:s');
            $this->commentaire->statut = 'approuve'; // Auto approve for now or 'pending'

            if ($this->commentaire->create()) {
                // Redirect back to the article page
                header("Location: ../View/front/article_detail.php?id=" . $_POST['article_id']);
            } else {
                echo "Unable to create comment.";
            }
        }
    }
    
    public function countAll() {
        return $this->commentaire->countAll();
    }
}
?>
