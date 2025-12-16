<?php
// Controller: CommentaireController
// Gère la logique métier des commentaires

include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/Commentaire.php';

class CommentaireController {
    private $db;
    private $commentaire;

    public function __construct() {
        // Use existing PDO connection from app config
        $this->db = config::getConnexion();
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

            if ($this->commentaire->create()) {
                // Redirect back to the article page
                header("Location: ../view/FrontOffice/article_detail.php?id=" . $_POST['article_id']);
                exit();
            } else {
                echo "Unable to create comment.";
            }
        }
    }
    
    public function countAll() {
        return $this->commentaire->countAll();
    }

    public function edit($id) {
        $this->commentaire->id = $id;
        return $this->commentaire->readOne();
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->commentaire->id = $_POST['id'];
            $this->commentaire->auteur = $_POST['auteur'];
            $this->commentaire->contenu = $_POST['contenu'];

            if ($this->commentaire->update()) {
                header("Location: ../view/BackOffice/comments_management.php?success=2");
                exit();
            } else {
                header("Location: ../view/BackOffice/edit_comment.php?id=" . $_POST['id'] . "&error=2");
                exit();
            }
        }
    }

    public function delete($id) {
        $this->commentaire->id = $id;
        if ($this->commentaire->delete()) {
            header("Location: ../view/BackOffice/comments_management.php?success=3");
            exit();
        } else {
            header("Location: ../view/BackOffice/comments_management.php?error=3");
            exit();
        }
    }
}
?>
