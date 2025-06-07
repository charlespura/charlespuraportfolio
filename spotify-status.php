<?php
// spotify-status.php

$client_id = '47ca30ec497f4e0eb84dde015e6fa71e';
$client_secret = '717778de811f483b9c6acd2712a24173';
$token_file = __DIR__ . '/spotify_tokens.json';

// Load saved tokens
if (!file_exists($token_file)) {
    http_response_code(500);
    echo json_encode(['error' => 'Tokens not found. Please authenticate via /callback.php']);
    exit;
}

$tokens = json_decode(file_get_contents($token_file), true);
if (!$tokens) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to read tokens']);
    exit;
}

// Check token expiration & refresh if needed (simple approach: always refresh for demo)
$ch = curl_init('https://accounts.spotify.com/api/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'grant_type' => 'refresh_token',
    'refresh_token' => $tokens['refresh_token'],
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . base64_encode("$client_id:$client_secret"),
    'Content-Type: application/x-www-form-urlencoded'
]);
$response = curl_exec($ch);
curl_close($ch);

$refresh_data = json_decode($response, true);

if (isset($refresh_data['access_token'])) {
    // Update saved tokens with new access_token (and optionally expiry)
    $tokens['access_token'] = $refresh_data['access_token'];
    // Save updated tokens (refresh token usually doesn't change)
    file_put_contents($token_file, json_encode($tokens, JSON_PRETTY_PRINT));
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to refresh token']);
    exit;
}

// Use the valid access token to get current playback
$ch = curl_init('https://api.spotify.com/v1/me/player/currently-playing');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $tokens['access_token'],
]);

$playback_response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 204 || $http_code == 404) {
    // No content or not playing anything
    echo json_encode([
        'track' => 'Nothing playing right now',
        'artist' => '',
        'album_art' => ''
    ]);
    exit;
}

$data = json_decode($playback_response, true);
if (!$data || !isset($data['item'])) {
    echo json_encode([
        'track' => 'Nothing playing right now',
        'artist' => '',
        'album_art' => ''
    ]);
    exit;
}

$track_name = $data['item']['name'] ?? 'Unknown Track';
$artist_name = $data['item']['artists'][0]['name'] ?? 'Unknown Artist';
$album_art_url = $data['item']['album']['images'][0]['url'] ?? '';

echo json_encode([
    'track' => $track_name,
    'artist' => $artist_name,
    'album_art' => $album_art_url
]);
