<?php
include(__DIR__ . '/../../config.php');

$token = $_POST["token"];
$token_hash = hash("sha256", $token);

try {
    $db = config::getConnexion();

    $sql = "SELECT * FROM sign_up WHERE reset_token = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$token_hash]);
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user === false) {
        die("Token not found");
    }

    if (strtotime($user["token_expiry"]) <= time()) {
        die("Token has expired");
    }

    // Validate password
    if (strlen($_POST["password_reset"]) < 8) {
        die("Password must be at least 8 characters");
    }

    if (!preg_match("/[a-z]/i", $_POST["password_reset"])) {
        die("Password must contain at least one letter");
    }

    if (!preg_match("/[0-9]/", $_POST["password_reset"])) {
        die("Password must contain at least one number");
    }

    if ($_POST["password_reset"] !== $_POST["password_confirmation"]) {
        die("Passwords must match");
    }

    // Store plain text password (REMOVED password_hash)
    $password = $_POST["password_reset"];

    // Update password and clear reset token
    $sql = "UPDATE sign_up 
            SET password = ?,
                reset_token = NULL,
                token_expiry = NULL
            WHERE name = ?";

    $stmt = $db->prepare($sql);
    $stmt->execute([$password, $user["name"]]);

    echo "Password updated successfully. You can now login.";

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>