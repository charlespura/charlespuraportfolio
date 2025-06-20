<?php
header('Content-Type: application/json');

$client_id = '47ca30ec497f4e0eb84dde015e6fa71e';
$client_secret = '717778de811f483b9c6acd2712a24173';

$firebaseUrl = "https://firestore.googleapis.com/v1/projects/charlespuraportfolio/databases/(default)/documents/spotifyTokens";
$firebaseApiKey = "AIzaSyCWI8MnGPuFXFjBvV6eL1vuVDEUOaoUNXo";
$docId = "spotify";

function firestoreGetDocument($firebaseUrl, $apiKey, $docId) {
    $url = $firebaseUrl . "/" . $docId . "?key=" . $apiKey;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode !== 200) return false;
    return json_decode($response, true);
}

function firestorePatchDocument($firebaseUrl, $apiKey, $data, $docId) {
    $url = $firebaseUrl . "/" . $docId . "?key=" . $apiKey;
    $fields = [];
    foreach ($data as $key => $value) {
        if (is_int($value)) {
            $fields[$key] = ["integerValue" => strval($value)];
        } elseif (is_float($value)) {
            $fields[$key] = ["doubleValue" => $value];
        } elseif (is_string($value)) {
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

$doc = firestoreGetDocument($firebaseUrl, $firebaseApiKey, $docId);
if (!$doc || !isset($doc['fields'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Tokens not found. Please authenticate via /callback.php']);
    exit;
}

$tokens = [];
foreach ($doc['fields'] as $key => $value) {
    if (isset($value['stringValue'])) {
        $tokens[$key] = $value['stringValue'];
    } elseif (isset($value['integerValue'])) {
        $tokens[$key] = intval($value['integerValue']);
    }
}

if (isset($tokens['expires_at'])) {
    $tokens['expires_at'] = (int)$tokens['expires_at'];
}

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
        firestorePatchDocument($firebaseUrl, $firebaseApiKey, $tokens, $docId);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to refresh token']);
        exit;
    }
}

// Try to get current playback
$ch = curl_init('https://api.spotify.com/v1/me/player/currently-playing');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $tokens['access_token']]);
$playback_response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 204 || $http_code == 404) {
    // If not playing, get the last played track
    $ch = curl_init('https://api.spotify.com/v1/me/player/recently-played?limit=1');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $tokens['access_token']]);
    $recent_response = curl_exec($ch);
    curl_close($ch);

    $recent_data = json_decode($recent_response, true);
    if (isset($recent_data['items'][0]['track'])) {
        $item = $recent_data['items'][0]['track'];
        $track_name = $item['name'] ?? 'Unknown Track';
        $artist_name = $item['artists'][0]['name'] ?? 'Unknown Artist';
        $album_art_url = $item['album']['images'][0]['url'] ?? '';
        $track_url = $item['external_urls']['spotify'] ?? '';
        echo json_encode([
            'track' => $track_name . " (Last played)",
            'artist' => $artist_name,
            'album_art' => $album_art_url,
            'url' => $track_url,
            'progress_ms' => 0,
            'duration_ms' => 0
        ]);
        exit;
    } else {
        echo json_encode([
            'track' => 'Nothing playing right now',
            'artist' => '',
            'album_art' => '',
            'url' => '',
            'progress_ms' => 0,
            'duration_ms' => 0
        ]);
        exit;
    }
}

$data = json_decode($playback_response, true);
if (!$data || !isset($data['item'])) {
    echo json_encode([
        'track' => 'Nothing playing right now',
        'artist' => '',
        'album_art' => '',
        'url' => '',
        'progress_ms' => 0,
        'duration_ms' => 0
    ]);
    exit;
}

$item = $data['item'];
$track_name = $item['name'] ?? 'Unknown Track';
$artist_name = $item['artists'][0]['name'] ?? 'Unknown Artist';
$album_art_url = $item['album']['images'][0]['url'] ?? '';
$track_url = $item['external_urls']['spotify'] ?? '';
$progress_ms = $data['progress_ms'] ?? 0;
$duration_ms = $item['duration_ms'] ?? 0;

echo json_encode([
    'track' => $track_name,
    'artist' => $artist_name,
    'album_art' => $album_art_url,
    'url' => $track_url,
    'progress_ms' => $progress_ms,
    'duration_ms' => $duration_ms
]);
?>
