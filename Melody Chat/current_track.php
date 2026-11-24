<?php

require('refresh_troken.php');

refreshTokenIfNeeded();

$accessToken = $_SESSION['spotify_access_token'];
$apiUrl = 'https://api.spotify.com/v1/me/player/recently-played?limit=1';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['items'][0])) {
    $track = $data['items'][0]['track'];
    echo "Última música tocada: {$track['name']} - {$track['artists'][0]['name']}";
} else {
    echo "Nenhuma reprodução recente encontrada.";
}


?>