<?php
$client_id = '339071cd398c4a8fbb3fc58bdfb62d91';
$redirect_uri = 'https://aluno18404.damiaodegoes.pt/callback.php';
$scopes = 'streaming user-read-playback-state user-modify-playback-state user-read-private user-read-email user-library-read streaming';  // Defina os escopos que você precisa

$authUrl = "https://accounts.spotify.com/authorize?client_id={$clientId}&response_type=code&redirect_uri={$redirectUri}&scope={$scopes}";

// Redireciona o usuário para a página de autorização
header('Location: ' . $authUrl);
exit;
?>