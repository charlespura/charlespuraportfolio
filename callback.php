<?php
// Spotify app credentials
$client_id = '47ca30ec497f4e0eb84dde015e6fa71e';
$client_secret = '717778de811f483b9c6acd2712a24173';
$redirect_uri = 'https://cpportfolio.onrender.com/callback.php';

// Get the authorization code from the URL
$code = $_GET['code'];

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

// Handle the response
$data = json_decode($response, true);
if (isset($data['access_token'])) {
    // Save refresh token & access token securely
  //file_put_contents(__DIR__ . '/token/spotify_tokens.json', json_encode($data));

file_put_contents('/var/www/html/token/spotify_tokens.json', json_encode($data));

    echo "Authorization successful! Refresh token saved.";
} else {
    echo "Error: " . $response;
}
?>
