<?php
session_start();
require('ligacao_melody.php');

if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

$email = $_SESSION['email'];
$query = "SELECT access_token FROM faixas WHERE email = '$email'";
$result = mysqli_query($con, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $access_token = $row['access_token'];
    mysqli_free_result($result);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/me/player/currently-playing");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $access_token"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        $data = json_decode($response, true);
        if (empty($data) || !isset($data['item'])) {
            echo json_encode(['error' => 'Nenhuma música está a tocar']);
        } else {
            echo $response; // Retorna os dados do Spotify diretamente
        }
    } elseif ($http_code === 204) {
        echo json_encode(['error' => 'Nenhuma música está a tocar']);
    } elseif ($http_code === 401) {
        echo json_encode(['error' => 'Token Expirado.. por favor Atualize']);
    } else {
        echo json_encode(['error' => 'Erro na API Spotify', 'code' => $http_code]);
    }
} else {
    echo json_encode(['error' => 'Usuário não encontrado']);
}
