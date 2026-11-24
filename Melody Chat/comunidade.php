<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao.php');

// Handle profile picture POST
if (isset($_POST['view_profile'])) {
    $_SESSION['visu_perfil'] = $_POST['util_destino'];
    echo "<script>window.location.href='profile_user.php';</script>";
}

// Handle search POST
$search_results = [];
$total_results = 0;
$results_per_page = 6;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $results_per_page;

if (isset($_POST['search']) || isset($_GET['search_query'])) {
    $search_query = isset($_POST['search_query']) ? mysqli_real_escape_string($con, $_POST['search_query']) : mysqli_real_escape_string($con, $_GET['search_query']);
    $_SESSION['search_query'] = $_POST['search_query'];
    // Count total results for pagination
    $count_sql = "
        SELECT COUNT(DISTINCT m.id) as total 
        FROM melodychat m
        LEFT JOIN generos_profile gp ON gp.id_user = m.id
        LEFT JOIN top_artists ta ON ta.email = m.email
        WHERE (gp.generos LIKE '%$search_query%'
            OR ta.artist_1 LIKE '%$search_query%'
            OR ta.artist_2 LIKE '%$search_query%'
            OR ta.artist_3 LIKE '%$search_query%'
            OR ta.artist_4 LIKE '%$search_query%'
            OR ta.artist_5 LIKE '%$search_query%')
        AND m.id != '" . $_SESSION['id'] . "'
        AND (ta.month_year = (SELECT MAX(month_year) FROM top_artists WHERE email = m.email) OR ta.month_year IS NULL)
    ";
    $count_result = mysqli_query($con, $count_sql);
    $total_results = mysqli_fetch_assoc($count_result)['total'];

    // Fetch results for current page
    $search_sql = "
        SELECT DISTINCT m.id, m.util, m.path,
            CASE 
                WHEN ta.artist_1 LIKE '%$search_query%' THEN ta.artist_1
                WHEN ta.artist_2 LIKE '%$search_query%' THEN ta.artist_2
                WHEN ta.artist_3 LIKE '%$search_query%' THEN ta.artist_3
                WHEN ta.artist_4 LIKE '%$search_query%' THEN ta.artist_4
                WHEN ta.artist_5 LIKE '%$search_query%' THEN ta.artist_5
                ELSE NULL
            END AS matched_artist
        FROM melodychat m
        LEFT JOIN generos_profile gp ON gp.id_user = m.id
        LEFT JOIN top_artists ta ON ta.email = m.email
        WHERE (gp.generos LIKE '%$search_query%'
            OR ta.artist_1 LIKE '%$search_query%'
            OR ta.artist_2 LIKE '%$search_query%'
            OR ta.artist_3 LIKE '%$search_query%'
            OR ta.artist_4 LIKE '%$search_query%'
            OR ta.artist_5 LIKE '%$search_query%')
        AND m.id != '" . $_SESSION['id'] . "'
        AND (ta.month_year = (SELECT MAX(month_year) FROM top_artists WHERE email = m.email) OR ta.month_year IS NULL)
        LIMIT $results_per_page OFFSET $offset
    ";
    $search_results = mysqli_query($con, $search_sql);
}

if (isset($_POST['chats'])) {
    header('location:chats.php');
    exit();
}

if (isset($_POST['friends'])) {
    header('location:comunidade.php');
    exit();
}

if (isset($_POST['group'])) {
    header('location:server.php');
    exit();
}

if (isset($_POST['logout'])) {
    header('location:dsession_melodychat.php');
    exit();
}

if (isset($_POST['editar'])) {
    header('location:editar.php');
    exit();
}

if (isset($_POST['admin'])) {
    header('location:admin.php');
    exit();
}

if (isset($_POST['profile'])) {
    header('location:profile.php');
    exit();
}

if (!isset($_SESSION['id'])) {
    header('location:dsession_melodychat.php');
    exit();
}

