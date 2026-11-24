<?php
session_start();

// Função para renovar o token de acesso usando o refresh token
function refreshAccessToken() {
    $clientId = '339071cd398c4a8fbb3fc58bdfb62d91';
    $clientSecret = 'b6bce70a6ebc4afa973af67eb6990201';
    $refreshToken = $_SESSION['spotify_refresh_token'];

    $url = 'https://accounts.spotify.com/api/token';
    $data = [
        'grant_type' => 'refresh_token',
        'refresh_token' => $refreshToken,
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['access_token'])) {
        $_SESSION['spotify_access_token'] = $result['access_token'];
        $_SESSION['spotify_expires_in'] = time() + $result['expires_in'];
    } else {
        throw new Exception('Erro ao renovar o token.');
    }
}

// Verifica se o token de acesso expirou e precisa ser renovado
if (time() > $_SESSION['spotify_expires_in']) {
    refreshAccessToken();
}
?>
