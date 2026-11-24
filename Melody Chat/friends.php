<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

if (!isset($_SESSION['email'])) {

    header('location:melodychat.php');
    exit();
}

date_default_timezone_set('Europe/Lisbon'); // Ajusta para o fuso horário desejado
$time_now = date("Y-m-d H:i:s");

$login_sql = "UPDATE melodychat set login_time = '$time_now' where email = '" . $_SESSION['email'] . "'";
$login_query = mysqli_query($con, $login_sql);


// Obtém a lista de amigos
$friendsQuery = "SELECT * from friends where estado = TRUE and ( solicitacao ='" . $_SESSION['email'] . "' or pedido = '" . $_SESSION['email'] . "' )";
$friendsResult = mysqli_query($con, $friendsQuery);

// Obtém as solicitações de amizade recebidas
$requestsQuery = "SELECT * FROM friends where solicitacao = '" . $_SESSION['email'] . "' and estado = 0";
$requestsResult = mysqli_query($con, $requestsQuery);



$sql = "SELECT * from melodychat where email = '" . $_SESSION['email'] . "'";
$result = mysqli_query($con, $sql);




if (isset($refresh)) {
    header("Refresh:0");
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
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="icon" href="transparente.png" type="image/png">
    <link href="https://cdn.lineicons.com/5.0/lineicons.css" rel="stylesheet" />
    <title>Amigos</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #181818;
            color: white;
        }

        .container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        .table-container {
            width: 48%;
            background-color: #2c2c2c;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .table-container h2 {
            text-align: center;
            background-color: #444444;
            margin: 0;
            padding: 10px 0;
            font-size: 18px;
        }

        .scrollable-table {
            max-height: 400px;
            overflow-y: auto;

        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px;
            text-align: center;
            color: white;
            background-color: #2c2c2c;
        }

        tr {
            background-color: #2c2c2c;
        }

        td img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }

        td img:hover {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            cursor: pointer;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .action-buttons button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            color: white;
        }

        .accept-button {
            background-color: #4CAF50;
        }

        .decline-button {
            background-color: #f44336;
        }

        .remove-button {
            background-color: #f44336;
        }

        button:hover {
            opacity: 0.8;
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

        .container-profile {
            margin-top: 90px;
            display: flex;
            gap: 20px;
            padding: 20px;
        }

        .card-profile {
            width: 30%;
            background-color: #2c2c2c;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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

    <div class="navbar-top">
        <div class="left-section">
            <img src="https://imageshack.com/i/pmmxUi4hp" alt="Logo" class="logo">
            <span style="margin-right: 18px;">Amigos</span>
            <a class="btn btn-danger btn-lg" style="color: white; border-radius: 5px;  margin-right: 18px;" href="profile.php">❮ Voltar</a>
            <a href="adicionar.php" style="color: white; margin-right: 18px;"><span><i class="lni lni-plus"></i>Adicionar Amigo</span></a>
        </div>
        <div class="dropdown">
            <input type="button" name="def" value="☰">
            <div class="dropdown-content">
                <a href="melodychat.php"><i class="lni lni-home-2" style="margin-right: 5px;"></i>Inicio</a>
            </div>
        </div>
    </div>

    <br><br><br>
    <div class="container">
        <!-- Tabela de Amigos -->
        <div class="table-container">
            <h2>Amigos</h2>
            <div class="scrollable-table">
                <table>
                    <tbody>
                        <?php if (mysqli_num_rows($friendsResult) > 0) :
                            while ($friend = mysqli_fetch_assoc($friendsResult)) :

                                if ($friend['pedido'] == $_SESSION['email']) {
                                    $sql = "SELECT * from melodychat where email = '" . $friend['solicitacao'] . "'";
                                } elseif ($friend['solicitacao'] == $_SESSION['email']) {
                                    $sql = "SELECT * from melodychat where email = '" . $friend['pedido'] . "'";
                                }
                                $resultado = mysqli_query($con, $sql);
                                $amigo = mysqli_fetch_assoc($resultado);


                                if (isset($_POST['remover'])) {
                                    $sql = "DELETE from friends where (pedido = '" . $_POST['email'] . "' and solicitacao = '" . $_SESSION['email'] . "') or (pedido = '" . $_SESSION['email'] . "' and solicitacao = '" . $_POST['email'] . "')";
                                    $Remove = mysqli_query($con, $sql);

                                    echo "<script>alert('Amigo Removido com sucesso.'); window.location.href='friends.php';</script>";
                                }


                                if (isset($_POST['profile_user'])) {

                                    $_SESSION['visu_perfil'] = $_POST['id'];
                                    echo "<script>window.location.href='profile_user.php';</script>";
                                }

                        ?>
                                <tr>
                                    <td>
                                        <form method="post">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($amigo['id']) ?>">
                                            <button name="profile_user" style="background-color: transparent;">
                                                <img src="<?= htmlspecialchars($amigo['path'] ?? 'melodychat.png') ?>" alt="Foto">
                                            </button>
                                        </form>

                                    </td>
                                    <td>
                                        <?= htmlspecialchars($amigo['util']) ?>
                                    </td>
                                    <td class="action-buttons">
                                        <form method="post">
                                            <input type="hidden" name="email" value="<?= htmlspecialchars($amigo['email']) ?>">
                                            <button class="remove-button" name="remover">Remover</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3">Nenhum amigo encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabela de Solicitações -->
        <div class="table-container">
            <h2>Solicitações de Amizade</h2>
            <div class="scrollable-table">
                <table>
                    <tbody>
                        <?php if (mysqli_num_rows($requestsResult) > 0) {

                            while ($request = mysqli_fetch_assoc($requestsResult)) {
                                $sql = "SELECT * from melodychat where email = '" . $request['pedido'] . "'";
                                $resultado = mysqli_query($con, $sql);
                                $request2 = mysqli_fetch_assoc($resultado);


                                if (isset($_POST['aceitar'])) {
                                    $sql = "UPDATE friends set estado = 1 where pedido = '" . $_POST['pedido'] . "' and solicitacao = '" . $_SESSION['email'] . "'";
                                    $ResultEstado = mysqli_query($con, $sql);

                                    echo "<script>alert('Solicitação Aceite.'); window.location.href='friends.php';</script>";
                                }

                                if (isset($_POST['recusar'])) {
                                    $sql = "DELETE from friends where pedido = '" . $_POST['pedido'] . "' and solicitacao = '" . $_SESSION['email'] . "'";
                                    $ResultEstado = mysqli_query($con, $sql);

                                    echo "<script>alert('Solicitação Cancelada.'); window.location.href='friends.php';</script>";
                                }


                        ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($request2['path'] ?? 'melodychat.png') ?>" alt="Foto">
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($request['pedido']) ?>
                                    </td>
                                    <td class="action-buttons">
                                        <form method="post">
                                            <input type="hidden" name="pedido" value="<?= htmlspecialchars($request['pedido']) ?>">
                                            <button class="accept-button" name="aceitar">Aceitar</button>
                                            <button class="decline-button" name="recusar">Recusar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3">Nenhuma solicitação de amizade.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
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