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

    // Consultar o Top 5 faixas
    $ch_tracks = curl_init();
    curl_setopt($ch_tracks, CURLOPT_URL, "https://api.spotify.com/v1/me/top/tracks?limit=5");
    curl_setopt($ch_tracks, CURLOPT_HTTPHEADER, ["Authorization: Bearer $access_token"]);
    curl_setopt($ch_tracks, CURLOPT_RETURNTRANSFER, true);
    $response_tracks = curl_exec($ch_tracks);
    curl_close($ch_tracks);

    // Consultar o Top 3 artistas
    $ch_artists = curl_init();
    curl_setopt($ch_artists, CURLOPT_URL, "https://api.spotify.com/v1/me/top/artists?limit=3");
    curl_setopt($ch_artists, CURLOPT_HTTPHEADER, ["Authorization: Bearer $access_token"]);
    curl_setopt($ch_artists, CURLOPT_RETURNTRANSFER, true);
    $response_artists = curl_exec($ch_artists);
    curl_close($ch_artists);

    $data_tracks = json_decode($response_tracks, true);
    $data_artists = json_decode($response_artists, true);

    if ($data_tracks && $data_artists) {
        echo json_encode([
            'tracks' => $data_tracks['items'],
            'artists' => $data_artists['items']
        ]);
    } else {
        echo json_encode(['error' => 'Erro ao recuperar as faixas ou artistas']);
    }
} else {
    echo json_encode(['error' => 'Usuário não encontrado']);
}
?>
