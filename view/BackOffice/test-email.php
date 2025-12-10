<?php
/**
 * Test Email Configuration for PeaceConnect
 * This file tests if PHP mail() function is properly configured
 */

echo "<h1>PeaceConnect - Email Configuration Test</h1>";

// Test basic PHP mail configuration
echo "<h2>1. PHP Mail Configuration</h2>";
echo "<pre>";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "smtp_port: " . ini_get('smtp_port') . "\n";
echo "sendmail_from: " . ini_get('sendmail_from') . "\n";
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "</pre>";

// Test sending a simple email
echo "<h2>2. Sending Test Email</h2>";

$to = "ghribiranim6@gmail.com";
$subject = "Test Email from PeaceConnect";
$message = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
        .header { background: #4e73df; color: white; padding: 20px; text-align: center; }
        .content { background: white; padding: 30px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>✅ Email Test Successful!</h1>
        </div>
        <div class='content'>
            <p>Hello,</p>
            <p>This is a test email from PeaceConnect to verify email configuration.</p>
            <p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>
            <p>If you received this email, the mail configuration is working correctly!</p>
        </div>
    </div>
</body>
</html>
";

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: PeaceConnect <noreply@peaceconnect.org>" . "\r\n";

$result = mail($to, $subject, $message, $headers);

if ($result) {
    echo "<p style='color: green; font-weight: bold;'>✅ Email sent successfully to $to</p>";
    echo "<p>Check your inbox (and spam folder) for the test email.</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Failed to send email</p>";
    echo "<p>Please check the following:</p>";
    echo "<ul>";
    echo "<li>SMTP configuration in php.ini</li>";
    echo "<li>sendmail configuration (if using sendmail)</li>";
    echo "<li>Error logs in C:\\xampp\\sendmail\\error.log</li>";
    echo "<li>Firewall settings</li>";
    echo "</ul>";
}

echo "<h2>3. Configuration Instructions</h2>";
echo "<p>To configure email sending in XAMPP:</p>";
echo "<ol>";
echo "<li>Edit <code>C:\\xampp\\php\\php.ini</code>:</li>";
echo "<pre>
[mail function]
SMTP=smtp.gmail.com
smtp_port=587
sendmail_from=your-email@gmail.com
sendmail_path=\"C:\\xampp\\sendmail\\sendmail.exe -t\"
</pre>";
echo "<li>Edit <code>C:\\xampp\\sendmail\\sendmail.ini</code>:</li>";
echo "<pre>
[sendmail]
smtp_server=smtp.gmail.com
smtp_port=587
auth_username=your-email@gmail.com
auth_password=YOUR_APP_PASSWORD
force_sender=your-email@gmail.com
</pre>";
echo "<li>Create Gmail App Password:</li>";
echo "<ul>";
echo "<li>Go to Google Account Security</li>";
echo "<li>Enable 2-Step Verification</li>";
echo "<li>Create App Password</li>";
echo "<li>Use that password in sendmail.ini</li>";
echo "</ul>";
echo "<li>Restart Apache in XAMPP Control Panel</li>";
echo "</ol>";

echo "<h2>4. Alternative: Use Mailtrap for Testing</h2>";
echo "<p>For development/testing without real email sending:</p>";
echo "<ul>";
echo "<li>Sign up at <a href='https://mailtrap.io' target='_blank'>mailtrap.io</a></li>";
echo "<li>Get SMTP credentials from your inbox</li>";
echo "<li>Configure sendmail.ini with Mailtrap credentials</li>";
echo "<li>All emails will be caught in Mailtrap (not sent to real addresses)</li>";
echo "</ul>";

echo "<hr>";

echo "<div style='margin-top: 30px; padding: 20px; background: #d4edda; border-left: 4px solid #28a745; border-radius: 5px;'>";
echo "<h3 style='color: #28a745; margin-bottom: 15px;'>🚀 Configuration Rapide</h3>";
echo "<p style='margin-bottom: 15px;'>Pour configurer l'envoi d'emails en quelques minutes :</p>";
echo "<a href='setup-email-guide.html' style='display: inline-block; background: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px;'>📖 Guide de Configuration Visuel</a>";
echo "<a href='../SOLUTION_RAPIDE_EMAIL.md' style='display: inline-block; background: #4e73df; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>📄 Documentation Complète</a>";
echo "</div>";

echo "<hr>";
echo "<p><a href='../FrontOffice/index.php'>← Back to Donation Form</a> | <a href='index.php'>Dashboard</a></p>";
?>
