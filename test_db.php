<?php
include_once 'config.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if($db) {
        echo "✅ Connexion à la base de données réussie !<br>";
        
        // Test de lecture des tables
        $stmt = $db->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "📊 Tables trouvées : " . implode(', ', $tables);
    }
} catch(PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>