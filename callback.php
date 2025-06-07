<?php
// Spotify app credentials
$client_id = '47ca30ec497f4e0eb84dde015e6fa71e';
$client_secret = '717778de811f483b9c6acd2712a24173';
$redirect_uri = 'https://cpportfolio.onrender.com/callback.php';

// Get the authorization code from the URL or use a hardcoded code for testing
if (isset($_GET['code']) && !empty($_GET['code'])) {
    $code = $_GET['code'];
} else {
    // Manually set the code here for testing if no code is in URL
    $code = 'AQDaiOMjEgqGbomLmcJvdt1124nV6m9wVieVefzESaTaL7qtV3u4nA8F5zh8WCX1m4c7lh6Fcv0y2QBbAaDM52MXJgD_JtR6ZXJKmV_QFFMJ3ZAuzZf1nZ0JKR9M67KNr1sw3gHYSBMmvzaAP4v1atm0k3NH7psxKiVlgOwGUdBEPigZPXKcodCVCwoymeIkFAewvPknXsvXaAwHvKE_hiN18dc42JxhJVDWG5AV';
}

// Exchange code for access and refresh tokens
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
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
    // Save refresh token & access token securely
    $saved = file_put_contents('/var/www/html/token/spotify_tokens.json', json_encode($data, JSON_PRETTY_PRINT));
    if ($saved === false) {
        echo "Warning: Failed to save tokens. Check file permissions.";
    } else {
        echo "Authorization successful! Refresh token saved.";
    }
    echo "<pre>";
    print_r($data);
    echo "</pre>";
} else {
    echo "Error: " . $response;
}
?>
