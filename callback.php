<?php
// Spotify app credentials
$client_id = '47ca30ec497f4e0eb84dde015e6fa71e';
$client_secret = '717778de811f483b9c6acd2712a24173';
$redirect_uri = 'https://cpportfolio.onrender.com/callback'; // Make sure it matches Spotify's redirect URI

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
    // Save the refresh token and access token securely (like in a database or a secure file)
    file_put_contents('spotify_tokens.json', json_encode($data));

    echo "Successfully authorized with Spotify!<br>";
    echo "Access Token: " . $data['access_token'] . "<br>";
    echo "Refresh Token: " . $data['refresh_token'] . "<br>";
} else {
    echo "Error: " . $response;
}
?>
