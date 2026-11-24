<?php
session_start();

$responseData = [
    'trackName' => 'Nenhuma música tocando no momento.',
    'artists' => '',
    'albumImage' => '',
    'progress' => 0,
    'duration' => 0,
    'error' => ''
];

if (isset($_SESSION['spotify_access_token'])) {
    $accessToken = $_SESSION['spotify_access_token'];

    // Fazer a solicitação para a API Recently Played
    $url = 'https://api.spotify.com/v1/me/player/recently-played?limit=1';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accessToken
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['items']) && !empty($data['items'])) {
        $lastPlayed = $data['items'][0];
        $trackName = $lastPlayed['track']['name'];
        $artists = array_map(function ($artist) {
            return $artist['name'];
        }, $lastPlayed['track']['artists']);
        $albumImage = $lastPlayed['track']['album']['images'][0]['url'];
        $playedAt = $lastPlayed['played_at'];
        
        $responseData['trackName'] = $trackName;
        $responseData['artists'] = implode(", ", $artists);
        $responseData['albumImage'] = $albumImage;
        $responseData['playedAt'] = $playedAt;
    } else {
        $responseData['trackName'] = 'Nenhuma música foi tocada recentemente ou o histórico está desativado.';
        $responseData['artists'] = '';
        $responseData['albumImage'] = '';
        $responseData['playedAt'] = '';
    }
} else {
    $responseData['error'] = 'Usuário não autenticado.';
}

header('Content-Type: application/json');
echo json_encode($responseData);
?>