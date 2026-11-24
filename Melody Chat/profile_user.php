<?php
session_start();
require("header.php");

if (!isset($_SESSION['visu_perfil'])) {
    header('location:melodychat.php');
    exit();
}

if (!isset($_SESSION['email'])) {
    header('location:melodychat.php');
    exit();
}

// Obtém dados do usuário
$user_id = intval($_SESSION['visu_perfil']);
$sql = "SELECT * FROM melodychat WHERE id = $user_id";
$query = mysqli_query($con, $sql);
if (!$query || mysqli_num_rows($query) === 0) {
    echo 'Erro: Usuário não encontrado';
}
$dados = mysqli_fetch_assoc($query);

// Obtém gêneros musicais
$sql = "SELECT id, generos FROM generos_profile WHERE id_user = $user_id";
$query = mysqli_query($con, $sql);
$genres = [];
if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $genres[] = ['id' => $row['id'], 'generos' => $row['generos']];
    }
}

// Obtém top artistas
$current_month_year = date('Y-m'); // e.g., '2025-04'
$user_email = mysqli_real_escape_string($con, $dados['email']);
$sql = "SELECT artist_1, artist_1_image, artist_2, artist_2_image, artist_3, artist_3_image, 
               artist_4, artist_4_image, artist_5, artist_5_image 
        FROM top_artists 
        WHERE email = '$user_email'";
$query = mysqli_query($con, $sql);
$top_artists = [];
if ($query && mysqli_num_rows($query) > 0) {
    $row = mysqli_fetch_assoc($query);
    foreach (['artist_1', 'artist_2', 'artist_3', 'artist_4', 'artist_5'] as $artist_field) {
        if (!empty($row[$artist_field])) {
            $top_artists[] = [
                'name' => $row[$artist_field],
                'image' => $row[$artist_field . '_image'] ?? 'melodychat.png'
            ];
        }
    }
}

if (isset($_POST['editar_perfil'])) {
    echo "<script>window.location.href='editar_profile.php';</script>";
    exit();
}

if (isset($_POST['chats_amigo'])) {
    $_SESSION['email_friend'] = $dados['email'];
    $_SESSION['foto'] = $dados['path'];
    $_SESSION['bio_amigo'] = $dados['bio'];
    $_SESSION['amigo_id'] = $dados['id'];
    $_SESSION['amigo_nome'] = $dados['util'];
    echo "<script>window.location.href='chats.php';</script>";
    exit();
}

if (isset($_POST['pedido_amigo'])) {
    $id_pedido = $_SESSION['email'];
    $sql = "SELECT email FROM melodychat WHERE id = '" . $_SESSION['visu_perfil'] . "'";
    $query = mysqli_query($con, $sql);
    $email_amigo = mysqli_fetch_assoc($query);
    $id_solicitacao = $email_amigo['email'];

    // Verificar se já existe um pedido de amizade
    $verifica_sql = "SELECT * FROM friends WHERE pedido = '$id_pedido' AND solicitacao = '$id_solicitacao'";
    $resultado_verifica = mysqli_query($con, $verifica_sql);

    if (mysqli_num_rows($resultado_verifica) > 0) {
        $alert = "Pedido de amizade já foi enviado!";
    } else {
        $id_pedido = mysqli_real_escape_string($con, $id_pedido);
        $id_solicitacao = mysqli_real_escape_string($con, $id_solicitacao);
        $sql_insert = "INSERT INTO friends (pedido, solicitacao, estado) VALUES ('$id_pedido', '$id_solicitacao', false)";
        if (mysqli_query($con, $sql_insert)) {
            $alert = "Pedido de amizade enviado com sucesso";
            $sql = "SELECT * FROM melodychat WHERE email = '$id_pedido'";
            $query = mysqli_query($con, $sql);
            $dados_amigo = mysqli_fetch_assoc($query);
            $nome = $dados_amigo['util'];
            $sql = "INSERT INTO notifications (email_user, email_user_causa, link, is_read, titulo, texto) 
                    VALUES ('$id_solicitacao', '$id_pedido', 'friends.php', FALSE, 'Pedido de Amizade', 'Tem um pedido de amizade enviado por: $nome')";
            $query = mysqli_query($con, $sql);
        } else {
            $erro = "Erro ao enviar pedido de amizade";
        }
    }
}