$est = "SELECT estado FROM manutencao WHERE id = 1;";
$estado = mysqli_query($con, $est);
$row = mysqli_fetch_assoc($estado);

$sql12 = "select * from melodychat where email = '" . $_SESSION['email'] . "';";
$result3 = mysqli_query($con, $sql12);
$rowe = mysqli_fetch_assoc($result3);

if (isset($_POST['contact'])) {
    header('location:contact.php');
    exit();
}

$amizade = "select * from melodychat where email = '" . $_SESSION['email'] . "'";
$resultado = mysqli_query($con, $amizade);
$pedido = mysqli_fetch_assoc($resultado);

if (isset($_POST['adicionar'])) {
    $id_pedido = $pedido['email'];
    $id_solicitacao = $_POST['util_destino'];
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
        } else {
            $erro = "Erro ao enviar pedido de amizade";
        }
    }
}

// Fetch recommended users (3 random users excluding the logged-in user)
$recommended_sql = "SELECT id, util, path FROM melodychat WHERE id != '" . $_SESSION['id'] . "' ORDER BY RAND() LIMIT 3";
$recommended_users = mysqli_query($con, $recommended_sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="transparente.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script defer src="plugins/fontawesome/js/all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.lineicons.com/5.0/lineicons.css" rel="stylesheet" />
    <title>Melody Chat</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: auto;
            font-family: 'Arial', sans-serif;
            background-image: url('fundo.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
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
            padding: 20px;
            width: 100%;
            min-height: calc(100vh - 110px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
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

        .dropdown {
            margin-left: auto;
        }

        .title {
            position: absolute;
            top: 50px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 60px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
            margin-top: 20px;
        }

        .background-img {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: blur(8px);
            z-index: -1;
        }

        .subtitle {
            position: absolute;
            top: 150px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 30px;
            font-weight: normal;
            color: #d3d3d3;
            text-align: center;
            margin-top: 50px;
        }

        .text-box {
            background-color: #1f1f1f;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            margin: 20px auto;
            font-size: 16px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .info-columns {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
        }

        .info-box {
            background-color: #1f1f1f;
            padding: 20px;
            border-radius: 10px;
            width: 30%;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .info-box h3 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .info-box p {
            font-size: 16px;
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

        .table-container {
            width: 80%;
            margin: 20px auto;
            border-radius: 10px;
            overflow: hidden;
            background-color: #333333;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-height: 400px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 15px;
            text-align: center;
            color: #ffffff;
        }

        tr:nth-child(even) {
            background-color: #2c2c2c;
        }

        tr:hover {
            background-color: #444444;
        }

        td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #555555;
        }

        .actions button {
            padding: 8px 12px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .button-friend-request {
            background-color: #4CAF50;
            color: white;
        }

        .button-report {
            background-color: #f44336;
            color: white;
        }

        .actions button:hover {
            opacity: 0.8;
        }

        /* New styles for recommendation card */
        .recommendation-card {
            background-color: #1f1f1f;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .card-top-bar {
            background-color: #181818;
            padding: 10px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card-top-bar a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.2s;
        }

        .card-top-bar a:hover {
            background-color: #333333;
        }

        .recommended-users {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .user-card {
            background-color: #2c2c2c;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-card img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid вокруг #555555;
        }

        .user-card .username {
            font-weight: bold;
            color: #ffffff;
            font-size: 16px;
        }

        .user-card .actions {
            margin-left: auto;
            display: flex;
            gap: 10px;
        }

        .recommendation-card h2 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
            color: #ffffff;
        }

        /* Add musical icon for theme */
        .recommendation-card::before {
            content: '\f001';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            font-size: 24px;
            color: #ffffff;
            position: absolute;
            top: 10px;
            left: 10px;
            opacity: 0.3;
        }

        /* Style for profile picture form */
        .user-card .profile-pic-form {
            display: inline-block;
            cursor: pointer;
        }

        .user-card .profile-pic-form button {
            background: none;
            border: none;
            padding: 0;
            margin: 0;
            cursor: pointer;
        }

        .user-card .profile-pic-form img {
            background: none;
            border: 2px solid #555555;
            border-radius: 50%;
            padding: 0;
            margin: 0;
        }

        .user-card .profile-pic-form button:hover img {
            opacity: 0.8;
        }

        /* Styles for search form */
        .search-form {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-form input[type="text"] {
            background-color: #2c2c2c;
            border: 1px solid #555555;
            border-radius: 5px;
            padding: 10px;
            color: #ffffff;
            font-size: 16px;
            width: 70%;
            outline: none;
        }

        .search-form input[type="text"]::placeholder {
            color: #cccccc;
        }

        .search-form button {
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #45a049;
        }

        /* New styles for search results */
        .search-results {
            display: flex;
            justify-content: space-between;
            width: 100%;
            gap: 20px;
        }

        .search-column {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .no-results {
            text-align: center;
            color: #cccccc;
            font-size: 16px;
        }

        /* New styles for pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination a {
            background-color: #2c2c2c;
            color: #ffffff;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        .pagination a:hover {
            background-color: #444444;
        }

        .pagination .active {
            background-color: #4CAF50;
            cursor: default;
        }

        .pagination .disabled {
            background-color: #1f1f1f;
            color: #666666;
            cursor: not-allowed;
        }
    </style>

</head>

<body>
    <?php

    require("header.php");
    ?>

    <div id="main">
        <img src="pexels-edwardeyer-811838.jpg" alt="Imagem de fundo" class="background-img">

        <!-- Recommendation Card -->
        <div class="recommendation-card">
            <div class="card-top-bar">
                <a href="comunidade.php?section=forum"><i class="fas fa-comments"></i> Fórum Público</a>
                <a href="comunidade.php?section=search"><i class="fas fa-music"></i> Procurar Amigos</a>
            </div>
            <?php if (isset($_GET['section']) && $_GET['section'] === 'forum') {
                echo "<script>window.location.href='forum.php';</script>";
                exit();
            } elseif (isset($_GET['section']) && $_GET['section'] === 'search') { ?>
                <h2>Procurar Amigos por Estilo de Música ou Artistas</h2>
                <?php if (isset($_POST['search']) || isset($_GET['search_query'])) { ?>
                    <?php if ($total_results > 0) { ?>
                        <div class="search-results">
                            <?php
                            $left_column = [];
                            $right_column = [];
                            $index = 0;
                            while ($user = mysqli_fetch_assoc($search_results)) {
                                if ($index < 3) {
                                    $left_column[] = $user;
                                } else {
                                    $right_column[] = $user;
                                }
                                $index++;
                            }
                            ?>
                            <div class="search-column">
                                <?php foreach ($left_column as $user) { ?>
                                    <div class="user-card">
                                        <form method="POST" class="profile-pic-form">
                                            <input type="hidden" name="util_destino" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="view_profile">
                                                <img src="<?php echo $user['path'] ?: 'melodychat.png'; ?>" alt="Foto de perfil">
                                            </button>
                                        </form>
                                        <div class="username"><?php echo htmlspecialchars($user['util']); ?></div>
                                        <?php
                                        $genres_sql = "SELECT generos FROM generos_profile WHERE id_user = '" . $user['id'] . "' AND generos like '%" . $_SESSION['search_query'] . "%'";
                                        $genres_result = mysqli_query($con, $genres_sql);
                                        $genres = mysqli_fetch_assoc($genres_result)['generos'] ?? 'Sem gêneros';
                                        ?>
                                        <div style="color: #cccccc; font-size: 14px;">
                                            <?php
                                            if (!empty($user['matched_artist'])) {
                                                echo htmlspecialchars($user['matched_artist']);
                                            } else {
                                                echo htmlspecialchars($genres);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="search-column">
                                <?php foreach ($right_column as $user) { ?>
                                    <div class="user-card">
                                        <form method="POST" class="profile-pic-form">
                                            <input type="hidden" name="util_destino" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="view_profile">
                                                <img src="<?php echo $user['path'] ?: 'melodychat.png'; ?>" alt="Foto de perfil">
                                            </button>
                                        </form>
                                        <div class="username"><?php echo htmlspecialchars($user['util']); ?></div>
                                        <?php
                                        $genres_sql = "SELECT generos FROM generos_profile WHERE id_user = '" . $user['id'] . "' AND generos like '%" . $_SESSION['search_query'] . "%' ";
                                        $genres_result = mysqli_query($con, $genres_sql);
                                        $genres = mysqli_fetch_assoc($genres_result)['generos'] ?? 'Sem gêneros';
                                        ?>
                                        <div style="color: #cccccc; font-size: 14px;">
                                            <?php
                                            if (!empty($user['matched_artist'])) {
                                                echo htmlspecialchars($user['matched_artist']);
                                            } else {
                                                echo htmlspecialchars($genres);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- Pagination -->
                        <?php
                        $total_pages = ceil($total_results / $results_per_page);
                        if ($total_pages > 1) {
                            echo '<div class="pagination">';
                            $search_query = isset($_POST['search_query']) ? urlencode($_POST['search_query']) : (isset($_GET['search_query']) ? urlencode($_GET['search_query']) : '');

                            // Link para página anterior
                            if ($current_page > 1) {
                                echo '<a href="comunidade.php?section=search&page=' . ($current_page - 1) . '&search_query=' . $search_query . '">Anterior</a>';
                            } else {
                                echo '<a class="disabled">Anterior</a>';
                            }

                            // Links para as páginas
                            for ($i = 1; $i <= $total_pages; $i++) {
                                if ($i == $current_page) {
                                    echo '<a class="active">' . $i . '</a>';
                                } else {
                                    echo '<a href="comunidade.php?section=search&page=' . $i . '&search_query=' . $search_query . '">' . $i . '</a>';
                                }
                            }

                            // Link para próxima página
                            if ($current_page < $total_pages) {
                                echo '<a href="comunidade.php?section=search&page=' . ($current_page + 1) . '&search_query=' . $search_query . '">Próximo</a>';
                            } else {
                                echo '<a class="disabled">Próximo</a>';
                            }

                            echo '</div>';
                        }
                        ?>
                    <?php } else { ?>
                        <p class="no-results">Nenhum utilizador encontrado.</p>
                    <?php } ?>
                <?php } else { ?>
                    <form method="POST" class="search-form">
                        <input type="text" name="search_query" placeholder="Procurar por estilo de música ou artistas..."
                            value="<?php echo htmlspecialchars(isset($_POST['search_query']) ? $_POST['search_query'] : (isset($_GET['search_query']) ? $_GET['search_query'] : '')); ?>" required>
                        <button type="submit" name="search">Procurar</button>
                    </form>
                <?php } ?>
            <?php } else { ?>

                <h2>Pessoas Recomendadas</h2>
                <div class="recommended-users">
                    <?php while ($user = mysqli_fetch_assoc($recommended_users)) { ?>
                        <div class="user-card">
                            <form method="POST" class="profile-pic-form">
                                <input type="hidden" name="util_destino" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="view_profile">
                                    <img src="<?php echo $user['path'] ?: 'melodychat.png'; ?>" alt="Foto de perfil">
                                </button>
                            </form>
                            <div class="username"><?php echo htmlspecialchars($user['util']); ?></div>
                        </div>
                    <?php } ?>
                    <?php if (mysqli_num_rows($recommended_users) == 0) { ?>
                        <p style="text-align: center; color: #cccccc;">Nenhum utilizador recomendado no momento.</p>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <?php if (!empty($alert)) { ?>
            <div class="text-box"><?php echo $alert; ?></div>
        <?php } ?>
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