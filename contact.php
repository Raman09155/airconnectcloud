<?php

// ---------------------
// ERROR LOGGING SETUP
// ---------------------
ini_set('display_errors', 0); // Hide errors from the user
ini_set('log_errors', 1);     // Log errors to PHP error log
error_reporting(E_ALL);       // Report all errors

// ---------------------
// SET JSON HEADER
// ---------------------
header('Content-Type: application/json');

// ---------------------
// LOAD ENV VARIABLES
// ---------------------
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Load .env
try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to load environment variables.',
        'error' => $e->getMessage()
    ]);
    exit;
}

// ---------------------
// SANITIZE FUNCTION
// ---------------------
function sanitizeInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

// ---------------------
// DEFAULT RESPONSE
// ---------------------
$response = [
    'status' => 'error',
    'message' => 'Something went wrong, please try again.'
];

// ---------------------
// HANDLE POST REQUEST
// ---------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $company = isset($_POST['company']) ? sanitizeInput($_POST['company']) : '';
    $description = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($company)) {
        $response['message'] = 'All fields are required.';
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
        echo json_encode($response);
        exit;
    }

    try {
        // Create mail object
        $mail = new PHPMailer(true);

        // SMTP configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.rediffmailpro.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_EMAIL'];       // from .env
        $mail->Password   = $_ENV['SMTP_PASSWORD'];    // from .env
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Sender
        $mail->setFrom($_ENV['SMTP_EMAIL'], 'Website Form');
        $mail->addReplyTo($email, $name);

        // Recipients (Multiple)
        $recipientList = explode(',', $_ENV['RECEIVER_EMAILS']);
        foreach ($recipientList as $recipientEmail) {
            $cleanEmail = trim($recipientEmail);
            if (!empty($cleanEmail) && filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($cleanEmail);
            }
        }

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission';
        $mail->Body    = "
            <strong>Name:</strong> $name<br>
            <strong>Email:</strong> $email<br>
            <strong>Phone:</strong> $phone<br>
            <strong>Company:</strong> $company<br>
            <strong>Description:</strong> $description
        ";

        $mail->send();

        $response['status'] = 'success';
        $response['message'] = 'Thank you for reaching out! We will get back to you soon.';
    } catch (Exception $e) {
        $response['message'] = 'Mailer Error: ' . $mail->ErrorInfo;
    }

    echo json_encode($response);
    exit;
}
