<?php
include(__DIR__ . '/../../config.php');

// Fallback for PHP 5.6
if (!function_exists('random_bytes')) {
    function random_bytes($length = 32) {
        return openssl_random_pseudo_bytes($length);
    }
}

$email = $_POST['email_4pass'];

$token = bin2hex(random_bytes(32));
$token_hash = hash('sha256', $token);
$expiry = date("Y-m-d H:i:s", time() + 1800);

try {
    $db = config::getConnexion(); 

    // First check if email exists
    $check_sql = "SELECT name FROM sign_up WHERE email = ?";
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->execute([$email]);
    
    if ($check_stmt->rowCount() === 0) {
        die("No account found with this email.");
    }

    $sql = "UPDATE sign_up SET reset_token = ?, token_expiry = ? WHERE email = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$token_hash, $expiry, $email]);

    if ($stmt->rowCount() > 0) {
        require('C:/xampp_lite_5_6/www/projetweb/PeaceConnect/view/FrontOffice/mailer.php');

        $mail->addAddress($email);
        $mail->Subject = 'Password Reset Request';

        $mail->Body = <<<END
<html>
<body>
    <h2>Password Reset</h2>
    <p>Click the link below to reset your password:</p>
    <p><a href="http://localhost/PeaceConnect/view/FrontOffice/reset_password.php?token=$token">Reset Password</a></p>
    <p>This link will expire in 30 minutes.</p>
    <p>If you did not request this password reset, please ignore this email.</p>
</body>
</html>
END;

        try {
            $mail->send();
            header("Location:signin.php");
        } catch (Exception $e) {
            echo "Failed to send email. Error: {$mail->ErrorInfo}";
        }

    } else {
        echo "Failed to save reset token.";
    }

} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
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
    <!-- Your form or content here -->
</body>
</html>