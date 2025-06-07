<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

function verifyRecaptcha($token, $secretKey) {
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $response = file_get_contents($verifyUrl . '?secret=' . urlencode($secretKey) . '&response=' . urlencode($token));
    $responseData = json_decode($response);
    return $responseData && $responseData->success;
}

function sendToFirestore($data, $firebaseUrl, $firebaseApiKey) {
    $docData = [
        "fields" => [
            "firstName" => ["stringValue" => $data['firstName']],
            "lastName" => ["stringValue" => $data['lastName']],
            "email" => ["stringValue" => $data['email']],
            "phone" => ["stringValue" => $data['phone']],
            "message" => ["stringValue" => $data['message']],
            "timestamp" => ["timestampValue" => date("c")] // RFC3339 timestamp
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $firebaseUrl . "?key=" . $firebaseApiKey);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($docData));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode === 200;
}

function sendEmailNotification($data, $to) {
    $subject = 'New Contact Message from ' . $data['firstName'] . ' ' . $data['lastName'];
    $body = "Name: {$data['firstName']} {$data['lastName']}\nEmail: {$data['email']}\nPhone: {$data['phone']}\n\nMessage:\n{$data['message']}";
    $headers = "From: {$data['email']}\r\nReply-To: {$data['email']}\r\n";

    return mail($to, $subject, $body, $headers);
}

// --- Main script starts here ---

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

// Google reCAPTCHA secret key
$recaptchaSecretKey = '6LcR6FYrAAAAAE9vYeMLYSEsugW7j7WoZwCc0lO0';

// Firebase Firestore settings
$firebaseUrl = "https://firestore.googleapis.com/v1/projects/charlespuraportfolio/databases/(default)/documents/contactFeedbacks";
$firebaseApiKey = "AIzaSyCWI8MnGPuFXFjBvV6eL1vuVDEUOaoUNXo";

// Sanitize input
$firstName = htmlspecialchars(trim($_POST['first_name'] ?? ''));
$lastName = htmlspecialchars(trim($_POST['last_name'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));
$recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

// Validate reCAPTCHA
if (!$recaptchaResponse || !verifyRecaptcha($recaptchaResponse, $recaptchaSecretKey)) {
    http_response_code(400);
    echo json_encode(['error' => 'reCAPTCHA verification failed.']);
    exit;
}

// Validate required fields
if (empty($firstName) || empty($lastName) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(["error" => "Please fill in all required fields."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid email address."]);
    exit;
}

$formData = [
    'firstName' => $firstName,
    'lastName' => $lastName,
    'email' => $email,
    'phone' => $phone,
    'message' => $message,
];

// Send to Firestore
if (!sendToFirestore($formData, $firebaseUrl, $firebaseApiKey)) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to send message to database. Please try again later."]);
    exit;
}

// Send email notification
if (!sendEmailNotification($formData, 'charles051902pura@gmail.com')) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to send email notification. Please try again later."]);
    exit;
}

echo json_encode(["success" => "Thank you for your message! 😊"]);
