<?php
session_start();

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Trocar o código por tokens

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://accounts.spotify.com/api/token");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirectUri,
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        // Salvar tokens na sessão
        $_SESSION['spotify_access_token'] = $tokenData['access_token'];
        $_SESSION['spotify_refresh_token'] = $tokenData['refresh_token'];
        $_SESSION['spotify_expires_at'] = time() + $tokenData['expires_in'];

        echo "Autenticação bem-sucedida! Tokens salvos na sessão.";
        header('location:profile.php');
        exit();
    } else {
        echo "Falha ao autenticar.";
    }
}else{
    echo "codigo nao recebido";
}
?>
