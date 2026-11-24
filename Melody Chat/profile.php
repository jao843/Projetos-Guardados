<?php
session_start();
require('ligacao_melody.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['email'])) {
    header('location:login.php');
    exit();
}

if (isset($_POST['starter'])) {
    header('location:auth.php');
    exit();
}

if (isset($_POST['change'])) {
    header('location:terminar.php');
    exit();
} else {
    $email = $_SESSION['email'];
    $query = "SELECT access_token FROM faixas WHERE email = '$email'";
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $access_token = $row['access_token'];

        // Consultar histórico recente
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/me/player/recently-played?limit=50");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $access_token"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 401) {
            json_encode(['error' => 'Token Expirado.. por favor Atualize']);
        }

        $data = json_decode($response, true);

        if ($data && isset($data['item'])) {
            json_encode($data);
        } else {
            json_encode(['error' => 'Nenhuma música está tocando no momento.']);
        }

        $recent_tracks = json_decode($response, true);

        $current_month_start = strtotime('first day of this month');
        $current_month_end = strtotime('last day of this month');
        $current_month_year = date('Y-m'); // e.g., '2025-04'

        if (isset($recent_tracks['items'])) {
            foreach ($recent_tracks['items'] as $item) {
                $played_at = strtotime($item['played_at']);
                if ($played_at >= $current_month_start && $played_at <= $current_month_end) {
                    $played_at_formatted = date('Y-m-d H:i:s', $played_at);

                    $query_check = "SELECT * FROM playback_history WHERE played_at = '$played_at_formatted' AND email = '" . $_SESSION['email'] . "'";
                    $result_check = mysqli_query($con, $query_check);

                    if (mysqli_num_rows($result_check) == 0) {
                        $track_name = mysqli_real_escape_string($con, $item['track']['name']);
                        $artist_name = mysqli_real_escape_string($con, $item['track']['artists'][0]['name']);
                        $track_duration_ms = $item['track']['duration_ms'];

                        $query_insert = "INSERT INTO playback_history (track_name, artist_name, played_at, duration_ms, email) 
                                        VALUES ('$track_name', '$artist_name', '$played_at_formatted', '$track_duration_ms', '" . $_SESSION['email'] . "')";
                        if (mysqli_query($con, $query_insert)) {
                        }

                        $query = "SELECT SUM(duration_ms) as total_duration FROM playback_history WHERE email = '" . $_SESSION['email'] . "'";
                        $result = mysqli_query($con, $query);

                        $row = mysqli_fetch_assoc($result);

                        if ($row && isset($row['total_duration'])) {
                            $_SESSION['total_duration_minutes'] = round($row['total_duration'] / 60000);
                        }
                    }
                }
            }

            // Consultar top 5 artistas do mês
            // Consultar top 5 artistas do mês
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/me/top/artists?time_range=short_term&limit=5");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $access_token"
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $top_response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 401) {
            } else {
                $top_artists_data = json_decode($top_response, true);
                $top_artists = [];

                if (isset($top_artists_data['items'])) {
                    $artist_names = array_map(function ($artist) {
                        return $artist['name'];
                    }, $top_artists_data['items']);

                    // Busca imagens dos artistas via Spotify API
                    foreach ($artist_names as $artist_name) {
                        $query = urlencode($artist_name);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/search?q=$query&type=artist&limit=5");
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            "Authorization: Bearer $access_token"
                        ]);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($ch);
                        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);

                        if ($http_code === 401) {
                            error_log("Token expirado para busca de artista '$artist_name'");
                            $top_artists[] = [
                                'name' => $artist_name,
                                'image' => 'melodychat.png'
                            ];
                            continue;
                        }

                        if ($http_code === 200) {
                            $data = json_decode($response, true);
                            $found = false;
                            if (isset($data['artists']['items']) && !empty($data['artists']['items'])) {
                                // Procura o artista com nome mais próximo
                                foreach ($data['artists']['items'] as $artist) {
                                    if (strtolower($artist['name']) === strtolower($artist_name)) {
                                        $image_url = !empty($artist['images']) ? $artist['images'][0]['url'] : 'melodychat.png';
                                        $top_artists[] = [
                                            'name' => $artist_name,
                                            'image' => $image_url
                                        ];
                                        $found = true;
                                        break;
                                    }
                                }
                                // Se não encontrou correspondência exata, usa o primeiro resultado
                                if (!$found) {
                                    $artist = $data['artists']['items'][0];
                                    $image_url = !empty($artist['images']) ? $artist['images'][0]['url'] : 'melodychat.png';
                                    $top_artists[] = [
                                        'name' => $artist_name,
                                        'image' => $image_url
                                    ];
                                    error_log("Artist '$artist_name' not matched exactly, using '{$artist['name']}'");
                                }
                            } else {
                                // Nenhum resultado encontrado
                                $top_artists[] = [
                                    'name' => $artist_name,
                                    'image' => 'melodychat.png'
                                ];
                                error_log("No search results for artist '$artist_name'");
                            }
                        } else {
                            // Erro na API
                            $top_artists[] = [
                                'name' => $artist_name,
                                'image' => 'melodychat.png'
                            ];
                            error_log("Spotify API error for '$artist_name': HTTP $http_code");
                        }
                    }

                    // Preparar os 5 artistas e suas imagens
                    $artist_fields = [];
                    for ($i = 1; $i <= 5; $i++) {
                        $artist_fields["artist_$i"] = '';
                        $artist_fields["artist_{$i}_image"] = 'melodychat.png';
                    }

                    $i = 0;
                    foreach ($top_artists as $index => $artist) {
                        if ($i < 5) {
                            $artist_fields["artist_" . ($index + 1)] = mysqli_real_escape_string($con, $artist['name']);
                            $artist_fields["artist_" . ($index + 1) . "_image"] = mysqli_real_escape_string($con, $artist['image']);
                            $i++;
                        }
                    }

                    // Verificar se já existe um registro para este usuário e mês
                    $query_top_check = "SELECT * FROM top_artists WHERE email = '$email'";
                    $result_top_check = mysqli_query($con, $query_top_check);

                    if (mysqli_num_rows($result_top_check) > 0) {
                        // Atualizar registro existente
                        $query_update = "UPDATE top_artists SET 
                            month_year = '{$current_month_year}',
                            artist_1 = '{$artist_fields['artist_1']}',
                            artist_1_image = '{$artist_fields['artist_1_image']}',
                            artist_2 = '{$artist_fields['artist_2']}',
                            artist_2_image = '{$artist_fields['artist_2_image']}',
                            artist_3 = '{$artist_fields['artist_3']}',
                            artist_3_image = '{$artist_fields['artist_3_image']}',
                            artist_4 = '{$artist_fields['artist_4']}',
                            artist_4_image = '{$artist_fields['artist_4_image']}',
                            artist_5 = '{$artist_fields['artist_5']}',
                            artist_5_image = '{$artist_fields['artist_5_image']}'
                            WHERE email = '$email'";
                        if (!mysqli_query($con, $query_update)) {
                            echo "Erro ao atualizar top artistas: " . mysqli_error($con);
                        }
                    } else {
                        // Inserir novo registro
                        $query_insert = "INSERT INTO top_artists (email, month_year, artist_1, artist_1_image, artist_2, artist_2_image, artist_3, artist_3_image, artist_4, artist_4_image, artist_5, artist_5_image)
                            VALUES ('$email', '$current_month_year',
                                    '{$artist_fields['artist_1']}', '{$artist_fields['artist_1_image']}',
                                    '{$artist_fields['artist_2']}', '{$artist_fields['artist_2_image']}',
                                    '{$artist_fields['artist_3']}', '{$artist_fields['artist_3_image']}',
                                    '{$artist_fields['artist_4']}', '{$artist_fields['artist_4_image']}',
                                    '{$artist_fields['artist_5']}', '{$artist_fields['artist_5_image']}')";
                        if (!mysqli_query($con, $query_insert)) {
                            echo "Erro ao inserir top artistas: " . mysqli_error($con);
                        }
                    }
                }
            }
        }
    } else {
        echo "Erro ao obter o token de acesso!";
    }
}

