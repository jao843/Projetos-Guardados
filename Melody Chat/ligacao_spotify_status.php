<?php
session_start();
require 'spotify_config.php';

define('SPOTIFY_CLIENT_ID', '339071cd398c4a8fbb3fc58bdfb62d91');
define('SPOTIFY_REDIRECT_URI', 'https://aluno18404.damiaodegoes.pt/callback_status.php');

$scopes = [
    'user-top-read',  // Para acessar os artistas e faixas mais ouvidos
    'user-read-email',  // Para acessar o e-mail do usuário
    'user-read-private', // Para informações do perfil
];

$authUrl = "https://accounts.spotify.com/authorize?" . http_build_query([
    'client_id' => SPOTIFY_CLIENT_ID,
    'response_type' => 'code',
    'redirect_uri' => SPOTIFY_REDIRECT_URI,
    'scope' => implode(' ', $scopes),
    'state' => bin2hex(random_bytes(16)),
]);

header('Location: ' . $authUrl);
exit();

