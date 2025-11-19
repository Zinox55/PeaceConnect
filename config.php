<?php
// config.php
function getPDO() {
    try {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=voluenteer_db;charset=utf8mb4",
            "root",
            ""
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (Exception $e) {
        die("Erreur connexion DB : " . $e->getMessage());
    }
}
?>