<?php
// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require 'vendor/autoload.php';

// Function to send email
function sendEmail($receiverEmail, $subject, $content) {
    // Create an instance of PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // Set your SMTP server address
        $mail->SMTPAuth   = true;
        $mail->Username   = 'infolittleaanya@gmail.com';  // SMTP username
        $mail->Password   = 'helk vtmo ecog zugo';     // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
        $mail->Port       = 587;  // TCP port for connection (TLS)

        // Email settings
        $mail->setFrom('infolittleaanya@gmail.com', 'Ravel Tech Consultancy');
        $mail->addAddress($receiverEmail);  // Add a recipient
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $content;  // Email content
        $mail->AltBody = strip_tags($content);  // Fallback for non-HTML clients

        // Send the email
        $mail->send();
        return 'Email sent successfully.';
    } catch (Exception $e) {
        return "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}
