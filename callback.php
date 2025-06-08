<?php
$client_id = '47ca30ec497f4e0eb84dde015e6fa71e';
$client_secret = '717778de811f483b9c6acd2712a24173';
$redirect_uri = 'https://cpportfolio.onrender.com/callback.php';

$firebaseUrl = "https://firestore.googleapis.com/v1/projects/charlespuraportfolio/databases/(default)/documents/spotifyTokens";
$firebaseApiKey = "AIzaSyCWI8MnGPuFXFjBvV6eL1vuVDEUOaoUNXo";
$docId = "spotify";  // Document ID for storing tokens

function firestorePatchDocument($firebaseUrl, $apiKey, $docId = "spotify", $data) {
    $url = $firebaseUrl . "/" . $docId . "?key=" . $apiKey;
    $fields = [];
    foreach ($data as $key => $value) {
        if (is_int($value)) {
            $fields[$key] = ["integerValue" => strval($value)];
        } else if (is_float($value)) {
            $fields[$key] = ["doubleValue" => $value];
        } else if (is_string($value)) {
            $fields[$key] = ["stringValue" => $value];
        } else {
            $fields[$key] = ["stringValue" => json_encode($value)];
        }
    }
    $payload = ["fields" => $fields];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpCode === 200;
}

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
        // Add expires_at timestamp
        $data['expires_at'] = time() + $data['expires_in'];

        // Save tokens to Firestore
        $saved = firestorePatchDocument($firebaseUrl, $firebaseApiKey, $docId, $data);
        if (!$saved) {
            echo "Warning: Failed to save tokens to Firestore.";
        } else {
            echo "Authorization successful! Tokens saved to Firestore.";
        }

        echo "<pre>" . print_r($data, true) . "</pre>";
    } else {
        echo "Error exchanging code: " . htmlspecialchars($response);
    }
} else {
    echo "No authorization code received.";
}
