<?php
include(__DIR__ . '/../../config.php');

// Fallback for PHP 5.6 (your XAMPP Lite)
if (!function_exists('random_bytes')) {
    function random_bytes($length = 32) {
        return openssl_random_pseudo_bytes($length);
    }
}

$email =$_POST['email_4pass'];

$token = bin2hex(random_bytes(32));
$token_hash = hash('sha256', $token);
$expiry = date("Y-m-d H:i:s", time() + 1800);

$db = config::getConnexion();   // Your connection works perfectly

$sql = "UPDATE sign_up SET reset_token = ?, token_expiry = ? WHERE email = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$token_hash, $expiry, $email]);
?>