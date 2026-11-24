<?php
session_start();
require('ligacao_melody.php');
require('spotify_config.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['email'])) {
    header('location:login.php');
    exit();
}


if (!isset($_SESSION['access_token'])) {
    header('Location: ligacao_spotify_status.php');
    exit();
}


$accessToken = $_SESSION['access_token'];

function getSpotifyData($url, $accessToken) {
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken",
        ],
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        die("Erro ao conectar com o Spotify: $error");
    }

    return json_decode($response, true);
}

// Obter dados do usuário
$userProfile = getSpotifyData('https://api.spotify.com/v1/me', $accessToken);
$topArtists = getSpotifyData('https://api.spotify.com/v1/me/top/artists?limit=3', $accessToken);
$topTracks = getSpotifyData('https://api.spotify.com/v1/me/top/tracks?limit=5', $accessToken);

if (isset($userProfile['error']) || isset($topArtists['error']) || isset($topTracks['error'])) {
    die("Erro ao obter dados do Spotify. Por favor, tente novamente.");
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
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Roboto', sans-serif;
            background-color: #1f1f1f;
            color: white;
        }





        .followers {
            font-size: 18px;
            color: #1DB954;
            font-weight: bold;
        }

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
    left: -120px; /* Move o menu para o lado esquerdo */
    top: 40px; /* Ajusta para não sobrepor o botão */
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
            width: 25%; /* Ajuste de largura */
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
    align-items: flex-start; /* Alinha os itens ao topo */
    gap: 20px; /* Espaçamento entre os elementos */
    padding: 20px;
}


/* Estilo do card-profile */
.card-profile {
    flex: 0 0 30%; /* Ocupar exatamente 30% do espaço */
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

/* Scroll personalizado (se necessário) */
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
    flex: 1; /* Ocupa o espaço restante */
    padding: 20px;
    border-radius: 10px;
    color: white;
    min-height: 400px; /* Para alinhar com o card-profile */
    display: flex; /* Para organizar os itens */
    flex-direction: column; /* Empilhar os itens verticalmente */
    gap: 20px; /* Espaçamento entre os itens */
}



.card-additional {
    flex: 0 0 20%; /* Ocupar uma largura fixa de 20% */
    margin-left: auto; /* Mantém no canto direito */
    padding: 20px;
    margin-top: -25px;
    border-radius: 10px;
    color: white;
}



    .function {
        position: relative; /* Para que o input possa preencher a área */
        background-color: #2c2c2c;
        border: none;
        color: white;
        padding: 15px;
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
        height: 60px; /* Define uma altura fixa */
    }

    .function:hover {
        background-color: #3a3a3a;
    }

    .function input[type="submit"] {
    flex: 1; /* Faz o botão ocupar o máximo de espaço horizontal possível */
    padding: 10px;
    font-size: 16px; /* Ajusta o tamanho da fonte */
    border: none;
    border-radius: 5px;
    background-color:transparent;
    color: white; /* Cor do texto */
    cursor: pointer;
    text-align: center;
}


.function i {
    margin-right: 15px;
    font-size: 18px; /* Tamanho dos ícones */
    color: white;
}

    .function.logout {
        background-color: #d9534f;
        color: white;
    }

    .function.logout:hover {
        background-color: #c9302c;
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
        background: #1e1e1e; /* Fundo mais escuro */
        font-family: Arial, sans-serif;
        color: #fff; /* Texto branco */
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5); /* Leve sombra */
    }
    .track-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 3px;
        text-align: center;
        color: #f5f5f5; /* Título mais claro */
    }
    .artist-name {
        font-size: 14px;
        margin-bottom: 10px;
        text-align: center;
        color: #bbb; /* Cor sutil para o artista */
    }
    .album-cover {
        width: 100%;
        height: auto;
        border-radius: 10px;
        margin-bottom: 10px;
        border: 2px solid #444; /* Borda discreta no álbum */
    }
    .error-message {
        color: #ff5555; /* Vermelho para mensagens de erro */
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





    </style>
    </head>
<body>

<div class="navbar-top">
        <div class="left-section">
            <img src="https://imageshack.com/i/pmmxUi4hp" alt="Logo" class="logo">
            <span>Status - Melody Chat</span>
        </div>
        <div class="dropdown">
                <input type="button" name="def" value="☰">
                <div class="dropdown-content">
                    <a href="melodychat.php"><i class="lni lni-home-2" style="margin-right: 5px;"></i>Inicio</a>
                    <a href="profile.php"><i class="lni lni-user-4" style="margin-right: 5px;"></i>Profile</a>
                </div>
            </div>
    </div>


    <div class="container">
        <img src="<?= htmlspecialchars($profilePic) ?>" alt="Foto de Perfil" class="profile-pic">
        <h1>Olá, <?= htmlspecialchars($displayName) ?>!</h1>
        <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
        <p class="followers"><strong>Seguidores:</strong> <?= htmlspecialchars($followers) ?></p>

        <div class="section">
            <h2>Top 3 Artistas Mais Ouvidos</h2>
            <ul>
                <?php foreach ($topArtists['items'] as $artist): ?>
                    <li>
                        <img src="<?= htmlspecialchars($artist['images'][0]['url'] ?? 'default-avatar.png') ?>" alt="Artista">
                        <?= htmlspecialchars($artist['name']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="section">
            <h2>Top 5 Faixas Mais Ouvidas</h2>
            <ul>
                <?php foreach ($topTracks['items'] as $track): ?>
                    <li>
                        <img src="<?= htmlspecialchars($track['album']['images'][0]['url'] ?? 'default-avatar.png') ?>" alt="Faixa">
                        <?= htmlspecialchars($track['name']) ?> - <?= htmlspecialchars($track['artists'][0]['name']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <a href="logout.php" class="btn">Sair</a>
    </div>





    <!-- JavaScript (Opcional) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>