if (isset($_POST['amigo_perfil'])) {
    $_SESSION['visu_perfil'] = $_POST['visu_perfil'];
    echo "<script>window.location.href='profile_user.php';</script>";
    exit();
}


$query = "SELECT SUM(duration_ms) as total_duration FROM playback_history WHERE email = '" . $dados['email'] . "'";
$result = mysqli_query($con, $query);

$row = mysqli_fetch_assoc($result);

if ($row && isset($row['total_duration'])) {
    $minutos = round($row['total_duration'] / 60000);
} else {
    echo "Não foram encontradas músicas ou o cálculo falhou.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script defer src="plugins/fontawesome/js/all.min.js"></script>
    <link rel="icon" href="transparente.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.lineicons.com/5.0/lineicons.css" rel="stylesheet" />
    <title><?php echo $dados['util'] ?></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Roboto', sans-serif;
            background-color: #1f1f1f;
            color: white;
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

        #main {
            margin-top: 90px;
            padding: 30px;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        footer {
            background-color: #1f1f1f;
            color: #ffffff;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .dropdown {
            position: relative;
            display: inline-block;
            margin: 0 5px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #181818;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 8px;
            right: 0;
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

        .profile-container {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .profile-picture {
            width: 200px;
            height: 200px;
            border-radius: 15px;
            object-fit: cover;
            border: 2px solid #3a3a3a;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .profile-name {
            font-size: 30px;
            font-weight: bold;
            color: #ffffff;
            margin-top: 100px;
        }

        .separator {
            width: 100%;
            height: 1px;
            background-color: #3a3a3a;
            margin: 20px 0;
        }

        .info-container {
            display: flex;
            gap: 20px;
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }

        .bio-container {
            background-color: #181818;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            min-height: 200px;
            display: flex;
            flex-direction: column;
        }

        .bio-title-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .bio-title {
            font-size: 20px;
            font-weight: bold;
            color: #ffffff;
        }

        .bio-content {
            font-size: 16px;
            color: #a0a0a0;
            line-height: 1.5;
            text-align: left;
            margin: 0;
            padding: 0;
            text-indent: 0;
            white-space: normal;
            display: block;
            overflow-wrap: break-word;
            flex-grow: 1;
        }

        .bio-text {
            white-space: pre-wrap;
            text-align: left;
            margin: 0;
            padding: 0;
        }

        .bio-placeholder {
            font-size: 16px;
            color: #a0a0a0;
            font-style: italic;
        }

        .genres-container {
            background-color: #181818;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            min-height: 200px;
            display: flex;
            flex-direction: column;
        }

        .friends-container {
            background-color: #181818;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            min-height: 200px;
            display: flex;
            flex-direction: column;
        }

        .genres-title-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .friends-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .genres-content {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            flex-grow: 1;
        }

        .genre-item {
            background-color: #2a2a2a;
            padding: 5px 27px;
            height: 35px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #ffffff;
        }

        .genres-placeholder {
            font-size: 16px;
            color: #a0a0a0;
            font-style: italic;
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

        .function_tiny_success {
            position: relative;
            background-color: #0e8f31;
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

        .function_tiny_success:hover {
            background-color: #084a1a;
        }

        .function_tiny_success input[type="submit"] {
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

        .function_tiny_success i {
            margin-right: 5px;
            font-size: 18px;
            color: white;
        }

        @media (max-width: 768px) {
            .info-container {
                flex-direction: column;
            }

            .bio-container,
            .genres-container {
                width: 100%;
            }
        }

        .friends-content {
            display: flex;
            overflow-x: auto;
            gap: 20px;
            padding-bottom: 10px;
            white-space: nowrap;
            scrollbar-width: thin;
            scrollbar-color: #3a3a3a #181818;
        }

        .friends-content::-webkit-scrollbar {
            height: 8px;
        }

        .friends-content::-webkit-scrollbar-track {
            background: #181818;
        }

        .friends-content::-webkit-scrollbar-thumb {
            background: #3a3a3a;
            border-radius: 4px;
        }

        .friends-content::-webkit-scrollbar-thumb:hover {
            background: #a0a0a0;
        }

        .friend-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 80px;
            text-align: center;
            flex-shrink: 0;
        }

        .friend-button {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .friend-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3a3a3a;
            transition: transform 0.2s, border-color 0.2s;
        }

        .friend-button:hover .friend-photo {
            transform: scale(1.1);
            border-color: #a0a0a0;
        }

        .friend-name {
            font-size: 14px;
            color: #ffffff;
            white-space: normal;
            max-width: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .friends-placeholder {
            font-size: 16px;
            color: #a0a0a0;
            font-style: italic;
        }

        .top-artists-container {
            background-color: #181818;
            padding: 10px;
            border-radius: 10px;
            width: 100%;
            min-height: 300px;
            display: flex;
            flex-direction: column;
        }

        .top-artists-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .top-artists-content {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding-bottom: 10px;
            white-space: nowrap;
            scrollbar-width: thin;
            scrollbar-color: #3a3a3a #181818;
            justify-content: space-around;
            min-width: 100%;
        }

        .top-artists-content::-webkit-scrollbar {
            height: 8px;
        }

        .top-artists-content::-webkit-scrollbar-track {
            background: #181818;
        }

        .top-artists-content::-webkit-scrollbar-thumb {
            background: #3a3a3a;
            border-radius: 4px;
        }

        .top-artists-content::-webkit-scrollbar-thumb:hover {
            background: #a0a0a0;
        }

        .artist-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 160px;
            text-align: center;
            flex-shrink: 0;
        }

        .artist-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3a3a3a;
            transition: transform 0.2s, border-color 0.2s;
        }

        .artist-name {
            font-size: 14px;
            color: #ffffff;
            white-space: normal;
            max-width: 160px;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 5px;
        }

        .artists-placeholder {
            font-size: 16px;
            color: #a0a0a0;
            font-style: italic;
        }
    </style>
</head>

<body>
    <span class="loader"></span>
    <div id="main">
        <div class="profile-container">
            <img src="<?php echo !empty($dados['path']) ? $dados['path'] : 'melodychat.png'; ?>" alt="Foto de Perfil" class="profile-picture">
            <div class="profile-info">
                <div class="profile-name"><?php echo htmlspecialchars($dados['util']); ?></div>
                <?php if ($_SESSION['visu_perfil'] == $_SESSION['id']) { ?>
                    <form method="post" class="function_tiny">
                        <i class="fa-solid fa-pencil"></i>
                        <input type="submit" name="editar_perfil" value="Editar Perfil">
                    </form>
                    <?php } else {
                    $friendsQuery = "SELECT * FROM friends WHERE estado = TRUE AND (solicitacao = '" . $_SESSION['email'] . "' OR pedido = '" . $_SESSION['email'] . "') AND (solicitacao = '" . $dados['email'] . "' OR pedido = '" . $dados['email'] . "')";
                    $friendsResultaa = mysqli_query($con, $friendsQuery);
                    $friends = mysqli_fetch_assoc($friendsResultaa);
                    if (mysqli_num_rows($friendsResultaa) > 0) { ?>
                        <form method="post" class="function_tiny">
                            <i class="fa-solid fa-comment"></i>
                            <input type="submit" name="chats_amigo" value="Mandar Mensagem">
                        </form>
                    <?php } else { ?>
                        <form method="post" class="function_tiny_success">
                            <i class="fa-solid fa-plus"></i>
                            <input type="submit" name="pedido_amigo" value="Pedido de Amizade">
                        </form>
                <?php }
                } ?>
                <div><span class="minutos" style="color: green; font-weight: bold;"><?php echo $minutos ?? '0'; ?> minutos </span> Acumulados! </div>
            </div>
        </div>
        <?php if (isset($alert)) { ?>
            <br>
            <div class='alert alert-success' style="text-align: center;" role='alert'>
                <?php echo $alert; ?>
            </div>
        <?php } ?>
        <div class="separator"></div>
        <div class="info-container">
            <div class="bio-container">
                <div class="bio-title">Biografia</div>
                <div class="bio-content">
                    <?php
                    if (!empty($dados['bio'])) {
                        $bio = preg_replace('/^[\s\x{00A0}\x{200B}\x{200E}\x{200F}\x{FEFF}]+/u', '', $dados['bio']);
                        echo '<div class="bio-text">' . htmlspecialchars($bio) . '</div>';
                    } else {
                        echo '<span class="bio-placeholder">🕸🕸🕷🕸🕸 (Vazio)</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="genres-container">
                <div class="genres-title-container">
                    <span class="bio-title">Gêneros Musicais (Escolhidos)</span>
                </div>
                <div class="genres-content" id="genres-content">
                    <?php
                    if (!empty($genres)) {
                        foreach ($genres as $genre) {
                            echo '<div class="genre-item" data-id="' . $genre['id'] . '">' .
                                htmlspecialchars($genre['generos']) . '</div>';
                        }
                    } else {
                        echo '<span class="genres-placeholder">Nenhum gênero musical adicionado</span>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="separator"></div>
        <div class="top-artists-container">
            <div class="top-artists-title" style="margin-left: 10px; margin-top:10px;">
                <span class="bio-title">Artistas Favoritos</span>
            </div>
            <div class="top-artists-content" id="top-artists-content">
                <?php
                if (!empty($top_artists)) {
                    foreach ($top_artists as $artist) {
                ?>
                        <div class="artist-item">
                            <img src="<?php echo htmlspecialchars($artist['image']); ?>" alt="Foto do Artista" class="artist-photo">
                            <span class="artist-name"><?php echo htmlspecialchars($artist['name']); ?></span>
                        </div>
                <?php
                    }
                } else {
                    echo '<span class="artists-placeholder">Nenhum dado de artistas disponível</span>';
                }
                ?>
            </div>
        </div>
        <div class="separator"></div>
        <div class="friends-container">
            <div class="friends-title">
                <span class="bio-title">Amigos</span>
            </div>
            <div class="friends-content" id="friends-content">
                <?php
                $sql = "SELECT * FROM friends WHERE estado = TRUE AND (solicitacao = '" . mysqli_real_escape_string($con, $dados['email']) . "' OR pedido = '" . mysqli_real_escape_string($con, $dados['email']) . "')";
                $query = mysqli_query($con, $sql);
                if (mysqli_num_rows($query) > 0) {
                    while ($row = mysqli_fetch_assoc($query)) {
                        $friend_email = ($row['solicitacao'] === $dados['email']) ? $row['pedido'] : $row['solicitacao'];
                        $friend_sql = "SELECT id, util, path FROM melodychat WHERE email = '" . mysqli_real_escape_string($con, $friend_email) . "'";
                        $friend_query = mysqli_query($con, $friend_sql);
                        $friend_data = mysqli_fetch_assoc($friend_query);
                        if ($friend_data) {
                            $friend_id = $friend_data['id'];
                            $friend_name = htmlspecialchars($friend_data['util']);
                            $friend_photo = !empty($friend_data['path']) ? $friend_data['path'] : 'melodychat.png';
                ?>
                            <form method="post" class="friend-item">
                                <input type="hidden" name="visu_perfil" value="<?php echo $friend_id; ?>">
                                <button type="submit" class="friend-button" name="amigo_perfil">
                                    <img src="<?php echo $friend_photo; ?>" alt="Foto do Amigo" class="friend-photo">
                                    <span class="friend-name"><?php echo $friend_name; ?></span>
                                </button>
                            </form>
                <?php
                        }
                    }
                } else {
                    echo '<span class="friends-placeholder">Nenhum amigo adicionado</span>';
                }
                ?>
            </div>
        </div>
    </div>
    <br><br><br>
    <?php
    $ano = date("Y");
    ?>
    <footer>
        © <?php echo $ano ?> Melody Chat. Todos os direitos reservados.
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Nenhum JavaScript necessário para esta página, já que é apenas visualização
    </script>
</body>

</html>