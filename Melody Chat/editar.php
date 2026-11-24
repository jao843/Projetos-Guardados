<?php
session_start();
require('ligacao.php');

$erros = '';
$bandeira_erro = 0;

if (!isset($_SESSION['email'])) {
    header('Location: melodychat.php');
    exit();
}

$email = $_SESSION['email'];
$utilizador = $_SESSION['util'];

if (isset($_POST['atualizar'])) {
    $novo_util = $_POST['novo_util'];
    $nova_pass = $_POST['nova_pass'];
    $pass_str = strlen($_POST['nova_pass']);

    if (!empty($novo_util) && strlen($novo_util) < 40) {
        $senha_atual = md5($_POST['pass']);
        $sql = "SELECT pass FROM melodychat WHERE email = '$email' AND pass = '$senha_atual'";

        $resultado = mysqli_query($con, $sql);

        if (mysqli_num_rows($resultado) > 0) {


            if (!empty($nova_pass)) {

                if ($pass_str >= 8) {

                    $nova_pass_md5 = md5($nova_pass);
                    $sql = "UPDATE melodychat SET util = '$novo_util', pass = '$nova_pass_md5' WHERE email = '$email'";

                    if ($atualizar_senha = mysqli_query($con, $sql)) {
                        $bandeira_erro = 1;
                    } else {
                        $erros = "<p>Error: " . $mysqli->error . "</p>";
                    }
                } else {
                    $erros = "<p>Error: Formato da Password não é aceite.. pelo menos 8 palavras</p>";
                }
            }

            if (isset($_FILES['foto_perfil'])) {
                $arquivo = $_FILES['foto_perfil'];
                if ($arquivo['size'] <= 2097152) {

                    if ($arquivo['error'] > 0) {
                    } else {




                        $pasta = "arquivos/";

                        $nomedoarquivo = $arquivo['name'];

                        $novonomedoarquivo = uniqid();

                        $extensao = strtolower(pathinfo($nomedoarquivo, PATHINFO_EXTENSION));

                        if ($extensao != "jpg" && $extensao != "png" && $extensao != "jpeg")
                            die("Tipo de arquivo nao aceite");

                        $deu_certo = move_uploaded_file($arquivo['tmp_name'], $pasta . $novonomedoarquivo . "." . $extensao);

                        $path = $pasta . $novonomedoarquivo . "." . $extensao;


                        if ($deu_certo) {
                            $sql = "UPDATE melodychat 
                                    SET nome = '$nomedoarquivo', path = '$path' 
                                    WHERE email = '" . $_SESSION['email'] . "'";

                            if ($result = mysqli_query($con, $sql)) {
                                $bandeira_erro = 1;
                            } else {
                                $erros = "<p>Error: " . $mysqli->error . "</p>";
                            }
                        } else {
                            $erros = "<p>ERRO ao enviar o arquivo</p>";
                        }
                    }
                } else {
                    $erros = ("Arquivo demasaido grande: Max 2MB");
                }
            }

            $sql_nome = "SELECT util FROM melodychat WHERE email = '" . $_SESSION['email'] . "';";
            $result_nome = mysqli_query($con, $sql_nome);
            $user_nome = mysqli_fetch_assoc($result_nome);

            if ($novo_util != $user_nome['util']) {

                $sql = "UPDATE melodychat SET util = '$novo_util' WHERE email = '$email'";
                $_SESSION['util'] = $novo_util;
                if ($result = mysqli_query($con, $sql)) {
                    $bandeira_erro = 1;
                } else {
                    $erros = "<p>Error: " . $mysqli->error . "</p>";
                }
            }
        } else {
            $erros = '[ERRO] - Palavra-passe atual incorreta.';
        }
    } else {
        $erros = '[ERRO] - Nome de utilizador inválido.';
    }
}

