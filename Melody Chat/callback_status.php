<?php
session_start();

define('SPOTIFY_CLIENT_ID', '339071cd398c4a8fbb3fc58bdfb62d91');
define('SPOTIFY_CLIENT_SECRET', 'b6bce70a6ebc4afa973af67eb6990201');
define('SPOTIFY_REDIRECT_URI', 'https://aluno18404.damiaodegoes.pt/callback_status.php');

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $url = 'https://accounts.spotify.com/api/token';
    $data = [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => SPOTIFY_REDIRECT_URI,
        'client_id' => SPOTIFY_CLIENT_ID,
        'client_secret' => SPOTIFY_CLIENT_SECRET,
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        die("Erro ao conectar ao Spotify: $error");
    }

    $result = json_decode($response, true);

    if (isset($result['access_token'])) {
        $_SESSION['access_token'] = $result['access_token'];
        $_SESSION['refresh_token'] = $result['refresh_token'];
        header('Location: stats_spotify.php'); // Redireciona para o status
        exit();
    } else {
        die("Erro ao obter o token: " . json_encode($result));
    }
} else {
    die("Autenticação falhou.");
}
