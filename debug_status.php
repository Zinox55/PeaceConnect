<?php
include_once 'config.php';
include_once 'Model/Article.php';

$database = new Database();
$db = $database->getConnection();

echo "<h2>Database Debug</h2>";

// Check unique statuses
$query = "SELECT DISTINCT statut FROM articles";
$stmt = $db->prepare($query);
$stmt->execute();
$statuses = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "<h3>Unique Statuses in DB:</h3>";
echo "<ul>";
foreach ($statuses as $status) {
    echo "<li>'" . $status . "' (Length: " . strlen($status) . ")</li>";
}
echo "</ul>";

// Check counts manually
$article = new Article($db);
echo "<h3>Model Counts:</h3>";
echo "Approuve: " . $article->countByStatus('approuve') . "<br>";
echo "Brouillon: " . $article->countByStatus('brouillon') . "<br>";

// Check recent articles
echo "<h3>Recent Articles:</h3>";
$stmt = $db->query("SELECT id, titre, statut FROM articles ORDER BY id DESC LIMIT 5");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " | Title: " . $row['titre'] . " | Status: '" . $row['statut'] . "'<br>";
}
?>
