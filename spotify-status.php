<?php
// Your Spotify API credentials
$client_id = '47ca30ec497f4e0eb84dde015e6fa71e';
$client_secret = '717778de811f483b9c6acd2712a24173';
$refresh_token = 'YOUR_SPOTIFY_REFRESH_TOKEN';

// Get a new access token using the refresh token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'grant_type' => 'refresh_token',
    'refresh_token' => $refresh_token,
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . base64_encode("$client_id:$client_secret"),
    'Content-Type: application/x-www-form-urlencoded'
]);
$response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($response, true);
$access_token = $token_data['access_token'] ?? null;

if (!$access_token) {
    echo json_encode(['error' => 'Unable to get access token']);
    exit;
}

// Get currently playing track
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.spotify.com/v1/me/player/currently-playing');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $access_token"
]);
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    $data = json_decode($response, true);
    if (isset($data['item'])) {
        echo json_encode([
            'track' => $data['item']['name'],
            'artist' => $data['item']['artists'][0]['name'],
            'album_art' => $data['item']['album']['images'][0]['url']
        ]);
    } else {
        echo json_encode(['track' => 'Nothing playing right now']);
    }
} else {
    echo json_encode(['track' => 'No response']);
}
?>
