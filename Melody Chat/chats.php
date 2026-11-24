<?php
session_start(); // PARA USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

if (!isset($_SESSION['email'])) {
    header('location:melodychat.php');
    exit();
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

date_default_timezone_set('Europe/Lisbon'); // Ajusta para o fuso horário desejado
$time_now = date("Y-m-d H:i:s");

$login_sql = "UPDATE melodychat set login = 1,login_time = '$time_now' where email = '" . $_SESSION['email'] . "'";
$login_query = mysqli_query($con, $login_sql);


// Obtém a lista de amigos
$friendsQuery = "SELECT * from friends where estado = TRUE and ( solicitacao ='" . $_SESSION['email'] . "' or pedido = '" . $_SESSION['email'] . "' )";
$friendsResult = mysqli_query($con, $friendsQuery);

// Obtém as solicitações de amizade recebidas
$requestsQuery = "SELECT * FROM friends where solicitacao = '" . $_SESSION['email'] . "' and estado = 0";
$requestsResult = mysqli_query($con, $requestsQuery);



$sql = "SELECT * from melodychat where email = '" . $_SESSION['email'] . "'";
$result = mysqli_query($con, $sql);




if (!isset($_SESSION['email_friend'])) {


    $friendsQuery = "SELECT * from friends where estado = TRUE and ( solicitacao ='" . $_SESSION['email'] . "' or pedido = '" . $_SESSION['email'] . "' )";
    $friendsResultaa = mysqli_query($con, $friendsQuery);
    $friends = mysqli_fetch_assoc($friendsResultaa);

    if (mysqli_num_rows($friendsResultaa) > 0) {
        $stmt = $con->prepare("SELECT * FROM mensagens WHERE from_email = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $last_message_query = $stmt->get_result();
        $last_messaged_friend = $last_message_query->fetch_assoc();



        $_SESSION['email_friend'] = $last_messaged_friend['to_email'];

        $stmt = $con->prepare("SELECT * FROM melodychat WHERE email = ?");
        $stmt->bind_param("s", $_SESSION['email_friend']);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $amigo = $resultado->fetch_assoc();

        // Armazena os dados do amigo
        $_SESSION['foto'] = $amigo['path'];
        $_SESSION['bio_amigo'] = $amigo['bio'];
        $_SESSION['amigo_id'] = $amigo['id'];
        $_SESSION['amigo_nome'] = $amigo['util'];
    }
    //s$stmt->close();
}



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
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obtém o código de status HTTP
    curl_close($ch);

    if ($http_code === 401) {
        echo json_encode(['error' => 'Token Expirado.. por favor Atualize']);
    }

    $data = json_decode($response, true);

    if ($data && isset($data['item'])) {
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'Nenhuma música está tocando no momento.']);
    }

    $recent_tracks = json_decode($response, true);

    $current_month_start = strtotime('first day of this month');  // Primeiro dia deste mês
    $current_month_end = strtotime('last day of this month');    // Último dia deste mês

    if (isset($recent_tracks['items'])) {

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
                    } else {
                        echo "Não foram encontradas músicas ou o cálculo falhou.";
                    }
                }
            }
        }
    }
} else {
    echo "Erro ao obter o token de acesso!";
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="transparente.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script defer src="plugins/fontawesome/js/all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.lineicons.com/5.0/lineicons.css" rel="stylesheet" />
    <title>Chat</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #000;
            color: white;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: #111;
            padding: 10px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .friends-container {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            max-width: 80%;
            overflow-x: auto;
            white-space: nowrap;
            gap: 10px;
            background-color: #1f2022;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            /* Aumentar a largura para mostrar mais amigos (ex.: 600px para ~6-7 amigos) */
            width: 600px;
            /* Ajuste aqui para um meio-termo */
        }

        /* Manter as propriedades de scrollbar */
        .friends-container::-webkit-scrollbar {
            height: 10px;
        }

        .friends-container::-webkit-scrollbar-track {
            background: #1f2022;
            border-radius: 5px;
        }

        .friends-container::-webkit-scrollbar-thumb {
            background: #7289da;
            border-radius: 5px;
        }

        .friends-container::-webkit-scrollbar-thumb:hover {
            background: #5b6eae;
        }

        /* Cartão de cada amigo */
        .friend-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .friend-card button {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: transparent;
            border: none;
            cursor: pointer;
            text-align: center;
            padding: 5px;
            width: 60px;
        }

        .friend-card button:hover {
            background-color: #7289da;
            border-radius: 5px;
        }

        .friend-card img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-bottom: 5px;
            border: 2px solid #172430;
        }

        .friend-card span {
            font-size: 12px;
            color: white;
            text-align: center;
        }


        /* Área de chat */
        .chat-container {
            margin-left: 250px;
            /* Garante que o conteúdo fique à direita da sidebar */
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #36393f;
            margin-top: 60px;
        }


        .messages {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            margin-top: 50px;
            /* Altura da topbar */
            margin-bottom: 60px;
            /* Altura da área de envio */
            display: flex;
            flex-direction: column;
            gap: 5px;
        }


        /* Estilo para as mensagens (próprias e dos amigos) */
        .own-message {
            text-align: right;
            background-color: #7289da;
            color: white;
            padding: 8px;
            margin: 5px;
            border-radius: 10px;
            max-width: 70%;
            margin-left: auto;
        }

        .friend-message {
            text-align: left;
            background-color: #40444b;
            color: white;
            padding: 8px;
            margin: 5px;
            border-radius: 10px;
            max-width: 70%;
            margin-right: auto;
        }


        .tools {
            display: flex;
            align-items: center;
            gap: 10px;
            position: absolute;
            /* Para fixar no início da topbar */
            left: 10px;
            /* Margem do lado esquerdo */
        }

        /* Botões da div tools */
        .tool-btn {
            padding: 8px 12px;
            background: #222;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .tool-btn:hover {
            background-color: #5b6eae;
        }

        .chat {
            position: absolute;
            right: 20px;
            /* Distância do lado direito */
            top: 50%;
            transform: translateY(-50%);
            /* Centraliza verticalmente na topbar */
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-size: 16px;
            font-weight: bold;
        }

        .chat p {
            margin: 0;
        }

        .chat strong {
            color: #7289da;
            /* Cor destacada */
            font-size: 18px;
            /* Um pouco maior */
        }


        /* Sidebar */


        /* Sidebar fixa à esquerda */
        .sidebar {
            width: 250px;
            height: calc(100vh - 60px);
            /* Altura ajustada para não sobrepor a topbar */
            background-color: #2c2f33;
            color: #fff;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            left: 0;
            top: 60px;
            /* Começa logo abaixo da topbar */
            z-index: 999;
        }

        /* Cartão de perfil na sidebar */
        .profile-card {
            text-align: center;
        }


        .profile-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto;
            display: block;
        }

        .profile-img:hover {
            width: 100px;
            height: 100px;
            opacity: 0.7;
            margin: 0 auto;
            display: block;
            border-radius: 50%;
            cursor: pointer;
        }



        .card-bio {
            background-color: #333;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            font-size: 1em;
            color: #ccc;
            line-height: 1.5;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            max-width: 100%;
            /* Garante que o card de bio ocupe toda a largura disponível */
            overflow: hidden;
            /* Impede o texto de ultrapassar os limites */
            text-overflow: ellipsis;
            /* Adiciona '...' no final se o texto for muito longo */
            white-space: nowrap;
            /* Impede quebras de linha no texto */
        }

        /* Ajuste para o texto da bio */
        .card-bio p {
            margin: 0;
            overflow: hidden;
            /* Impede o texto de ultrapassar os limites */
            text-overflow: ellipsis;
            /* Adiciona '...' no final se o texto for muito longo */
            white-space: nowrap;
            /* Impede quebras de linha */
        }

        #message-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #40444b;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1001;
            /* Ajusta para ficar acima dos outros elementos */
        }

        #message-bar form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0;
        }

        #message-input {
            flex: 1;
            /* Ocupa o espaço disponível */
            padding: 10px;
            border: none;
            border-radius: 5px;
            min-width: 600px;
            /* Aumentado para ficar mais largo */
            width: 100%;
            /* Usa o espaço disponível até o limite */
            box-sizing: border-box;
            margin-left: 400px;
            /* Adiciona 50px de margem à esquerda */
        }

        #send-button {
            padding: 10px 20px;
            background-color: #7289da;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            font-size: 16px;
        }

        #send-button:hover {
            background-color: #5b6eae;
        }



        .input-area {
            display: flex;
            padding: 10px;
            background-color: #40444b;
            position: fixed;
            bottom: 0;
            left: 200px;
            right: 0;
            z-index: 1001;
            margin-bottom: 0;
        }

        .input-area input[type="text"] {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-right: 10px;
        }

        .input-area input[type="submit"] {
            padding: 10px 20px;
            background-color: #7289da;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }


        /* Botão dropdown */
        .menu_dropdown_mensagens {
            position: absolute;
            top: 5px;
            left: 5px;
            width: 28px;
            height: 28px;
            background: transparent;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu_dropdown_mensagens i {
            font-size: 18px;
            color: white;
        }

        .menu_dropdown_mensagens:hover i {
            color: #000;
        }

        /* Menu dropdown ajustado para esquerda */
        .dropdown_menu {
            position: absolute;
            top: 5px;
            left: -120px;
            /* Move o dropdown para a esquerda */
            background: #0c121c;
            border: 1px solid #000;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            display: none;
            /* Oculto por padrão */
            z-index: 10;
            width: 110px;
        }

        /* Botão de apagar */
        .delete_button {
            background: #0c121c;
            color: red;
            border: none;
            padding: 8px 12px;
            width: 100%;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
        }

        .edit_message {
            background: #0c121c;
            color: white;
            border: none;
            padding: 8px 12px;
            width: 100%;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
        }

        .edit_message:hover {
            background: #0f1012;
        }

        .delete_button:hover {
            background: #1c0605;
        }

        /* Estilo do contêiner das mensagens */
        #messages {
            background: #222;
            padding: 20px;
            border-radius: 5px;
            height: 600px;
            overflow-y: scroll;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            position: relative;
            padding: 10px;
            border-radius: 8px;
            max-width: 80%;
            min-width: 150px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            margin-top: 10px;
        }

        .message.own {
            align-self: flex-end;
            background-color: #007bff;
            color: #fff;
            text-align: right;
        }

        .message.member {
            align-self: flex-start;
            background-color: #333;
            color: #fff;
            text-align: left;
        }

        .message .text {
            margin: 0;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        .time {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }


        .spotify-widget {
            position: fixed;
            top: 15px;
            right: 10px;
            width: 280px;
            background-color: #1e1e1e;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            padding: 10px;
            color: white;
            font-family: Arial, sans-serif;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            /* Organiza main-content e playing-on-spotify verticalmente */
        }

        .main-content {
            display: flex;
            align-items: center;
            gap: 10px;
            /* Espaço entre a capa do álbum e as informações */
        }

        .album-cover {
            width: 80px;
            height: auto;
            border-radius: 5px;
            flex-shrink: 0;
        }

        .track-info-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .track-info {
            font-size: 14px;
            margin: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .artist-name {
            font-size: 12px;
            margin: 0;
            color: #bbb;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .playing-on-spotify {
            font-size: 13px;
            /* Letras pequenas */
            font-weight: bold;
            /* Negrito */
            color: #1db954;
            /* Cor verde do Spotify */
            display: flex;
            align-items: center;
            gap: 5px;
            /* Espaço entre o ícone e o texto */
            margin-top: 8px;
            /* Espaço acima do texto */
            width: 100%;
            /* Garante que o texto se estenda por toda a largura */
            text-align: left;
            /* Alinha à esquerda para cobrir a área da capa */
        }

        .playing-on-spotify i {
            font-size: 16px;
            /* Tamanho do ícone do Spotify */
        }

        .error-message {
            color: #ff5555;
            font-size: 12px;
            margin: 0;
            display: none;
        }

        .share-btn {
            padding: 5px 10px;
            background-color: #111;
            /* Verde do Spotify */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
            margin-left: 10px;
            /* Espaço entre o texto e o botão */
        }

        .share-btn:hover {
            background-color: #172430;
            /* Verde mais escuro ao passar o mouse */
        }

        .message.own a,
        .message.member a {
            color: #1db954;
            /* Verde do Spotify */
            text-decoration: underline;
            background-color: #214452;
        }

        .message.own a:hover,
        .message.member a:hover {
            color: #17a34a;
            background-color: #214452;
        }

        .scroll-down-btn {
            position: fixed;
            bottom: 60px;
            /* Reduzido para ficar mais próximo do fundo */
            right: 20px;
            /* Mantém a distância da direita */
            background-color: #222b38;
            /* Mantém a cor principal */
            color: white;
            border: none;
            border-radius: 50%;
            /* Mantém circular */
            width: 30px;
            /* Reduzido para um tamanho menor */
            height: 30px;
            /* Reduzido para um tamanho menor */
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1002;
            /* Mantém acima dos outros elementos */
            font-size: 16px;
            /* Reduzido o tamanho do ícone */
        }

        .scroll-down-btn:hover {
            background-color: #0f1012;
            /* Mantém o hover, mas opcional */
        }

        .scroll-down-btn i {
            margin: 0;
            /* Remove margens internas do ícone */
        }

        button[name="profile_user"] {
            background-color: transparent;
            /* Transparent background */
            border: none;
            /* Remove any default borders */
            padding: 0;
            /* Remove padding to avoid extra space */
            box-shadow: none;
            /* No shadow */
            cursor: pointer;
            /* Pointer cursor for consistency */
        }
    </style>
</head>

<body>





    <?php if (!isset($_SESSION['email_friend'])) {
    ?>
        <!-- Topbar com lista de amigos -->
        <div class="topbar">
            <div class="tools">
                <button class="tool-btn" onclick="location.href='melodychat.php';">Início</button>
                <button class="tool-btn" onclick="location.href='profile.php';">Perfil</button>
            </div>
            <div class="friends-container">
                <?php
                if (mysqli_num_rows($friendsResult) > 0) {
                    while ($friend = mysqli_fetch_assoc($friendsResult)) {
                        // Determina o email do amigo com base em quem fez o pedido ou recebeu
                        if ($friend['pedido'] == $_SESSION['email']) {
                            $sql = "SELECT * from melodychat where email = '" . $friend['solicitacao'] . "'";
                        } elseif ($friend['solicitacao'] == $_SESSION['email']) {
                            $sql = "SELECT * from melodychat where email = '" . $friend['pedido'] . "'";
                        }

                        $resultado = mysqli_query($con, $sql);
                        $amigo = mysqli_fetch_assoc($resultado);

                        // Verifica o status de login do amigo
                        $online_sql = "SELECT login from melodychat where email = '" . $amigo['email'] . "'";
                        $online_query = mysqli_query($con, $online_sql);
                        $online_status = mysqli_fetch_assoc($online_query); // Obtém o status

                        // Define o estilo da borda com base no status (online ou offline)
                        $border_style = isset($online_status['login']) && $online_status['login'] == 1 ? 'border: 2px solid #6ad15c;' : 'border: 2px solid #172430;';

                ?>
                        <div class="friend-card">
                            <form method="post">
                                <button name="amigo">
                                    <div style="position: relative; display: inline-block;">
                                        <img src="<?= $amigo['path'] ?? 'melodychat.png'; ?>" alt="Foto" style="width: 40px; height: 40px; border-radius: 50%; margin-bottom: 5px; <?php echo $border_style; ?>">
                                    </div>
                                    <span><?= htmlspecialchars($amigo['util']); ?></span>
                                </button>
                                <input type="hidden" name="amigo_email" value="<?= htmlspecialchars($amigo['email']); ?>">
                                <input type="hidden" name="amigo_nome" value="<?= htmlspecialchars($amigo['util']); ?>">
                            </form>
                        </div>
                <?php
                    }
                } else {
                    echo "Não tens amigos adicionados";
                }

                if (isset($_POST['amigo'])) {
                    // Obtenha o email ou ID do amigo clicado
                    $amigo_email = $_POST['amigo_email'];

                    // Atualize a variável de sessão com o amigo selecionado
                    $_SESSION['email_friend'] = $amigo_email;
                    $_SESSION['amigo_nome'] = $_POST['amigo_nome'];
                }
                ?>
            </div>
        </div>
    <?php } else {
    ?>


        <div class="topbar">
            <div class="tools">
                <button class="tool-btn" onclick="location.href='melodychat.php';">Início</button>
                <button class="tool-btn" onclick="location.href='profile.php';">Perfil</button>
            </div>
            <div class="friends-container">
                <?php
                if (mysqli_num_rows($friendsResult) > 0) {
                    // Array para armazenar os amigos ordenados por última mensagem
                    $orderedFriends = [];

                    while ($friend = mysqli_fetch_assoc($friendsResult)) {
                        // Determina o email do amigo
                        $friendEmail = ($friend['pedido'] == $_SESSION['email']) ? $friend['solicitacao'] : $friend['pedido'];

                        // Consulta a mensagem mais recente entre o usuário e o amigo
                        $sqlMessages = "SELECT MAX(created_at) as ultima_mensagem 
                                        FROM mensagens 
                                        WHERE (from_email = '" . $_SESSION['email'] . "' AND to_email = '" . $friendEmail . "') 
                                           OR (from_email = '" . $friendEmail . "' AND to_email = '" . $_SESSION['email'] . "')";
                        $messageQuery = mysqli_query($con, $sqlMessages);
                        $messageData = mysqli_fetch_assoc($messageQuery);

                        // Adiciona o amigo ao array com a data da última mensagem (ou null se não houver mensagens)
                        $orderedFriends[] = [
                            'email' => $friendEmail,
                            'ultima_mensagem' => $messageData['ultima_mensagem'] ?? '1970-01-01 00:00:00' // Data padrão para amigos sem mensagens
                        ];
                    }

                    // Ordena os amigos pela data da última mensagem (mais recente primeiro)
                    usort($orderedFriends, function ($a, $b) {
                        return strtotime($b['ultima_mensagem']) - strtotime($a['ultima_mensagem']);
                    });

                    // Agora percorre os amigos ordenados
                    foreach ($orderedFriends as $friend) {
                        // Consulta os dados do amigo na tabela melodychat
                        $sql = "SELECT * FROM melodychat WHERE email = '" . $friend['email'] . "'";
                        $resultado = mysqli_query($con, $sql);
                        $amigo = mysqli_fetch_assoc($resultado);

                        // Verifica o status de login do amigo
                        $online_sql = "SELECT login FROM melodychat WHERE email = '" . $amigo['email'] . "'";
                        $online_query = mysqli_query($con, $online_sql);
                        $online_status = mysqli_fetch_assoc($online_query);

                        // Define o estilo da borda com base no status
                        $border_style = isset($online_status['login']) && $online_status['login'] == 1 ? 'border: 2px solid #6ad15c;' : 'border: 2px solid #172430;';
                ?>
                        <div class="friend-card">
                            <form method="post">
                                <button name="amigo">
                                    <div style="position: relative; display: inline-block;">
                                        <img src="<?= $amigo['path'] ?? 'melodychat.png'; ?>" alt="Foto" style="width: 40px; height: 40px; border-radius: 50%; margin-bottom: 5px; <?php echo $border_style; ?>">
                                    </div>
                                    <span><?= htmlspecialchars($amigo['util']); ?></span>
                                </button>
                                <input type="hidden" name="amigo_email" value="<?= htmlspecialchars($amigo['email']); ?>">
                                <input type="hidden" name="amigo_nome" value="<?= htmlspecialchars($amigo['util']); ?>">
                                <input type="hidden" name="foto" value="<?= htmlspecialchars($amigo['path']) ?>">
                            </form>
                        </div>
                <?php
                    }
                } else {
                    echo "Não tens amigos adicionados";
                }

                if (isset($_POST['amigo'])) {
                    $amigo_email = $_POST['amigo_email'];
                    $_SESSION['email_friend'] = $amigo_email;
                    $_SESSION['amigo_nome'] = $_POST['amigo_nome'];

                    $sql = "SELECT * from melodychat where email = '" . $_SESSION['email_friend'] . "'";
                    $resultado = mysqli_query($con, $sql);
                    $amigo = mysqli_fetch_assoc($resultado);

                    $_SESSION['foto'] = $amigo['path'];
                    $_SESSION['bio_amigo'] = $amigo['bio'];
                    $_SESSION['amigo_id'] = $amigo['id'];
                    $_SESSION['email_nome'] = $_SESSION['amigo_nome'];
                }
                ?>
            </div>
        </div>

        <?php if (isset($_SESSION['flag'])) { ?>
            <div class="spotify-widget" id="spotifyWidget">
                <div class="main-content">
                    <img id="album-image" class="album-cover" src="" alt="Capa do Álbum" style="display: none;">
                    <div class="track-info-container">
                        <div id="track-name" class="track-info">Carregando música...</div>
                        <div id="artist-name" class="artist-name"></div>
                        <div id="error" class="error-message">Nenhuma música está tocando no momento.</div>
                    </div>
                    <button id="share-button" class="share-btn" title="Partilhar música" style="display: none;"><i class="fa-solid fa-share"></i></button>
                </div>
                <div class="playing-on-spotify" id="playing" style="display: none;">
                    <a href="https://open.spotify.com/" style="color:#1db954" target="_blank">
                        <i class="fab fa-spotify"></i> <strong>Playing on Spotify</strong>
                    </a>
                </div>
            </div>
        <?php } ?>

        <div class="sidebar">
            <div class="profile-card">
                <br><br>
                <h2>Perfil</h2>
                <form method="post">
                    <button name="profile_user" style="background-color: transparent;">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($_SESSION['amigo_id']) ?>">
                        <img src="<?= isset($_SESSION['foto']) && !empty($_SESSION['foto']) ? $_SESSION['foto'] : 'melodychat.png'; ?>" alt="Foto de Perfil" class="profile-img">
                    </button>
                </form>
                <p><?= isset($_SESSION['amigo_nome']) ? htmlspecialchars($_SESSION['amigo_nome']) : 'Nome não disponível'; ?></p>
                <br>
                <div class="card-bio">
                    <p><?php echo $_SESSION['bio_amigo'] ? $_SESSION['bio_amigo'] : '🎶🎧' ?></p>
                </div>
            </div>
        </div>
        <?php
        if (isset($_POST['profile_user'])) {

            $_SESSION['visu_perfil'] = $_POST['id'];
            echo "<script>window.location.href='profile_user.php';</script>";
        }
        ?>
        <div class="chat-container" id="chat-container">
            <button class="scroll-down-btn" title="Rolar para baixo">
                <i class="fas fa-arrow-down"></i> <!-- Ícone de seta para baixo do Font Awesome -->
            </button>
            <div class="messages" id="messages">
                <p>Carregando mensagens...</p>
            </div>
        </div>

        <div class="input-area" id="message-bar">
            <form method="post">
                <input type="text" id="message-input" name="message" placeholder="Escreve a tua mensagem..." required>
                <input type="submit" name="send_message" value="Enviar" id="send-button">
            </form>
        </div>

        <div id="editModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #40444b; padding: 20px; border-radius: 10px; z-index: 1003; width: 400px;">
            <h3 style="margin: 0 0 15px; color: white;">Editar Mensagem</h3>
            <form id="editMessageForm">
                <input type="hidden" id="editMessageId" name="message_id">
                <textarea id="editMessageText" name="message" style="width: 100%; min-height: 100px; padding: 10px; border-radius: 5px; border: none; resize: vertical;"></textarea>
                <div style="margin-top: 15px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="submit" class="tool-btn" style="background: #7289da;">Salvar</button>
                    <button type="button" id="cancelEditButton" class="tool-btn" style="background: #dc3545;">Cancelar</button>
                </div>
            </form>
        </div>



        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {

                function loadMessages() {
                    $.ajax({
                        url: 'ajax/mensagens_ajax.php',
                        type: 'POST',
                        data: {
                            from: '<?= $_SESSION['email'] ?? '' ?>',
                            to: '<?= $_SESSION['email_friend'] ?? '' ?>',
                            action: 'load_messages'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#messages').html(response.html);
                            } else {
                                console.error('Erro ao carregar mensagens:', response.error);
                                alert('Erro ao carregar mensagens: ' + response.error);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro AJAX:', status, error, xhr.responseText);
                            alert('Erro ao carregar mensagens.');
                        }
                    });
                }

                loadMessages(); // Carrega inicialmente
                setInterval(loadMessages, 3000); // Atualiza a cada 3 segundos

                $('#message-bar form').submit(function(e) {
                    e.preventDefault();
                    var message = $('#message-input').val();
                    var submitButton = $(this).find(':submit');
                    submitButton.prop('disabled', true);

                    if (!message.trim()) {
                        alert('Por favor, digite uma mensagem.');
                        submitButton.prop('disabled', false);
                        return;
                    }

                    $.ajax({
                        url: 'ajax/mensagens_ajax.php',
                        type: 'POST',
                        data: {
                            action: 'send_message',
                            from: '<?= $_SESSION['email'] ?? '' ?>',
                            to: '<?= $_SESSION['email_friend'] ?? '' ?>',
                            message: message
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#message-input').val('');
                                loadMessages();
                                $(function() {
                                    var hasScrolled = localStorage.getItem('hasScrolled') === 'true';
                                    console.log('hasScrolled:', hasScrolled);
                                    setTimeout(function() {
                                        if (hasScrolled) {
                                            var messages = document.getElementById("chat-container");
                                            if (messages) {
                                                console.log("Elemento #messages encontrado, rolando para o final...");
                                                messages.scrollIntoView({
                                                    behavior: 'smooth',
                                                    block: 'end'
                                                });
                                                localStorage.setItem('hasScrolled', 'true');
                                            } else {
                                                console.error("Elemento #messages não encontrado!");
                                            }
                                        } else {
                                            console.log("Scroll já foi aplicado anteriormente, pulando...");
                                        }
                                    }, 100);
                                });
                            } else {
                                alert('Erro ao enviar mensagem: ' + (response.error || 'Erro desconhecido'));
                            }
                            submitButton.prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro AJAX:', status, error, xhr.responseText);
                            alert('Erro ao enviar mensagem.');
                            submitButton.prop('disabled', false);
                        }
                    });
                });


                function openEditModal(messageId, messageText) {
                    $('#editMessageId').val(messageId);
                    $('#editMessageText').val(messageText);
                    $('#editModal').show();
                }

                function closeEditModal() {
                    $('#editModal').hide();
                    $('#editMessageId').val('');
                    $('#editMessageText').val('');
                }

                // Manipula o clique no botão de editar
                $(document).on('click', '.edit_message', function(e) {
                    e.preventDefault();
                    var $form = $(this).closest('form');
                    var messageId = $form.find('input[name="id_message"]').val();
                    var messageText = $form.closest('.message').find('.text').text().trim();

                    openEditModal(messageId, messageText);
                    $form.closest('.dropdown_menu').hide(); // Fecha o dropdown
                });

                // Manipula o envio do formulário de edição
                $('#editMessageForm').submit(function(e) {
                    e.preventDefault();

                    var messageId = $('#editMessageId').val();
                    var newMessage = $('#editMessageText').val().trim();

                    if (!newMessage) {
                        alert('Por favor, digite uma mensagem.');
                        return;
                    }

                    $.ajax({
                        url: 'ajax/mensagens_ajax.php',
                        type: 'POST',
                        data: {
                            action: 'edit_message',
                            message_id: messageId,
                            from: '<?= $_SESSION['email'] ?? '' ?>',
                            to: '<?= $_SESSION['email_friend'] ?? '' ?>',
                            message: newMessage
                        },
                        success: function(response) {
                            console.log('Resposta do servidor:', response); // Para depuração
                            if (response.success) {
                                closeEditModal();
                                loadMessages(); // Recarrega as mensagens
                            } else {
                                alert('Erro ao editar mensagem: ' + (response.error || 'Erro desconhecido'));
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro AJAX:', status, error, xhr.responseText);
                            alert('Erro ao editar mensagem: ' + xhr.responseText);
                        }
                    });
                });

                // Manipula o clique no botão de cancelar
                $('#cancelEditButton').click(function() {
                    closeEditModal();
                });

                // Fecha o modal ao clicar fora
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#editModal, .edit_message').length) {
                        closeEditModal();
                    }
                });

                function fetchCurrentTrack() {
                    fetch('atualizar_musica.php')
                        .then(response => response.json())
                        .then(data => {
                            const albumImage = document.getElementById('album-image');
                            const trackName = document.getElementById('track-name');
                            const artistName = document.getElementById('artist-name');
                            const error = document.getElementById('error');
                            const shareButton = document.getElementById('share-button');

                            if (data.error) {
                                console.error(data.error);
                                trackName.textContent = data.error; // Exibe a mensagem de erro, incluindo "Token Expirado.. por favor Atualize"
                                trackName.style.display = 'block';
                                artistName.style.display = 'none';
                                albumImage.style.display = 'none';
                                error.style.display = 'none'; // Oculta o elemento de erro padrão
                                shareButton.style.display = 'none';
                                playing.style.display = 'none';
                                return;
                            }

                            if (data && data.item) {
                                albumImage.src = data.item.album.images[0].url;
                                albumImage.style.display = 'block';
                                trackName.textContent = data.item.name;
                                playing.style.display = 'block';
                                artistName.textContent = data.item.artists[0].name;
                                artistName.style.display = 'block';
                                error.style.display = 'none';
                                trackName.style.display = 'block';
                                shareButton.style.display = 'block';
                                shareButton.dataset.trackUrl = data.item.external_urls.spotify;
                            } else {
                                trackName.textContent = 'Nenhuma música está tocando no momento.';
                                artistName.style.display = 'none';
                                albumImage.style.display = 'none';
                                error.style.display = 'none';
                                shareButton.style.display = 'none';
                                playing.style.display = 'none';
                            }
                        })
                        .catch(error => console.error('Erro ao buscar a música atual:', error));
                }

                // Função para compartilhar a música como mensagem
                function shareTrackAsMessage() {
                    const shareButton = document.getElementById('share-button');
                    const trackUrl = shareButton.dataset.trackUrl;

                    if (trackUrl) {
                        $.ajax({
                            url: 'ajax/mensagens_ajax.php',
                            type: 'POST',
                            data: {
                                action: 'send_message',
                                from: '<?= $_SESSION['email'] ?? '' ?>',
                                to: '<?= $_SESSION['email_friend'] ?? '' ?>',
                                message: 'Ouve esta música: ' + trackUrl // Mensagem com o link
                            },
                            success: function(response) {
                                if (response.success) {
                                    loadMessages(); // Atualiza as mensagens no chat
                                } else {
                                    alert('Erro ao compartilhar música: ' + (response.error || 'Erro desconhecido'));
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Erro AJAX:', status, error, xhr.responseText);
                                alert('Erro ao compartilhar música.');
                            }
                        });
                    } else {
                        alert('Nenhuma música disponível para compartilhar.');
                    }
                }

                // Vincula o evento de clique ao botão
                $('#share-button').on('click', shareTrackAsMessage);

                setInterval(fetchCurrentTrack, 4000);
                fetchCurrentTrack(); // Carrega imediatamente

                function scrollToBottom() {
                    var messages = document.getElementById("chat-container");
                    if (messages) {
                        console.log("Rolando para o final...");
                        messages.scrollIntoView({
                            behavior: 'smooth',
                            block: 'end'
                        });
                        localStorage.setItem('hasScrolled', 'true');
                    } else {
                        console.error("Elemento #chat-container não encontrado!");
                    }
                }

                // Evento de clique no botão de scroll
                $('.scroll-down-btn').on('click', function() {
                    scrollToBottom();
                });
            });
        </script>
    <?php } ?>
    <?php
    if (isset($_POST['delete_message'])) {
        $id_message = $_POST['id_message'];

        $sql = "DELETE FROM mensagens WHERE id = ?";
        $stmt = $con->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id_message);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo "<script>alert('Mensagem apagada com sucesso.'); window.location.href='chats.php';</script>";
                } else {
                    echo "<script>alert('Erro ao apagar mensagem ou ID não encontrado.'); window.location.href='chats.php';</script>";
                }
            } else {
                echo "<script>alert('Erro ao executar a consulta.'); window.location.href='chats.php';</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Erro na preparação da consulta.'); window.location.href='chats.php';</script>";
        }
    }

    if (isset($_POST['edit_message'])) {
    }
    ?>


    <script>
        function toggleDropdown(button) {
            let dropdown = button.nextElementSibling;
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }
    </script>


    <?php
    function getHoraAtual()
    {
        date_default_timezone_set('Europe/Lisbon'); // Ajusta para o fuso horário desejado
        $dataHora = date("Y-m-d H:i:s"); // Formato aceito pelo MySQL: YYYY-MM-DD HH:MM:SS
        return $dataHora;
    }


    // Usar a função

    ?>
    <script>
        $(function() {
            var hasScrolled = localStorage.getItem('hasScrolled') === 'true';
            console.log('hasScrolled:', hasScrolled);

            setTimeout(function() {
                if (hasScrolled) {
                    var messages = document.getElementById("chat-container");
                    if (messages) {
                        console.log("Elemento #messages encontrado, rolando para o final...");
                        messages.scrollIntoView({
                            behavior: 'smooth',
                            block: 'end'
                        }); // Rola para o final do elemento
                        localStorage.setItem('hasScrolled', 'true'); // Marca como já scrollado
                    } else {
                        console.error("Elemento #messages não encontrado!");
                    }
                } else {
                    console.log("Scroll já foi aplicado anteriormente, pulando...");
                }
            }, 100);
        });

        
    </script>
</body>

</html>