$sql = "SELECT * FROM melodychat WHERE email = '" . $_SESSION['email'] . "';";
$result = mysqli_query($con, $sql);
$user = mysqli_fetch_assoc($result);




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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="icon" href="transparente.png" type="image/png">
    <title>Editar Perfil</title>
    <style>
        body {
            background-color: #1e1e1e;
            color: #ffffff;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .card {
            background-color: #2c2c2c;
            color: #ffffff;
            border-radius: 5px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 10px;
            width: 85%;
            /* Diminui a largura dos cards de 90% para 85% */
            margin: 0 auto;
            /* Mantém os cards centralizados */
        }

        .card-header {
            background-color: #333333;
            border-bottom: 1px solid #555555;
            text-align: center;
            font-weight: bold;
            padding: 5px;
            /* Reduzir padding do header */
            font-size: 14px;
            /* Reduzir tamanho da fonte */
        }

        .card-body {
            padding: 10px;
            /* Reduzir padding do body do card */
        }

        .btn-outline-primary,
        .btn-outline-success,
        .btn-outline-danger {
            color: #4682b4;
            border-color: #4682b4;
            padding: 3px 10px;
            /* Reduzir padding dos botões */
            font-size: 12px;
            /* Reduzir tamanho da fonte dos botões */
        }

        .btn-outline-primary:hover,
        .btn-outline-success:hover,
        .btn-outline-danger:hover {
            background-color: #4682b4;
            color: white;
        }

        .form-control {
            background-color: #333333;
            border: 1px solid #a9a9a9;
            color: #ffffff;
            padding: 3px;
            /* Reduzir padding dos inputs */
            font-size: 12px;
            /* Reduzir tamanho da fonte */
        }

        .tooltip-inner {
            background-color: #4682b4 !important;
            color: #ffffff !important;
            font-size: 12px;
            /* Reduzir tamanho da fonte do tooltip */
        }

        .alert {
            margin-top: 5px;
            /* Reduzir margem superior dos alertas */
            padding: 5px;
            /* Reduzir padding dos alertas */
            font-size: 12px;
            /* Reduzir tamanho da fonte dos alertas */
        }

        .row {
            margin-top: 10px;
            /* Reduzir margem superior da row */
        }

        .logo-central {
            display: block;
            margin: 0 auto 10px;
            /* Reduzir margem inferior do logo */
            width: 80px;
            /* Reduzir largura do logo */
            height: auto;
            opacity: 0.9;
        }

        .card-profile img {
            width: 80px;
            /* Aumenta um pouco a foto de perfil (de 60px para 80px) */
            height: 80px;
            /* Aumenta a altura proporcionalmente */
            border-radius: 50%;
            margin: 0 auto 5px;
            display: block;
        }

        .card-profile h3 {
            text-align: center;
            margin-top: 5px;
            font-size: 16px;
            /* Aumenta o tamanho da fonte do nome (de 14px para 16px) */
        }

        .card-profile p {
            text-align: center;
            margin: 3px 0;
            color: #a0a0a0;
            font-size: 12px;
            /* Aumenta o tamanho da fonte do email e cargo (de 10px para 12px) */
        }

        input[type="email"] {
            background-color: transparent;
            border: none;
            border-bottom: 1px solid #333;
            /* Borda inferior mais fina */
            outline: none;
            padding: 2px;
            /* Reduzir padding dos inputs */
            width: 100%;
            font-size: 12px;
            /* Reduzir tamanho da fonte */
        }

        input[type="email"]:focus {
            border-bottom: 1px solid #007BFF;
            transition: border-color 0.3s ease;
            background-color: transparent;
        }

        input[type="password"] {
            background-color: transparent;
            border: none;
            border-bottom: 1px solid #333;
            outline: none;
            padding: 2px;
            width: 100%;
            font-size: 12px;
        }

        input[type="password"]:focus {
            border-bottom: 1px solid #007BFF;
            transition: border-color 0.3s ease;
            background-color: transparent;
            color: white;
        }

        input[type="text"] {
            background-color: transparent;
            border: none;
            border-bottom: 1px solid #333;
            outline: none;
            padding: 2px;
            width: 100%;
            font-size: 12px;
        }

        input[type="text"]:focus {
            border-bottom: 1px solid #007BFF;
            transition: border-color 0.3s ease;
            background-color: transparent;
            color: white;
        }

        input[type="file"] {
            display: none;
        }



        .file label {
            padding: 8px 3px;
            /* Mantém o padding vertical, ajusta horizontal se necessário */
            width: 180px;
            /* Mantém a largura aumentada */
            height: 50px;
            /* Mantém a altura fixa */
            background-color: #333;
            color: #FFF;
            text-transform: uppercase;
            text-align: center;
            /* Garante que o texto esteja centralizado horizontalmente */
            display: flex;
            /* Usa flexbox para centralizar verticalmente */
            justify-content: center;
            /* Centraliza horizontalmente */
            align-items: center;
            /* Centraliza verticalmente */
            margin-top: 3px;
            cursor: pointer;
            font-size: 12px;
            /* Aumenta o tamanho da fonte de 10px para 12px */
            line-height: normal;
            /* Remove line-height fixo, já que usamos flexbox */
            box-sizing: border-box;
            /* Inclui padding e border no cálculo da altura */
        }

        #file-feedback {
            font-style: italic;
            font-weight: bold;
            color: #28a745;
            margin-top: 2px;
            /* Reduzir margem superior */
            font-size: 10px;
            /* Reduzir tamanho da fonte */
        }

        .biografia-display {
            white-space: pre-wrap;
            /* Preserva espaços e quebras de linha */
            background-color: #333333;
            border: 1px solid #a9a9a9;
            padding: 10px;
            border-radius: 5px;
            min-height: 80px;
            /* Altura mínima para consistência */
            font-size: 12px;
            color: #ffffff;
        }
    </style>
