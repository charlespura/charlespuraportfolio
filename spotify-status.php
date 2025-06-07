<?php
$client_id = '47ca30ec497f4e0eb84dde015e6fa71e';
$client_secret = '717778de811f483b9c6acd2712a24173';
$redirect_uri = 'https://cpportfolio.onrender.com/callback.php';
$code = 'AQDaiOMjEgqGbomLmcJvdt1124nV6m9wVieVefzESaTaL7qtV3u4nA8F5zh8WCX1m4c7lh6Fcv0y2QBbAaDM52MXJgD_JtR6ZXJKmV_QFFMJ3ZAuzZf1nZ0JKR9M67KNr1sw3gHYSBMmvzaAP4v1atm0k3NH7psxKiVlgOwGUdBEPigZPXKcodCVCwoymeIkFAewvPknXsvXaAwHvKE_hiN18dc42JxhJVDWG5AV';

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
echo "<pre>";
print_r($data);
echo "</pre>";
?>
