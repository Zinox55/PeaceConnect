<?php
session_start();

echo "<h2>Diagnostic Session</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>\$_SESSION['e'] est défini ?</strong> " . (isset($_SESSION['e']) ? "OUI" : "NON") . "</p>";

if (isset($_SESSION['e'])) {
    echo "<p><strong>Valeur de \$_SESSION['e']:</strong> " . $_SESSION['e'] . "</p>";
} else {
    echo "<p style='color: red;'><strong>Session vide - Aucun utilisateur connecté</strong></p>";
}

echo "<hr>";
echo "<p><a href='events.php'>Aller à events.php</a></p>";
echo "<p><a href='signin.php'>Aller à signin.php</a></p>";
echo "<p><a href='index.php'>Retour à home</a></p>";
?>
