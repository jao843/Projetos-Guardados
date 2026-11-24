<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

if (!isset($_SESSION['email'])) {
    header('location:melodychat.php');
    exit();
} elseif (isset($_SESSION['email'])) {
    $sql = "Select email FROM melodychat WHERE cargo=TRUE and email= '" . $_SESSION['email'] . "';";
    $resultado = mysqli_query($con, $sql);
    $num_cargo = mysqli_num_rows($resultado);
    if ($num_cargo != 0) {
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
        if (isset($_POST['hstr_alt'])) {
            header('location:hstr_alt.php');
            exit();
        }
        if (isset($_POST['noti'])) {
            header('location:notificacoes.php');
            exit();
        }

        if (isset($_POST['utils'])) {
            header('location:utilizadores.php');
            exit();
        }
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <!-- Meta tags Obrigatórias -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://cdn.lineicons.com/5.0/lineicons.css" rel="stylesheet" />
    <link rel="icon" href="transparente.png" type="image/png">
    <script defer src="plugins/fontawesome/js/all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Melody Chat</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            font-family: 'Arial', sans-serif;
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
            left: 50%;
            transform: translateX(-50%);
            font-size: 30px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
            margin-top: 20px;
        }

        .background-img {
            position: absolute;
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

        .admin-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .admin-card {
            background-color: #2c2c2c; /* Darker background */
            color: #ffffff; /* White text for contrast */
            width: 100%;
            max-width: 500px;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); /* Darker shadow */
        }

        .admin-card h2 {
            font-size: 20px;
            color: #ffffff; /* White text */
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-size: 14px;
            color: #a9a9a9; /* Lighter gray for labels */
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #555555; /* Darker border */
            background-color: #333333; /* Dark input background */
            color: #ffffff; /* White text */
            border-radius: 6px;
            font-size: 14px;
        }

        input[type="checkbox"] {
            margin-right: 8px;
        }

        .submit-button {
            width: 20%;
            padding: 10px;
            font-size: 14px;
            background-color: #4682b4; /* Darker blue */
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
            margin: 10px auto 0;
            text-align: center;
        }

        .submit-button:hover {
            background-color: #5a9bd4; /* Lighter blue on hover */
        }

        .back-button {
            background-color: #4682b4; /* Darker blue */
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .back-button:hover {
            background-color: #5a9bd4; /* Lighter blue on hover */
            transform: scale(1.05);
        }

        .back-button:active {
            background-color: #3a6a9a; /* Even darker blue when clicked */
            transform: scale(0.95);
        }

        .back-button::before {
            content: '←';
            margin-right: 8px;
            font-weight: bold;
        }

        .card {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background-color: #333333; /* Darker card background */
            border: 1px solid #555555; /* Darker border */
            border-radius: 8px;
            max-width: 600px;
            margin: 10px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .user-row {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            justify-content: space-between;
        }

        .user-email {
            font-size: 10px;
            color: #d3d3d3; /* Lighter text for contrast */
            flex: 2;
        }

        .separator {
            font-size: 14px;
            color: #888;
        }

        .button {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .button-block {
            background-color: #ff6347; /* Keep red for block */
            color: #fff;
        }

Facilitate:hover {
            background-color: #c82333;
        }

        .button-admin {
            background-color: #4682b4; /* Darker blue */
            color: #fff;
        }

        .button-promote {
            background-color: #32cd32; /* Keep green for promote */
            color: #fff;
        }

        .button-promote:hover {
            background-color: #28a745;
        }

        .button[disabled] {
            background-color: #555555; /* Darker disabled button */
            color: #888888;
            cursor: default;
        }
    </style>
</head>

<body>
    <!-- Barra de Navegação Fixa no Topo -->
    <?php
    require('header.php');
    ?>

    <div id="main">
        <img src="drum.jpg" alt="Imagem de fundo" class="background-img">
        <br><br><br>

        <div class="admin-container">
            <div class="admin-card">
                <div class="title">
                    Ferramentas de administração
                </div>

                <form method="post">
                    <div class="form-group">
                        <?php
                        if (isset($_POST['voltar'])) {
                            $aux = 0;
                        }

                        if (isset($_POST['procurar'])) {
                            if (isset($_POST['email'])) {
                                $aux = 1;
                                $_SESSION['email_aux'] = $_POST['email'];
                                $id_email_sql = "SELECT * from melodychat where email = '" . $_SESSION['email_aux'] . "'";
                                $id_email_query = mysqli_query($con, $id_email_sql);
                                $id_email = mysqli_fetch_assoc($id_email_query);
                                $_SESSION['id_aux'] = $id_email['id'];
                            } else {
                                $erros = "Insira um email valido";
                            }
                        }

                        if (isset($_SESSION['email_aux'])) {
                            $sql = "select * from melodychat where email = '" . $_SESSION['email_aux'] . "';";
                            $resultado = mysqli_query($con, $sql);
                            $all = mysqli_num_rows($resultado);
                            if ($all > 0) {
                                $dados = mysqli_fetch_assoc($resultado);
                                $email = $dados['email'];
                            }
                        }

                        if (!isset($_POST['procurar'])) {
                            $aux = 0;
                        }

                        if (isset($_POST['bloquear'])) {
                            $bloquear = "UPDATE melodychat SET estado = TRUE where email = '" . $_SESSION['email_aux'] . "';";
                            $bloquear_resul = mysqli_query($con, $bloquear);
                            $aux = 1;
                            $alt_sql = "INSERT INTO alt_administracao set id_user = " . $_SESSION['id'] . ", email = '" . $_SESSION['email'] . "', id_user_alt = " . $_SESSION['id_aux'] . ",email_user_alt = '" . $_SESSION['email_aux'] . "', acao = 'Bloquear'";
                            $alt_query = mysqli_query($con, $alt_sql);
                        }

                        if (isset($_POST['bloqueado'])) {
                            $bloqueado = "UPDATE melodychat SET estado = FALSE where email = '" . $_SESSION['email_aux'] . "';";
                            $bloqueado_resul = mysqli_query($con, $bloqueado);
                            $aux = 1;
                            $alt_sql = "INSERT INTO alt_administracao set id_user = " . $_SESSION['id'] . ", email = '" . $_SESSION['email'] . "', id_user_alt = " . $_SESSION['id_aux'] . ",email_user_alt = '" . $_SESSION['email_aux'] . "', acao = 'Desbloquear'";
                            $alt_query = mysqli_query($con, $alt_sql);
                        }

                        if (isset($_POST['promover'])) {
                            if ($dados['cargo'] == FALSE) {
                                $promover = "UPDATE melodychat SET cargo = TRUE where email = '" . $_SESSION['email_aux'] . "';";
                                $promover_resul = mysqli_query($con, $promover);
                                $aux = 1;
                                $alt_sql = "INSERT INTO alt_administracao set id_user = " . $_SESSION['id'] . ", email = '" . $_SESSION['email'] . "', id_user_alt = " . $_SESSION['id_aux'] . ",email_user_alt = '" . $_SESSION['email_aux'] . "', acao = 'Promover'";
                                $alt_query = mysqli_query($con, $alt_sql);
                            } else if ($dados['cargo'] == TRUE) {
                                $despromover = "UPDATE melodychat SET cargo = FALSE where email = '" . $_SESSION['email_aux'] . "';";
                                $despromover_resul = mysqli_query($con, $despromover);
                                $aux = 1;
                                $alt_sql = "INSERT INTO alt_administracao set id_user = " . $_SESSION['id'] . ", email = '" . $_SESSION['email'] . "', id_user_alt = " . $_SESSION['id_aux'] . ",email_user_alt = '" . $_SESSION['email_aux'] . "', acao = 'Despromover'";
                                $alt_query = mysqli_query($con, $alt_sql);
                            }
                        }

                        $alt_sql = "";

                        if ($aux == 0) {
                        ?>
                            <label>Procurar utilizador</label>
                            <input type="text" name="email" placeholder="Digite o email para procurar...">
                            <input type="submit" name="procurar" class="submit-button" value="Procurar">
                        <?php
                        } else {
                        ?>
                            <form method="post">
                                <input type="submit" name="voltar" class="back-button" value="Voltar">
                            </form>
                            <?php
                            if (isset($_SESSION['email_aux'])) {
                                $sql = "select * from melodychat where email = '" . $_SESSION['email_aux'] . "';";
                                $resultado = mysqli_query($con, $sql);
                                $all = mysqli_num_rows($resultado);
                                if ($all > 0) {
                                    $dados = mysqli_fetch_assoc($resultado);
                                    $email = $dados['email'];
                                }
                            }

                            if ($all > 0) {
                                if ($_SESSION['email_aux'] != 'rjoaoduarte@gmail.com') {
                            ?>
                                    <div class="card">
                                        <form method="post" class="user-row">
                                            <span class="user-email"><?php echo $_SESSION['email_aux']; ?></span>
                                            <span class="separator">|</span>
                                            <?php if ($dados['estado'] == FALSE) { ?>
                                                <input type="submit" name="bloquear" value="Bloquear" class="button button-block">
                                            <?php } else { ?>
                                                <input type="submit" name="bloqueado" value="Bloqueado" class="button button-block">
                                            <?php } ?>
                                            <input type="submit" value="<?php if ($dados['cargo'] == false) {
                                                                            echo 'Utilizador';
                                                                        } else {
                                                                            echo 'ADMIN';
                                                                        } ?>" class="button button-admin" disabled>
                                            <input type="submit" name="promover" value="Promover" class="button button-promote">
                                        </form>
                                    </div>
                                <?php
                                } else {
                                ?>
                                    <div class="card">
                                        <div class="row" align="center" style="margin-top: 5px;">
                                            <div class="col-md-12" align="center">
                                                <div class="alert alert-dark" role="alert">
                                                    <span> Este email não permite mudanças</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }
                            } else {
                            ?>
                                <div class="card">
                                    <div class="row" align="center" style="margin-top: 5px;">
                                        <div class="col-md-12" align="center">
                                            <div class="alert alert-dark" role="alert">
                                                <span> Não existe esse email na base de dados</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        }

                        $est = "SELECT estado FROM manutencao WHERE id = 1;";
                        $estado = mysqli_query($con, $est);
                        $row = mysqli_fetch_assoc($estado);

                        if (isset($_POST['manu'])) {
                            if ($row && $row['estado'] == 1) {
                                $estado_1 = "UPDATE manutencao SET estado = 0 WHERE id = 1;";
                                $resul = mysqli_query($con, $estado_1);
                                $aviso = "[Modo de Manutenção - Ativado]";
                                $alt_sql = "INSERT INTO alt_administracao set id_user = " . $_SESSION['id'] . ", email = '" . $_SESSION['email'] . "', acao = 'Manutenção Ativa'";
                                $alt_query = mysqli_query($con, $alt_sql);
                            } else {
                                $estado_1 = "UPDATE manutencao SET estado = 1 WHERE id = 1;";
                                $resul = mysqli_query($con, $estado_1);
                                $aviso = "[Modo de Manutenção - Desativado]";
                                $alt_sql = "INSERT INTO alt_administracao set id_user = " . $_SESSION['id'] . ", email = '" . $_SESSION['email'] . "', acao = 'Manutenção Desativa'";
                                $alt_query = mysqli_query($con, $alt_sql);
                            }
                        }
                        ?>

                        <?php
                        if (isset($aviso)) {
                        ?>
                            <div class="row" align="center" style="margin-top: 5px;">
                                <div class="col-md-12" align="center">
                                    <div class="alert alert-dark" role="alert">
                                        <?php echo $aviso; ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>

                        <div class="form-group">
                            <label>Ferramentas</label>
                            <input type="submit" name="manu" value="Modo de Manutenção" class="btn btn-dark">
                            <input type="submit" name="hstr_alt" style="margin-left: 10px" value="Hístorico" class="btn btn-dark">
                            <input type="submit" name="noti" style="margin-left: 5px" value="Notificações" class="btn btn-dark">
                        </div>
                        <div>
                            <input type="submit" name="utils" style="margin-left: 5px" value="Utilizadores" class="btn btn-dark">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php
    $ano = date("Y");
    ?>
    <footer>
        © <?php echo $ano ?> Melody Chat. Todos os direitos reservados.
    </footer>

<?php
    } else {
        header('location:melodychat.php');
        exit();
    }
} else {
    header('location:melodychat.php');
    exit();
}
?>

<!-- JavaScript (Opcional) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>