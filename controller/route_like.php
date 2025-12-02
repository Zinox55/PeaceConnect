<?php
include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../Model/Like.php';

$database = new Database();
$db = $database->getConnection();
$like = new Like($db);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['article_id'])) {
    $like->article_id = $_POST['article_id'];
    $like->ip_address = $_SERVER['REMOTE_ADDR'];

    if ($like->addLike()) {
        // Success
        header("Location: ../View/front/article_detail.php?id=" . $_POST['article_id']);
    } else {
        // Already liked or error
        header("Location: ../View/front/article_detail.php?id=" . $_POST['article_id'] . "&error=already_liked");
    }
}
?>