</head>

<body>

    <?php

    $sql = "SELECT * FROM melodychat where email = '" . $_SESSION['email'] . "'";

    $result = mysqli_query($con, $sql);
    $user = mysqli_fetch_assoc($result);

    ?>
    <div class="container">
        <img src="https://imageshack.com/i/pmmxUi4hp" class="logo-central" alt="Logo Melody Chat">
        <div class="row">
            <!-- Card para exibir dados atuais -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Dados Atuais</div>
                    <div class="card-profile">
                        <br><br>
                        <img src="<?php if (isset($user['path'])) {
                                        echo $user['path'];
                                    } else { ?>melodychat.png<?php } ?>" alt="Foto de perfil">
                        <h3><?php echo $_SESSION['util']; ?></h3>
                        <p><?php echo $_SESSION['email']; ?></p>
                        <br>
                    </div>
                </div>
            </div>
            <!-- Card para alterar os dados -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Alterar Dados</div>
                    <div class="card-body">
                        <!-- Feedback Messages -->
                        <?php if ($bandeira_erro == 1) { ?>
                            <div class="alert alert-success" role="alert">
                                Dados atualizados com sucesso!
                            </div>
                        <?php } elseif (!empty($erros)) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $erros; ?>
                            </div>
                        <?php } ?>
                        <form enctype="multipart/form-data" method="post">
                            <div class="form-group">

                                <!-- foto de perfil -->
                                <label for="">Nova Foto de Perfil</label>
                                <br>
                                <div class="file">
                                    <label for="foto_perfil">Enviar Imagem</label>
                                    <input type="file" class="form-control" id="foto_perfil" name="foto_perfil">
                                    <span id="file-feedback" style="display: none; color: #28a745; font-size: 14px; margin-top: 5px;"></span>
                                </div>

                                <script>
                                    // Captura o input do arquivo e o elemento de feedback
                                    const fileInput = document.getElementById('foto_perfil');
                                    const fileFeedback = document.getElementById('file-feedback');

                                    // Monitorar mudanças no campo de upload
                                    fileInput.addEventListener('change', function() {
                                        if (fileInput.files.length > 0) {
                                            const fileName = fileInput.files[0].name; // Nome do arquivo selecionado
                                            fileFeedback.style.display = 'block'; // Torna a mensagem visível
                                            fileFeedback.textContent = `Ficheiro selecionado: ${fileName}`; // Define a mensagem
                                        } else {
                                            fileFeedback.style.display = 'none'; // Esconde a mensagem se não houver arquivo
                                        }
                                    });
                                </script>
                                <label for="novo_util">Novo Nome de Utilizador</label>
                                <input
                                    type="text"
                                    name="novo_util"
                                    class="form-control"
                                    placeholder="Digite o novo nome..."
                                    value="<?php echo $_SESSION['util']; ?>"
                                    maxlength="40"
                                    data-toggle="tooltip"
                                    data-placement="right"
                                    title="Máximo 40 caracteres.">
                            </div>
                            <div class="form-group">
                                <label for="pass">Password Atual</label>
                                <input
                                    type="password"
                                    name="pass"
                                    class="form-control"
                                    placeholder="Digite a Password atual..."
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="nova_pass">Nova Password</label>
                                <input
                                    type="password"
                                    name="nova_pass"
                                    class="form-control"
                                    placeholder="Digite a nova Password..."
                                    maxlength="35"
                                    data-toggle="tooltip"
                                    data-placement="right"
                                    title="Máximo 35 caracteres.">
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="profile.php" class="btn btn-danger">Voltar</a>
                                <input type="submit" name="atualizar" class="btn btn-success" value="Atualizar">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>

</html>