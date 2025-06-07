<?php
// callback.php

$client_id = '47ca30ec497f4e0eb84dde015e6fa71e';
$client_secret = '717778de811f483b9c6acd2712a24173';
$redirect_uri = 'https://cpportfolio.onrender.com/callback.php';

// Use /tmp directory to avoid permission errors
$token_file = sys_get_temp_dir() . '/spotify_tokens.json';

if (isset($_GET['code']) && !empty($_GET['code'])) {
    $code = $_GET['code'];

    // Exchange authorization code for tokens
    $ch = curl_init('https://accounts.spotify.com/api/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirect_uri,
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode("$client_id:$client_secret"),
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['access_token'])) {
        // Add expires_at timestamp (current time + expires_in seconds)
        $data['expires_at'] = time() + $data['expires_in'];

        // Save tokens to /tmp directory
        $saved = file_put_contents($token_file, json_encode($data, JSON_PRETTY_PRINT));
        if ($saved === false) {
            echo "Warning: Failed to save tokens to $token_file. Check file permissions.";
        } else {
            echo "Authorization successful! Tokens saved to $token_file.";
        }

        echo "<pre>" . print_r($data, true) . "</pre>";
    } else {
        echo "Error exchanging code: " . htmlspecialchars($response);
    }
} else {
    echo "No authorization code received.";
}