$est = "SELECT estado FROM manutencao WHERE id = 1;";
$estado = mysqli_query($con, $est);
$row = mysqli_fetch_assoc($estado);

if ($row['estado'] == FALSE and isset($_SESSION['email'])) {
    $sql = "SELECT * from melodychat where email = '" . $_SESSION['email'] . "'";
    $sql_cargo = mysqli_query($con, $sql);

    $cargo_email = mysqli_fetch_assoc($sql_cargo);

    if ($cargo_email['cargo'] == FALSE) {
        header('location:dsession_melodychat.php');
        exit();
    }
}

date_default_timezone_set('Europe/Lisbon');
$time_now = date("Y-m-d H:i:s");

$login_sql = "UPDATE melodychat set login = 1,login_time = '$time_now' where email = '" . $_SESSION['email'] . "'";
$login_query = mysqli_query($con, $login_sql);

// Obtém informações do usuário
$sql = "SELECT * FROM melodychat WHERE email = '" . $_SESSION['email'] . "';";
$result = mysqli_query($con, $sql);
$user = mysqli_fetch_assoc($result);

if (isset($_POST['visu_perfil'])) {
    $_SESSION['visu_perfil'] = $_SESSION['id'];
    header('location:profile_user.php');
    exit();
}

if (isset($_POST['editar_perfil'])) {
    $_SESSION['visu_perfil'] = $_SESSION['id'];
    header('location:editar_profile.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.lineicons.com/5.0/lineicons.css" rel="stylesheet" />
    <link rel="icon" href="transparente.png" type="image/png">
    <script src="https://sdk.scdn.co/spotify-player.js"></script>
    <script defer src="plugins/fontawesome/js/all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Perfil - Melody Chat</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Roboto', sans-serif;
            background-color: #1f1f1f;
            color: white;
            overflow: hidden;
            overflow-x: hidden;
        }

        .navbar-top img.logo {
            width: 50px;
            height: 50px;
            margin-right: 20px;
            border-radius: 8px;
        }

        .navbar-options {
            display: flex;
            gap: 10px;
        }

        .navbar-options input {
            background-color: #181818;
            font-size: 15px;
            font-weight: bold;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
        }

        .navbar-top .logo {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            margin-right: 20px;
        }

        .navbar-top form {
            display: flex;
            gap: 20px;
            margin-left: auto;
        }

        .navbar-top input {
            padding: 10px;
            background-color: #181818;
            font-size: 15px;
            font-weight: bold;
            border: none;
            color: white;
            cursor: pointer;
        }

        .navbar-top input:hover {
            color: #a0a0a0;
        }

        .logo {
            width: 50px;
            height: 50px;
            margin-right: 10px;
            border-radius: 8px;
        }

        .navbar-options input:hover {
            color: #a0a0a0;
        }

        .navbar-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            background-color: #181818;
            height: 70px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .left-section {
            display: flex;
            align-items: center;
        }

        .right-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .aux {
            display: flex;
            align-items: center;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #181818;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 8px;
            left: -120px;
            top: 40px;
        }

        .dropdown-content a {
            color: white;
            padding: 10px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .dropdown-content a:hover {
            background-color: #3a3a3a;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .card-profile img {
            width: 25%;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto;
            display: block;
        }

        .card-profile h3 {
            text-align: center;
            margin-top: 15px;
            font-size: 20px;
        }

        .card-profile p {
            text-align: center;
            margin: 5px 0;
            color: #a0a0a0;
        }

        .container-profile {
            margin-top: 90px;
            display: flex;
            align-items: flex-start;
            gap: 20px;
            padding: 20px;
        }

        .card-profile {
            flex: 0 0 30%;
            max-height: 450px;
            overflow-y: auto;
            background: linear-gradient(135deg, #2c2c2c, #3a3a3a);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .card-profile img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #555;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .card-profile h3 {
            font-size: 22px;
            font-weight: bold;
            color: #f0f0f0;
            margin: 10px 0 5px 0;
        }

        .card-profile p {
            font-size: 14px;
            color: #bbb;
            margin: 5px 0;
        }

        .card-profile p:first-of-type {
            font-size: 16px;
            font-weight: bold;
            color: #fff;
        }

        .card-profile::-webkit-scrollbar {
            width: 8px;
        }

        .card-profile::-webkit-scrollbar-thumb {
            background: #555;
            border-radius: 4px;
        }

        .card-profile::-webkit-scrollbar-track {
            background: #2c2c2c;
        }

        .functionalities {
            flex: 1;
            padding: 20px;
            border-radius: 10px;
            color: white;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        .card-additional {
            flex: 0 0 10%;
            margin-left: auto;
            padding: 1px;
            margin-top: -40px;
            border-radius: 10px;
            color: white;
        }

        .function {
            position: relative;
            background-color: #2c2c2c;
            border: none;
            color: white;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            text-align: left;
            width: 100%;
            max-width: 250px;
            height: 50px;
        }

        .function:hover {
            background-color: #3a3a3a;
        }

        .function input[type="submit"] {
            flex: 1;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: transparent;
            color: white;
            cursor: pointer;
            text-align: center;
            margin: 0;
        }

        .function i {
            margin-right: 5px;
            font-size: 18px;
            color: white;
        }

        .function.logout {
            background-color: #d9534f;
            color: white;
        }

        .function.logout:hover {
            background-color: #c9302c;
        }

        .function_tiny {
            position: relative;
            background-color: #0c547d;
            border: none;
            color: white;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            text-align: left;
            width: 100%;
            max-width: 200px;
            height: 50px;
        }

        .function_tiny:hover {
            background-color: #07324a;
        }

        .function_tiny input[type="submit"] {
            flex: 1;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: transparent;
            color: white;
            cursor: pointer;
            text-align: center;
            margin: 0;
        }

        .function_tiny i {
            margin-right: 5px;
            font-size: 18px;
            color: white;
        }

        footer {
            background-color: #181818;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .card-additional h3 {
            text-align: center;
            margin-bottom: 15px;
        }

        .card-additional ul {
            list-style: none;
            padding: 0;
        }

        .card-additional li {
            margin: 10px 0;
            color: #a0a0a0;
        }

        .player-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 300px;
            margin: 20px auto;
            padding: 15px;
            border: 1px solid #333;
            border-radius: 10px;
            background: #1e1e1e;
            font-family: Arial, sans-serif;
            color: #fff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .track-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
            text-align: center;
            color: #f5f5f5;
        }

        .artist-name {
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
            color: #bbb;
        }

        .album-cover {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 10px;
            border: 2px solid #444;
        }

        .error-message {
            color: #ff5555;
            font-size: 14px;
            margin-top: 10px;
        }

        .spotify-brand {
            font-size: 17px;
            font-weight: bold;
            color: #1db954;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
            width: 100%;
            text-align: center;
            text-decoration: none;
        }

        .spotify-brand:hover {
            color: #1ed760;
            text-decoration: none;
        }

        .button-container {
            position: absolute;
            top: 5px;
            right: 5px;
            display: flex;
            gap: 5px;
        }

        .button-container .btn-sm {
            padding: 3px 6px;
            line-height: 1;
        }

        .button-container .btn-sm i {
            font-size: 10px;
        }

        .button-container .btn-outline-success,
        .button-container .btn-outline-danger {
            background: transparent;
            color: #fff;
            border-color: #1db954;
        }

        .button-container .btn-outline-danger {
            border-color: #dc3545;
        }

        .button-container .btn-outline-success:hover {
            background: #1db954;
            color: #fff;
            border-color: #1db954;
        }

        .button-container .btn-outline-danger:hover {
            background: #dc3545;
            color: #fff;
            border-color: #dc3545;
        }

        .spotify {
            width: 150px;
            height: auto;
        }
    </style>
</head>

<body>
    <?php require('header.php'); ?>
    <div class="container-profile">
        <div class="card-profile">
            <img src="<?php if (isset($user['path'])) {
                            echo $user['path'];
                        } else { ?>melodychat.png<?php } ?>" alt="Foto de perfil">
            <h3><?php echo $user['util']; ?></h3>
            <form method="post" class="function_tiny">
                <i class="fa-solid fa-user"></i>
                <input type="submit" name="visu_perfil" value="Visualizar Perfil">
            </form>
        </div>
        <div style="display: flex;">
            <div class="functionalities-left functionalities">
                <form action="editar.php" method="get" class="function">
                    <i class="fa-solid fa-gears"></i>
                    <input type="submit" value="Configuração de Dados">
                </form>
                <form action="chats.php" method="get" class="function">
                    <i class="lni lni-message-3-text"></i>
                    <input type="submit" value="Os Meus Chats">
                </form>
                <form action="friends.php" method="get" class="function">
                    <i class="lni lni-user-multiple-4"></i>
                    <input type="submit" value="Lista de Amigos">
                </form>
                <form action="dsession_melodychat.php" method="post" class="function logout">
                    <i class="lni lni-exit"></i>
                    <input type="submit" value="Terminar Sessão">
                </form>
            </div>
            <div class="functionalities-right functionalities">
                <form action="server.php" method="get" class="function">
                    <i class="lni lni-user-multiple-4"></i>
                    <input type="submit" style="margin-left: 5px" value="Os Meus Grupos">
                </form>
                <form action="status.php" method="get" class="function">
                    <i class="lni lni-spotify"></i>
                    <input type="submit" style="margin-left: 5px" value="Status | Spotify">
                </form>
                <form method="post" class="function">
                    <i class="fa-solid fa-users-gear"></i>
                    <input type="submit" style="margin-left: 5px" name="editar_perfil" value="Editar Perfil Público">
                </form>
            </div>
        </div>
        <div class="card-additional">
            <?php if (!isset($_SESSION['flag'])) { ?>
                <form method="post">
                    <div class="btn btn-lg">
                        <button type="submit" name="starter" class="btn btn-success btn-lg" style="text-align: center; align-items: center;"><i class="lni lni-spotify" style="font-size: 25px; margin-right: 5px"></i>Iniciar Sessão com o Spotify</button>
                    </div>
                </form>
            <?php } else { ?>
                <div class="player-container">
                    <div class="button-container">
                        <form method="post">
                            <button type="submit" class="btn btn-outline-success btn-sm" title="Atualizar" name="starter">
                                <i class="fa-solid fa-arrows-rotate"></i>
                            </button>
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Terminar Sessão" name="change">
                                <i class="fa-solid fa-door-open"></i>
                            </button>
                        </form>
                    </div>
                    <a href="https://open.spotify.com/" target="_blank" class="spotify-brand">
                        <img src="Spotify_Full_Logo_RGB_Green.png" class="spotify" alt="Spotify">
                    </a>
                    <img id="album-image" class="album-cover" style="display: none;" />
                    <div id="track-name" class="track-title">Carregando música...</div>
                    <div id="inf">
                        <br><br>
                        <a href="informacao_spotify.php" style="color: #2d5e28;">Entenda como funciona...</a>
                    </div>
                    <div id="artists" class="artist-name"></div>
                    <div id="played-at" class="played-at" style="font-size: 0.9em; color: gray;"></div>
                    <div id="error" class="error-message" style="display: none;">Erro ao carregar informações.</div>
                </div>
                <?php
                if (isset($_SESSION['flag'])) {
                    $email = $_SESSION['email'];
                    $query = "SELECT access_token, token_expires_at FROM faixas WHERE email = '$email'";
                    $result = mysqli_query($con, $query);

                    if ($result) {
                        $row = mysqli_fetch_assoc($result);
                        $access_token = $row['access_token'];
                        $token_expires_at = $row['token_expires_at'];
                        mysqli_free_result($result);
                    } else {
                        echo "Erro na consulta: " . mysqli_error($con);
                        exit;
                    }

                    if (new DateTime() >= new DateTime($token_expires_at)) {
                        $_POST['starter'] = TRUE;
                    }

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/me/player/currently-playing");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        "Authorization: Bearer $access_token"
                    ]);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    $data = json_decode($response, true);

                    if ($data && isset($data['item'])) {
                        $track_name = htmlspecialchars($data['item']['name'], ENT_QUOTES, 'UTF-8');
                        $artist_name = htmlspecialchars($data['item']['artists'][0]['name'], ENT_QUOTES, 'UTF-8');
                        $album_image = htmlspecialchars($data['item']['album']['images'][0]['url'], ENT_QUOTES, 'UTF-8');
                ?>
                        <script>
                            document.getElementById('album-image').src = '<?php echo $album_image; ?>';
                            document.getElementById('album-image').style.display = 'block';
                            document.getElementById('track-name').textContent = '<?php echo $track_name; ?>';
                            document.getElementById('artists').textContent = '<?php echo $artist_name; ?>';
                        </script>
                    <?php
                    } else {
                    ?>
                        <script>
                            document.getElementById('error').textContent = 'Nenhuma música está tocando no momento.';
                        </script>
                <?php
                    }
                }
                ?>
                <script>
                    function fetchCurrentTrack() {
                        fetch('atualizar_musica.php')
                            .then(response => response.json())
                            .then(data => {
                                const albumImage = document.getElementById('album-image');
                                const trackName = document.getElementById('track-name');
                                const artists = document.getElementById('artists');
                                const error = document.getElementById('error');
                                if (data.error) {
                                    trackName.textContent = data.error;
                                    trackName.style.display = 'block';
                                    albumImage.style.display = 'none';
                                    inf.style.display = 'block';
                                    artists.style.display = 'none';
                                    error.style.display = 'none';
                                    return;
                                }
                                if (data && data.item) {
                                    document.getElementById('album-image').src = data.item.album.images[0].url;
                                    document.getElementById('album-image').style.display = 'block';
                                    document.getElementById('track-name').textContent = data.item.name;
                                    inf.style.display = 'none';
                                    document.getElementById('artists').textContent = data.item.artists.map(artist => artist.name).join(', ');
                                } else {
                                    document.getElementById('track-name').textContent = 'Nenhuma música está tocando no momento.';
                                    document.getElementById('album-image').style.display = 'none';
                                    inf.style.display = 'block';
                                }
                            })
                            .catch(error => console.error('Erro ao buscar a música atual:', error));
                    }
                    setInterval(fetchCurrentTrack, 3000);
                    fetchCurrentTrack();
                </script>
            <?php } ?>
        </div>
    </div>
    <?php
    $ano = date("Y");
    ?>
    <footer>
        © <?php echo $ano ?> Melody Chat. Todos os direitos reservados.
    </footer>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>

</html>