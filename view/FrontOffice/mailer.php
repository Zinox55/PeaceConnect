<?php
// mailer.php for XAMPP Lite 5.6
// Make sure PHPMailer library is in the right folder
// Example path: C:/xampp_lite_5_6/www/projetweb/PeaceConnect/vendor/PHPMailer/src/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer classes
require 'C:/xampp_lite_5_6/www/projetweb/PeaceConnect/controller/PHPMailer/src/Exception.php';
require 'C:/xampp_lite_5_6/www/projetweb/PeaceConnect/controller/PHPMailer/src/PHPMailer.php';
require 'C:/xampp_lite_5_6/www/projetweb/PeaceConnect/controller/PHPMailer/src/SMTP.php';

// Create the PHPMailer object
$mail = new PHPMailer(true);

// SMTP configuration
$mail->isSMTP();
$mail->Host       = 'smtp.gmail.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'obbaachref178@gmail.com';   // your Gmail
$mail->Password   = 'byus crwe aocf dmqr';     // NOT your Gmail password!
$mail->SMTPSecure = 'ssl';
$mail->Port       = 465;


// Default from email
$mail->setFrom('noreply@example.com', 'Your Website');
$mail->isHTML(true); // Send email as HTML
?>
