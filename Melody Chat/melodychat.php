<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

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

if (isset($_POST['comunidade'])) {
    header('location:comunidade.php');
    exit();
}


$update_offline_sql = "UPDATE melodychat SET login = 0 WHERE login_time < DATE_SUB(NOW(), INTERVAL 1 HOUR)";
$update_offline_result = mysqli_query($con, $update_offline_sql);


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

if (isset($_SESSION['email'])) {

    $sql12 = "select * from melodychat where email = '" . $_SESSION['email'] . "';";
    $result3 = mysqli_query($con, $sql12);
    $rowe = mysqli_fetch_assoc($result3);
}


if (isset($_POST['contact'])) {
    header('location:contact.php');
    exit();
}

if (isset($_POST['start'])) {
    header('location:login.php');
    exit();
}


if (isset($_SESSION['email'])) {

    $termos = "SELECT * from direitos where email = '" . $_SESSION['email'] . "'";
    $direitos = mysqli_query($con, $termos);

    if ($verificar_termos = mysqli_num_rows($direitos) == 0) {
        header('location:termos.php');
        exit();
    }
}





//SPOTIFY CALCULO PARA MINUTOS ACUMULADOS


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <!-- Meta tags Obrigatórias -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script defer src="plugins/fontawesome/js/all.min.js"></script>
    <link rel="icon" href="transparente.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.lineicons.com/5.0/lineicons.css" rel="stylesheet" />
    <title>Melody Chat</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            /* Impede o scroll */
            font-family: 'Arial', sans-serif;
            /* Fonte opcional */
        }

        /* Navbar fixo no topo */
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

        /* Ajuste do conteúdo principal */
        #main {
            margin-top: 90px;
            /* Para compensar a altura da navbar */
            padding: 20px;
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
            /* Small margin for balance */
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
            /* Changed from left: -120px */
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

        /* Exibir dropdown ao passar o mouse no botão */
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

        .title {
            position: absolute;
            top: 50px;
            /* Ajuste a posição do título para ficar mais para cima */
            left: 50%;
            transform: translateX(-50%);
            /* Centraliza o título */
            font-size: 50px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
            /* Sombra para o texto */
            margin-top: 20px;
        }

        .background-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Faz a imagem cobrir toda a tela */
            filter: blur(8px);
            /* Aplica o desfoque na imagem */
            z-index: -1;
            /* Coloca a imagem desfocada atrás do conteúdo */
        }

        .subtitle {
            position: absolute;
            top: 150px;
            /* Ajuste a posição do subtítulo */
            left: 50%;
            transform: translateX(-50%);
            font-size: 30px;
            font-weight: normal;
            color: #d3d3d3;
            /* Cor do texto */
            text-align: center;
            margin-top: 50px;
        }

        /* Estilo para o parágrafo com fundo cinza e bordas arredondadas */
        .text-box {
            background-color: #1f1f1f;
            /* Cor de fundo cinza */
            padding: 20px;
            border-radius: 10px;
            /* Borda arredondada */
            width: 80%;
            /* Largura do parágrafo */
            margin: 20px auto;
            /* Centraliza o parágrafo */
            font-size: 16px;
            /* Tamanho da fonte */
            color: white;
            /* Cor do texto */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Sombra leve */
        }

        .info-columns {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
        }

        .info-box {
            background-color: #1f1f1f;
            /* Mesma cor de fundo das outras áreas */
            padding: 20px;
            border-radius: 10px;
            /* Bordas arredondadas */
            width: 30%;
            /* Largura da coluna */
            color: white;
            /* Cor do texto */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Sombra leve */
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

        .separator {
            font-size: 25px;
            color: #888;
        }
    </style>
</head>

<body>


    <!-- Barra de Navegação Fixa no Topo -->
    <div class="navbar-top">
        <div class="left-section">
            <img src="https://imageshack.com/i/pmmxUi4hp" alt="Logo" class="logo">
            <?php

            if (isset($_SESSION['email'])) {

                //tem email, o server ta em manutencao e eh admin
                if ($row && $row['estado'] == 0) {

                    if ($rowe['cargo'] == 1) {
            ?>
                        <form method="post" class="navbar-options">
                            <input type="submit" name="chats" value="Chat">
                            <input type="submit" name="group" value="Grupos">
                        </form>
        </div>
        <form method="post">
            <span align="center" class="btn btn-outline-danger">SITE EM MANUTENÇÃO</span>

            <!-- Validar se o cargo eh 1 -->
            <?php ?>
            <a href="admin.php" class="btn btn-outline-warning"> <i class='lni lni-gears-3' style="margin-right: 5px;"></i>Ferramentas de administração</a>
            <input type="submit" name="contact" value="Contacte-nos">
            <input type="submit" name="profile" value="Perfil">
            <?php require('notifica.php'); ?>
            <div class="dropdown">
                <input type="button" name="def" value="☰">
                <div class="dropdown-content">
                    <a href="melodychat.php"><i class="lni lni-home-2" style="margin-right: 5px;"></i>Inicio</a>
                    <a href="dsession_melodychat.php" style="color: red;"><i class="lni lni-exit" style="margin-right: 5px;"></i>Terminar</a>
                </div>
            </div>
    </div>
    </form>
<?php
                    }
                } else {
                    //tem email, o server esta ligado e tem cargo
                    if ($rowe['cargo'] == 1) {
?>

    <form method="post" class="navbar-options">
        <input type="submit" name="chats" value="Chat">
        <input type="submit" name="group" value="Grupos">
    </form>
    </div>
    <form method="post">

        <!-- Validar se o cargo eh 1 -->
        <?php ?>
        <a href="admin.php" class="btn btn-outline-warning"> <i class='lni lni-gears-3' style="margin-right: 5px;"></i>Ferramentas de administração</a>
        <input type="submit" name="contact" value="Contacte-nos">
        <input type="submit" name="profile" value="Perfil">
        <?php require('notifica.php'); ?>
        <div class="dropdown">
            <input type="button" name="def" value="☰">
            <div class="dropdown-content">
                <a href="melodychat.php"><i class="lni lni-home-2" style="margin-right: 5px;"></i>Inicio</a>
                <a href="dsession_melodychat.php" style="color: red;"><i class="lni lni-exit" style="margin-right: 5px;"></i>Terminar</a>
            </div>
        </div>
        </div>
    </form>
<?php           //tem email, o server esta ligado e nao tem cargo
                    } else {
?>

    <form method="post" class="navbar-options">
        <input type="submit" name="chats" value="Chat">
        <input type="submit" name="group" value="Grupos">
    </form>
    </div>
    <form method="post">

        <!-- Validar se o cargo eh 1 -->
        <?php ?>
        <input type="submit" name="contact" value="Contacte-nos">
        <input type="submit" name="profile" value="Perfil">
        <?php require('notifica.php'); ?>
        <div class="dropdown">
            <input type="button" name="def" value="☰">
            <div class="dropdown-content">
                <a href="melodychat.php"><i class="lni lni-home-2" style="margin-right: 5px;"></i>Inicio</a>
                <a href="dsession_melodychat.php" style="color: red;"><i class="lni lni-exit" style="margin-right: 5px;"></i>Terminar</a>
            </div>
        </div>
        </div>
    </form>
<?php
                    }
                }
                //nao tem email, o server ta ligado manutencao
            } else {
                if ($row && $row['estado'] == 0) {
?> <span align="center" class="btn btn-outline-danger">SITE EM MANUTENÇÃO</span>
</div>
<form method="post">
    <div class="dropdown">
        <input type="button" name="def" value="☰">
        <div class="dropdown-content">
            <a href="login_admin.php"><i class="lni lni-enter" style="margin-right: 5px;"></i>Login - Administração</a>
        </div>
    </div>
    </div>
</form>


<?php
                    //nao tem email e o server ta ativo
                } else {
?>
    <form method="post" class="navbar-options">
    </form>
    </div>
    <form method="post">
        <input type="submit" value="Começar agora" class="btn btn-success" style="color: white;" name="start">
        <span class="separator">|</span>
        <input type="submit" name="contact" value="Contacte-nos">
        <div class="dropdown">
            <input type="button" name="def" value="☰">
            <div class="dropdown-content">
                <a href="login.php"><i class="lni lni-enter" style="margin-right: 5px;"></i>Login</a>
                <a href="signup.php"><i class="lni lni-plus" style="margin-right: 5px;"></i>Criar Conta</a>
            </div>
        </div>
        </div>
    </form>
<?php
                }
            }
?>

<?php
?>
</div>

<div id="main">
    <img src="fundo.jpg" alt="Imagem de fundo" class="background-img">
    <br><br><br>
    <div class="title">
        <?php
        if (!isset($_SESSION['email'])) {
            echo "MelodyChat!";
        } else {
            echo "Bem-Vindo/a, " . $_SESSION['util'];

            date_default_timezone_set('Europe/Lisbon'); // Ajusta para o fuso horário desejado
            $time_now = date("Y-m-d H:i:s");

            $login_sql = "UPDATE melodychat set login = 1, login_time = '$time_now' where email = '" . $_SESSION['email'] . "'";
            $login_query = mysqli_query($con, $login_sql);
        }  ?>
    </div>

    <?php if (isset($_SESSION['email'])) { ?>
        <div style="text-align: center; margin-top: 50px;">
            <form method="post">
                <input type="submit" name="comunidade" class="btn btn-primary" value="Entrar na Comunidade">
            </form>
        </div>
    <?php } ?>
    <div class="info-columns">
        <div class="info-box">
            <h3>Música</h3>
            <p>Partilha aqui a tua música e inicia conversa com pessoas com o mesmo gosto músical, faz amigos e cria o teu grupo de amigos vibrantes</p>
        </div>
        <div class="info-box">
            <h3>Objetivo</h3>
            <p>O objetivo do Melody Chat é criar uma comunidade ligada e tranquila onde todos podem se respeitar e partilhar os gostos musicais</p>
        </div>
        <div class="info-box">
            <h3>Contacte-Nos</h3>
            <p>Qualquer erro ou dúvida mande nos um email e a nossa equipa irá responder e ajudar sempre que póssivel todos os utilizadores</p>
        </div>
    </div>

</div>

<?php

$ano = date("Y");

?>

<footer>
    &copy; <?php echo $ano ?> Melody Chat. Todos os direitos reservados.
</footer>

<!-- JavaScript (Opcional) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>

</html>