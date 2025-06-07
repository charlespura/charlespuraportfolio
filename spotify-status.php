<?php
// spotify-status.php

header('Content-Type: application/json');

$client_id = '47ca30ec497f4e0eb84dde015e6fa71e';
$client_secret = '717778de811f483b9c6acd2712a24173';
$token_file = sys_get_temp_dir() . '/spotify_tokens.json';

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

// Function to refresh token
function refreshAccessToken($client_id, $client_secret, $refresh_token) {
    $ch = curl_init('https://accounts.spotify.com/api/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'refresh_token',
        'refresh_token' => $refresh_token,
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode("$client_id:$client_secret"),
        'Content-Type: application/x-www-form-urlencoded',
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Check if token expired (with 60 second buffer)
if (!isset($tokens['expires_at']) || time() > ($tokens['expires_at'] - 60)) {
    $refresh_data = refreshAccessToken($client_id, $client_secret, $tokens['refresh_token']);
    if (isset($refresh_data['access_token'])) {
        $tokens['access_token'] = $refresh_data['access_token'];
        if (isset($refresh_data['expires_in'])) {
            $tokens['expires_in'] = $refresh_data['expires_in'];
            $tokens['expires_at'] = time() + $refresh_data['expires_in'];
        }
        if (isset($refresh_data['refresh_token'])) {
            $tokens['refresh_token'] = $refresh_data['refresh_token'];
        }
        file_put_contents($token_file, json_encode($tokens, JSON_PRETTY_PRINT));
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to refresh token']);
        exit;
    }
}

// Use access token to get current playback
$ch = curl_init('https://api.spotify.com/v1/me/player/currently-playing');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $tokens['access_token'],
]);

$playback_response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Handle no content or not playing
if ($http_code == 204 || $http_code == 404) {
    echo json_encode([
        'track' => 'Nothing playing right now',
        'artist' => '',
        'album_art' => '',
        'url' => ''
    ]);
    exit;
}

$data = json_decode($playback_response, true);
if (!$data || !isset($data['item'])) {
    echo json_encode([
        'track' => 'Nothing playing right now',
        'artist' => '',
        'album_art' => '',
        'url' => ''
    ]);
    exit;
}

$track_name = $data['item']['name'] ?? 'Unknown Track';
$artist_name = $data['item']['artists'][0]['name'] ?? 'Unknown Artist';
$album_art_url = $data['item']['album']['images'][0]['url'] ?? '';
$track_url = $data['item']['external_urls']['spotify'] ?? '';

echo json_encode([
    'track' => $track_name,
    'artist' => $artist_name,
    'album_art' => $album_art_url,
    'url' => $track_url
]);
