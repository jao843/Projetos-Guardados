<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

if (!isset($_SESSION['email'])) {

    header('location:melodychat.php');
    exit();
}

if (isset($_POST['procurar'])) {

    $id = mysqli_real_escape_string($con, trim($_POST['id']));
    if (!is_numeric($id)) {
        $erro = "ID inválido. Por favor, insira um número.";
    } else {
        $sql = "SELECT * FROM melodychat WHERE id = $id";
        $resultId = mysqli_query($con, $sql);
        $veri = mysqli_num_rows($resultId);
        $bandeira = 1;
        if ($veri == 0) {
            $erro = "Pessoa não encontrada";
            $aux = 0;
        } else {
            $aux = 1;
            $perfil = mysqli_fetch_assoc($resultId);
            $_SESSION['emailAmigo'] = $perfil['email'];
        }
    }
}


if (isset($_POST['adicionar'])) {


    $id_pedido = $_SESSION['email']; // IDdo usuário que envia o pedido (usuário logado)
    $id_solicitacao = $_SESSION['emailAmigo']; // ID do usuário que recebe o pedido

    // Verificar se já existe um pedido de amizade
    $verifica_sql = "SELECT * FROM friends WHERE pedido = '$id_pedido' AND solicitacao = '$id_solicitacao'";
    $resultado_verifica = mysqli_query($con, $verifica_sql);

    if (mysqli_num_rows($resultado_verifica) > 0) {
        $alert = "Pedido de amizade já foi enviado!";
    } else {
        $id_pedido = mysqli_real_escape_string($con, $id_pedido);
        $id_solicitacao = mysqli_real_escape_string($con, $id_solicitacao);

        // Consulta SQL
        $sql_insert = "INSERT INTO friends (pedido, solicitacao, estado) VALUES ('$id_pedido', '$id_solicitacao', false)";

        // Verifica se a consulta foi bem-sucedida
        if (mysqli_query($con, $sql_insert)) {
            $alert = "Pedido de amizade enviado com sucesso";

            $sql = "SELECT * FROM melodychat where email = '$id_pedido'";
            $query = mysqli_query($con, $sql);
            $dados = mysqli_fetch_assoc($query);

            $nome = $dados['util'];
            $sql = "INSERT INTO notifications (email_user, email_user_causa, link, is_read, titulo, texto) VALUES ('$id_solicitacao','$id_pedido','friends.php', FALSE, 'Pedido de Amizade', 'Tem um pedido de amizade enviado por: $nome')";
            $query = mysqli_query($con, $sql);
        } else {
            // Exibe o erro diretamente no navegador, se houver
            $erro = "Erro ao enviar pedido de amizade";
        }
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


        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #181818;
            color: white;
        }

        .container-cards {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 100px;
        }

        .card-main,
        .card-search,
        .card-found {
            background-color: #2c2c2c;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            color: white;
        }

        .card-main {
            width: 30%;
            text-align: center;
        }

        .card-main h1 {
            font-size: 50px;
            margin: 20px 0;
        }

        .card-search form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .card-search input[type="text"] {
            padding: 10px;
            border-radius: 5px;
            border: none;
            font-size: 16px;
        }

        .card-search button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .card-search button:hover {
            opacity: 0.8;
        }


        .card-search {
            width: 60%;
            background-color: #2c2c2c;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            color: white;
            margin-bottom: 20px;
            /* Espaço para a próxima seção */
        }

        .card-result {
            width: 60%;
            /* Mesma largura da card-search */
            max-width: 600px;
            /* Definir uma largura fixa para não ficar muito larga */
            background-color: #1a1a1a;
            /* Tom mais escuro */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            color: white;
            display: none;
            /* Oculto até que haja resultado */
            margin: 0 auto;
            /* Centraliza a card */
        }


        .card-result img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid #555;
            /* Destaque para a imagem */
        }

        .card-result .details span {
            font-size: 18px;
            font-weight: bold;
        }

        .card-result .details small {
            font-size: 14px;
            color: #a0a0a0;
            /* Cor mais clara para subtítulos */
        }

        .details img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
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
        }


        td img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>

<body>

    <div class="navbar-top">
        <div class="left-section">
            <img src="https://imageshack.com/i/pmmxUi4hp" alt="Logo" class="logo">
            <span style="margin-right: 18px;">Adicionar</span>
            <br><br><br><br><br><a class="btn btn-danger btn-lg" style="color: white; border-radius: 5px;" href="friends.php">❮ Voltar</a>



        </div>
        <div class="dropdown">
            <input type="button" name="def" value="☰">
            <div class="dropdown-content">
                <a href="melodychat.php"><i class="lni lni-home-2" style="margin-right: 5px;"></i>Inicio</a>
            </div>
        </div>
    </div>

    <?php
    if (isset($alert)) {
    ?>
        <br><br><br>
        <div class='alert alert-success' role='alert'>
            <?php echo $alert; ?>
        </div>
    <?php
    }
    ?>


    <div class="container-cards">
        <!-- Card do ID principal -->

        <div class="card-main">
            <?php 
            
            $sql = "SELECT * from melodychat where email = '".$_SESSION['email']."'";
            $query = mysqli_query($con, $sql);
            $id = mysqli_fetch_assoc($query);
            
            ?>
            <h2>Seu ID</h2>
            <h1># <?php echo $id['id']; ?></h1> <!-- Exibe o ID do usuário -->

        </div>

        <!-- Card de pesquisa e adicionar amigos -->
        <div class="card-search">

            <?php
            if (isset($bandeira)) {
                if (isset($aux)) {
                    if ($aux = 1) {

                        if (isset($erro)) {
            ?>
                            <br><br><br>
                            <div class='alert alert-danger' role='alert'>
                                <?php echo $erro; ?>
                            </div>
                            <a href="adicionar.php" class="btn btn-danger btn-lg" style="align-items: center">Tentar novamente</a>
                        <?php

                        } else {



                        ?>
                            <table>
                                <tbody>



                                    <div class="details">
                                        <a href="adicionar.php" class="btn btn-danger" style="align-items: center">Voltar</a>
                                        <tr>
                                            <td>
                                                <img src="<?= htmlspecialchars($perfil['path'] ?? 'melodychat.png') ?>" alt="Foto">
                                            </td>
                                            <td>
                                                <p><?php echo $perfil['util']; ?></p>
                                            </td>
                                            <td>

                                                <form method="POST">
                                                    <input type="hidden" name="friend_id" value="<?php echo $perfil['id']; ?>">
                                                    <button type="submit" name="adicionar">
                                                        <i class="lni lni-user-plus"></i> Adicionar Amigo
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    </div>
                                </tbody>
                            </table>
                <?php
                        }
                    }
                }
            } else {


                ?>
                <h2>Procurar e Adicionar Amigos</h2>
                <form method="POST">
                    <input type="text" name="id" placeholder="Digite o ID da pessoa" required>
                    <button type="submit" name="procurar">Procurar</button>
                </form>
            <?php
            }
            ?>
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