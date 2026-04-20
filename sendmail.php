<?php
ob_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Synchronize HTML 'phone' or 'mobile' with PHP variables
    $name = htmlspecialchars($_POST['name'] ?? 'N/A');
    $email = htmlspecialchars($_POST['email'] ?? 'N/A');
    $phone = htmlspecialchars($_POST['phone'] ?? $_POST['mobile'] ?? 'N/A');

    // Enhanced Diagnostic Logging
    $log_entry = "[" . date('Y-m-d H:i:s') . "] START LEAD SUBMISSION\n";
    $log_entry .= "RAW POST: " . print_r($_POST, true) . "\n";
    $log_entry .= "Parsed - Name: $name | Email: $email | Phone: $phone\n";
    file_put_contents("debug-log.txt", $log_entry, FILE_APPEND);

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'rock83694@gmail.com';
        $mail->Password = 'eigvmkokcvihyboz';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Sender & Recipient set
        $mail->setFrom('rock83694@gmail.com', 'L&T Crestoria Lead');

        // Clear all before adding to prevent duplicates or legacy injections
        $mail->clearAddresses();
        $mail->addAddress('Leads.kaashniproptec@gmail.com');
        $mail->addAddress('thegrowthmonks@gmail.com');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Lead: L&T Crestoria Estate Panvel';
        $mail->Body = "
            <div style='font-family: sans-serif; padding: 20px; border: 1px solid #C5A059; border-radius: 10px;'>
                <h2 style='color: #C5A059;'>New Website Lead Captured</h2>
                <hr style='border: 0; border-top: 1px solid #eee;'>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Phone:</strong> {$phone}</p>
                <hr style='border: 0; border-top: 1px solid #eee;'>
                <p style='font-size: 10px; color: #888;'>This is an automated delivery from your project website.</p>
            </div>
        ";

        if ($mail->send()) {
            file_put_contents("debug-log.txt", "SUCCESS: Mail sent to primary recipients.\n\n", FILE_APPEND);
            header("Location: thankyou.html");
            exit();
        }

    } catch (Exception $e) {
        $error_msg = "PHPMAILER ERROR: " . $mail->ErrorInfo;
        file_put_contents("debug-log.txt", "ERROR: $error_msg\n\n", FILE_APPEND);
        echo "Error Sending Email. Please contact support. (Debug: $error_msg)";
    }

} else {
    echo "⚠️ Access Denied: Method not allowed.";
}
