<?php
include(__DIR__ . '/../../config.php');

$token = $_GET["token"];
$token_hash = hash("sha256", $token);

try {
    $db = config::getConnexion(); // PDO connection

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

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <h1>Reset Password</h1>
    
    <form method="post" action="proccess_reset.php">
        
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        
        <label for="password_reset">New password</label>
        <input type="password" id="password_reset" name="password_reset" required>
        
        <label for="password_confirmation">Repeat password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>
        
        <button>Send</button>
    </form>

</body>
</html>