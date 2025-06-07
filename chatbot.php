<?php
$api_key = 'AIzaSyD1geTLRgFI4t8bImICKVtLBVwwiYsPvSs';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $userMessage = trim($_POST['message']);

    // Portfolio context to guide the chatbot
    $portfolioContext = <<<EOT
You are a chatbot assistant for Charles Pura's portfolio.

Charles Pura is a Full-Stack Developer and IT student based in Quezon City, Philippines. He specializes in web-based systems and Android Studio applications.

Skills:
- HTML, CSS, JavaScript, PHP, SQL
- Android Studio and Java
- Bootstrap, Tailwind CSS

Projects:
-I've created 172+ projects, including diagrams, web systems, and CRUD.
- Logistics System: manages logistics, inventory, and delivery tracking using HTML5, CSS3, Tailwind CSS, PHP.
- Threat Management System: security system for reporting threats with HTML5, CSS3, Tailwind CSS, PHP.
- PetFinder App: Android app that generates pet info and scans QR codes using Android Studio and Java.
- Kids Learning App: Android app with fingerprint login, videos, and trivia quizzes built with Android Studio and Java.

Contact:
- Email: charles051902pura@gmail.com
- GitHub: https://github.com/charlespura
- Location: Quezon City, Philippines

Answer questions about Charles Pura’s portfolio, skills, projects, background, and contact info clearly and helpfully.
If the question is unrelated to the portfolio, answer it as a general helpful assistant.
EOT;

    // Combine portfolio context with the user message to build the full prompt
    $fullPrompt = $portfolioContext . "\n\nUser: " . $userMessage . "\nAI:";

    $postData = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $fullPrompt]
                ]
            ]
        ]
    ];

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$api_key";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        http_response_code(500);
        echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
        exit;
    }

    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_status !== 200) {
        echo json_encode(['error' => "API request failed with status $http_status", 'response' => $response]);
        exit;
    }

    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'JSON decode error: ' . json_last_error_msg()]);
        exit;
    }

    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        echo json_encode(['reply' => $result['candidates'][0]['content']['parts'][0]['text']]);
    } else {
        echo json_encode(['error' => 'No text found in response', 'full_response' => $result]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Please provide a message']);
}
