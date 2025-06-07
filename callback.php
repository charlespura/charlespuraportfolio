<?php
// Spotify API credentials
$client_id = '47ca30ec497f4e0eb84dde015e6fa71e';
$client_secret = '717778de811f483b9c6acd2712a24173';
$redirect_uri = 'https://cpportfolio.onrender.com/callback'; // This must match your Spotify App redirect URI!

// Check if we have an authorization code
if (!isset($_GET['code'])) {
    die('No authorization code provided!');
}

$code = $_GET['code'];

// Exchange the authorization code for an access token and refresh token
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
    'Content-Type: application/x-www-form-urlencoded',
]);

$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    die('Error getting tokens from Spotify.');
}

$data = json_decode($response, true);

// Display or store your refresh token
if (isset($data['refresh_token'])) {
    echo "<h3>✅ Your Refresh Token (save this!):</h3>";
    echo "<pre>" . htmlspecialchars($data['refresh_token']) . "</pre>";
} else {
    echo "<h3>⚠️ No refresh token found.</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}
?>
