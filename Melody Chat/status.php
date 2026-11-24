<?php
session_start();
require('ligacao_melody.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['email'])) {
    header('location:login.php');
    exit();
}


$email = $_SESSION['email'];
$query = "SELECT access_token FROM faixas WHERE email = '$email'";
$result = mysqli_query($con, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $access_token = $row['access_token'];
} else {
    echo "Erro ao obter o token de acesso!";
    exit;
}

// Obter informações do perfil do Spotify
$profile_ch = curl_init();
curl_setopt($profile_ch, CURLOPT_URL, "https://api.spotify.com/v1/me");
curl_setopt($profile_ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $access_token"
]);
curl_setopt($profile_ch, CURLOPT_RETURNTRANSFER, true);
$profile_response = curl_exec($profile_ch);
curl_close($profile_ch);

$profile_data = json_decode($profile_response, true);
$spotify_name = $profile_data['display_name'] ?? 'Usuário';
$spotify_image = $profile_data['images'][0]['url'] ?? 'melodychat.png';

// Obter o Top Artistas
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/me/top/artists?limit=3");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $access_token"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$artists = [];
$data = json_decode($response, true);
if (isset($data['items'])) {
    foreach ($data['items'] as $artist) {
        $artists[] = [
            'name' => $artist['name'],
            'image' => $artist['images'][0]['url'] ?? 'default-artist.png',
        ];
    }
}

// Obter o Top Faixas
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/me/top/tracks?limit=5");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $access_token"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response_tracks = curl_exec($ch);
curl_close($ch);

$tracks = [];
$track_data = json_decode($response_tracks, true);
if (isset($track_data['items'])) {
    foreach ($track_data['items'] as $track) {
        $tracks[] = [
            'name' => $track['name'],
            'artist' => $track['artists'][0]['name'],
            'image' => $track['album']['images'][0]['url'] ?? 'default-track.png',
        ];
    }
}


$headers = ["Authorization: Bearer $access_token"];



// Consultar top faixas
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/me/top/tracks?time_range=short_term&limit=5");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$top_tracks = json_decode(curl_exec($ch), true);
curl_close($ch);

// Consultar top artistas
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/me/top/artists?time_range=short_term&limit=5");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$top_artists = json_decode(curl_exec($ch), true);
curl_close($ch);


// Consultar histórico recente
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.spotify.com/v1/me/player/recently-played?limit=50");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $access_token"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$recent_tracks = json_decode($response, true);

// Verificar se temos itens
if (!isset($recent_tracks['items'])) {
    header('location:auth.php');
    exit();
}

$current_month_start = strtotime('first day of this month');  // Primeiro dia deste mês
$current_month_end = strtotime('last day of this month');    // Último dia deste mês

