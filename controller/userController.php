<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../Model/Book.php');

class UserControllerController {
    public function addBook(User $user) {
        $sql = "INSERT INTO sign_up VALUES (NULL, :name,:email,:password,:verify_password,:role)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name' => $book->getTitle(),
                'email' => $book->getAuthor(),
                'password' => $book->getPublicationDate() ? $book->getPublicationDate()->format('Y-m-d') : null,
                'verify_password' => $book->getLangue(),
                'role' => $book->getStatus()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }


   
}
?>