foreach ($recent_tracks['items'] as $item) {
    $played_at = strtotime($item['played_at']);  // Data em que a faixa foi tocada

    // Verifica se a faixa foi tocada dentro do mês atual
    if ($played_at >= $current_month_start && $played_at <= $current_month_end) {
        // Formatar a data do played_at
        $played_at_formatted = date('Y-m-d H:i:s', $played_at);

        // Verificar se a faixa já foi registrada para esse email
        $query_check = "SELECT * FROM playback_history WHERE played_at = '$played_at_formatted' AND email = '" . $_SESSION['email'] . "'";
        $result_check = mysqli_query($con, $query_check);

        if (mysqli_num_rows($result_check) == 0) {
            // Se não existe, inserir o registro
            $track_name = mysqli_real_escape_string($con, $item['track']['name']);
            $artist_name = mysqli_real_escape_string($con, $item['track']['artists'][0]['name']);
            $track_duration_ms = $item['track']['duration_ms'];

            // Inserir na base de dados
            $query_insert = "INSERT INTO playback_history (track_name, artist_name, played_at, duration_ms, email) 
                             VALUES ('$track_name', '$artist_name', '$played_at_formatted', '$track_duration_ms', '" . $_SESSION['email'] . "')";
            if (mysqli_query($con, $query_insert)) {
            } else {
                echo "Erro ao inserir dados: " . mysqli_error($con);
            }






            $query = "SELECT SUM(duration_ms) as total_duration FROM playback_history WHERE email = '" . $_SESSION['email'] . "'";
            $result = mysqli_query($con, $query);


            $row = mysqli_fetch_assoc($result);

            if ($row && isset($row['total_duration'])) {
                $_SESSION['total_duration_minutes'] = round($row['total_duration'] / 60000);
?><script>
                    console.log('Calculo concluido');
                </script><?php
                        } else {
                            echo "Não foram encontradas músicas ou o cálculo falhou.";
                            ?><script>
                    console.log('Erro no calculo');
                </script><?php
                        }
                            ?><script>
                console.log('Teste');
            </script><?php
                    }
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
    <title>Stats - Melody Chat</title>
    <style>
        .btn:hover {
            background-color: #1DB954;
            color: #ffffff;
        }



        .profile-pic {
            border-radius: 50%;
            margin: 20px 0;
            width: 120px;
            height: 120px;
            object-fit: cover;
        }


        .navbar-top {
            width: 100%;
            height: 70px;
            background-color: #181818;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .navbar-top img.logo {
            width: 120px;
            height: 35px;
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


        .navbar-top {
            width: 100%;
            height: 70px;
            background-color: #181818;
            /* Cor da barra */
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
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
            /* Cor mais clara ao passar o mouse */
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

        .logo {
            width: 50px;
            height: 50px;
            margin-right: 10px;
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

        .navbar-options input:hover {
            color: #a0a0a0;
        }

        .navbar-options input:hover {
            color: #a0a0a0;
        }

        .dropdown {
            position: relative;
            display: inline-block;
            margin-left: 30px;
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
            /* Move o menu para o lado esquerdo */
            top: 40px;
            /* Ajusta para não sobrepor o botão */
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

        /* Exibir dropdown ao passar o mouse no botão */
        .dropdown:hover .dropdown-content {
            display: block;
        }

        .card-profile img {
            width: 25%;
            /* Ajuste de largura */
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
            /* Alinha os itens ao topo */
            gap: 20px;
            /* Espaçamento entre os elementos */
            padding: 20px;
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
            /* Fundo mais escuro */
            font-family: Arial, sans-serif;
            color: #fff;
            /* Texto branco */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5);
            /* Leve sombra */
        }

        .track-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
            text-align: center;
            color: #f5f5f5;
            /* Título mais claro */
        }

        .artist-name {
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
            color: #bbb;
            /* Cor sutil para o artista */
        }

        .album-cover {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 10px;
            border: 2px solid #444;
            /* Borda discreta no álbum */
        }

        .error-message {
            color: #ff5555;
            /* Vermelho para mensagens de erro */
            font-size: 14px;
            margin-top: 10px;
        }


        .section {
            margin-top: 20px;
        }

        .section h2 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #f5f5f5;
        }

        .section ul {
            list-style: none;
            padding: 0;
        }

        .section li {
            margin: 10px 0;
            font-size: 14px;
            color: #b3b3b3;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #fff;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .spotify-profile img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            margin-top: 20px;
        }

        .spotify-profile h1 {
            margin-top: 10px;
        }

        .top-section {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-top: 30px;
        }

        .top-tracks,
        .top-artists {
            text-align: left;
        }

        .track,
        .artist {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 10px;
        }

        .track img,
        .artist img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
        }

        .track p,
        .artist p {
            margin: 0;
        }

        .resumo-mensal {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #1e1e1e;
            /* Fundo escuro */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            /* Sombra mais evidente */
            color: #f5f5f5;
            /* Texto claro */
        }

        .resumo-mensal h1 {
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            color: #fff;
        }

        .resumo-mensal .conteudo {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .resumo-mensal .top-artistas,
        .resumo-mensal .top-musicas {
            flex: 1;
            background: #2a2a2a;
            /* Fundo das divisões */
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            /* Sombra leve */
            transition: transform 0.2s, background 0.2s;
        }

        .resumo-mensal ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .resumo-mensal li {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 15px;
            background: #3a3a3a;
            /* Fundo para cada item */
            padding: 10px;
            border-radius: 8px;
            transition: transform 0.2s, background 0.2s;
        }

        .resumo-mensal img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #555;
        }

        .resumo-mensal .track-name,
        .resumo-mensal .artist-name {
            font-size: 16px;
            font-weight: bold;
            color: #f5f5f5;
        }

        .resumo-mensal small {
            font-size: 12px;
            color: #bbb;
        }


        .tempo-escutado {
            font-size: 24px;
            /* Tamanho grande */
            font-weight: bold;
            /* Negrito */
            color: #fff;
            /* Branco para o texto padrão */
            text-align: center;
            /* Centralizado */
        }

        .tempo-escutado .minutos {
            color: #4caf50;
            /* Verde para os minutos */
        }

        .menssagem {
            font-size: 12px;
            /* Tamanho do texto pequeno */
            color: #777777;
            /* Cor cinza clara */
            font-style: italic;
            /* Estilo de texto itálico */
            margin-top: 10px;
            /* Espaçamento superior */
            margin-bottom: 10px;
            /* Espaçamento inferior */
        }

        .spotify {
            width: 150px;
            /* Ajuste o tamanho conforme necessário */
            height: auto;
            /* Mantém a proporção da imagem */
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
    </style>
</head>

<body>

    <div class="navbar-top">
        <div class="left-section">

            <a href="https://open.spotify.com/" target="_blank" class="spotify-brand">
                <img src="Spotify_Full_Logo_RGB_White.png" class="spotify" alt="Spotify">
            </a>
        </div>
        <div class="dropdown">
            <input type="button" name="def" value="☰">
            <div class="dropdown-content">
                <a href="melodychat.php"><i class="lni lni-home-2" style="margin-right: 5px;"></i>Inicio</a>
                <a href="profile.php"><i class="lni lni-user-4" style="margin-right: 5px;"></i>Profile</a>
            </div>
        </div>
    </div>


    <br><br><br><br>
    <div class="container">
        <?php
        // Verifica se há dados suficientes no geral
        if (!isset($recent_tracks['items']) || empty($tracks) && empty($artists) && empty($top_tracks['items']) && empty($top_artists['items'])) {
            echo "<div class='error-message'>Você é novo no Spotify ou ainda não tem dados suficientes para exibir. Continue ouvindo para ver as suas estatísticas!</div>";
        } else {
        ?>
            <div class="spotify-profile">
                <img src="<?php echo $spotify_image; ?>" alt="Perfil do Spotify">
                <h1><?php echo htmlspecialchars($spotify_name, ENT_QUOTES, 'UTF-8'); ?></h1>
            </div>

            <div class="top-section">
                <div class="top-tracks">
                    <h2>Top Faixas</h2>
                    <?php if (!empty($tracks)): ?>
                        <?php foreach ($tracks as $track): ?>
                            <div class="track">
                                <img src="<?php echo $track['image']; ?>" alt="Capa da Faixa">
                                <p><?php echo htmlspecialchars($track['name'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($track['artist'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="error-message">Nenhuma faixa disponível. Continue ouvindo para ver as suas músicas favoritas!</p>
                    <?php endif; ?>
                </div>

                <div class="top-artists">
                    <h2>Top Artistas</h2>
                    <?php if (!empty($artists)): ?>
                        <?php foreach ($artists as $artist): ?>
                            <div class="artist">
                                <img src="<?php echo $artist['image']; ?>" alt="Imagem do Artista">
                                <p><?php echo htmlspecialchars($artist['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="error-message">Nenhum artista disponível. Continue ouvindo para descobrir os seus artistas favoritos!</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="resumo-mensal">
                <h1>Resumo Mensal</h1>
                <?php if (isset($_SESSION['total_duration_minutes']) && $_SESSION['total_duration_minutes'] > 0): ?>
                    <p class="tempo-escutado">
                        <span class="minutos"><?php echo $_SESSION['total_duration_minutes']; ?> minutos </span> Acumulados!
                        <br>
                    </p>
                <?php else: ?>
                    <p class="tempo-escutado">Nenhum tempo de escuta registrado ainda. Continue ouvindo!</p>
                <?php endif; ?>

                <div class="conteudo">
                    <!-- Top Artistas -->
                    <div class="top-artistas">
                        <h2>Top 5 Artistas</h2>
                        <?php if (!empty($top_artists['items'])): ?>
                            <ul class="artistas-lista">
                                <?php foreach ($top_artists['items'] as $artist): ?>
                                    <li>
                                        <img src="<?= $artist['images'][0]['url'] ?>" alt="<?= $artist['name'] ?>">
                                        <span><?= $artist['name'] ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="error-message">Nenhum artista disponível para o resumo mensal.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Top 5 Músicas -->
                    <div class="top-musicas">
                        <h2>Top 5 Músicas</h2>
                        <?php if (!empty($top_tracks['items'])): ?>
                            <ul class="faixas-lista">
                                <?php for ($i = 0; $i < min(5, count($top_tracks['items'])); $i++): ?>
                                    <li>
                                        <img src="<?= $top_tracks['items'][$i]['album']['images'][0]['url'] ?>" alt="<?= $top_tracks['items'][$i]['name'] ?>">
                                        <div>
                                            <span><?= $top_tracks['items'][$i]['name'] ?></span>
                                            <small><?= $top_tracks['items'][$i]['artists'][0]['name'] ?></small>
                                        </div>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        <?php else: ?>
                            <p class="error-message">Nenhuma música disponível para o resumo mensal.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>






    <!-- JavaScript (Opcional) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>

</html>