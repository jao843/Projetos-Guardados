<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

$alert = '';
if (isset($_POST['mandar'])) {
    if (!isset($_SESSION['email'])) {
        $alert = '<div class="alert alert-danger" role="alert">Erro: Sessão do utilizador não encontrada. Inicie Sessão novamente.</div>';
    } else {
        $email_user = mysqli_real_escape_string($con, $_POST['email']);
        $titulo = mysqli_real_escape_string($con, $_POST['assunto']);
        $texto = mysqli_real_escape_string($con, $_POST['mensagem']);
        $link = mysqli_real_escape_string($con, $_POST['link']);
        $email_user_causa = mysqli_real_escape_string($con, $_SESSION['email']);

        // Server-side email validation
        $sql = "SELECT email FROM melodychat WHERE email = '$email_user'";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            // Email exists, proceed with INSERT
            $insert_sql = "INSERT INTO notifications (email_user, email_user_causa, link, is_read, titulo, texto) VALUES ('$email_user', '$email_user_causa', '$link', 0, '$titulo', '$texto')";
            if (mysqli_query($con, $insert_sql)) {
                $alert = '<div class="alert alert-success" role="alert">Notificação enviada com sucesso!</div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">Erro ao enviar notificação: ' . mysqli_error($con) . '</div>';
            }
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Email não encontrado na base de dados!</div>';
        }
        mysqli_close($con);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <!-- Meta tags Obrigatórias -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="icon" href="transparente.png" type="image/png">
    <title>Melody Chat</title>

    <style>
        body {
            background-color: #1e1e1e;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            width: 100%;
            margin-top: 50px;
        }

        .card {
            background-color: #2c2c2c;
            color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            padding: 20px;
            width: 32rem;
            max-height: 35rem;
            margin: 0 auto;
            text-align: center;
        }

        .row {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .col-md-6 {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo-central {
            display: block;
            margin: 0 auto 15px;
            width: 100px;
            height: auto;
            opacity: 0.9;
        }

        .btn-outline-primary {
            color: #4682b4;
            border-color: #4682b4;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: #4682b4;
            color: white;
        }

        .btn-outline-success {
            color: #32cd32;
            border-color: #32cd32;
            transition: all 0.3s ease;
        }

        .btn-outline-success:hover {
            background-color: #32cd32;
            color: white;
        }

        .btn-outline-danger {
            color: #ff6347;
            border-color: #ff6347;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            background-color: #ff6347;
            color: white;
        }

        .alert-success {
            background-color: #32cd32;
            color: #ffffff;
            text-align: center;
        }

        .alert-danger {
            background-color: #ff4500;
            color: #ffffff;
            text-align: center;
        }

        h4 {
            color: #ffffff;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            color: #a9a9a9;
            font-size: 12px;
            margin-bottom: 4px;
            display: block;
        }

        .form-control {
            background-color: #333333;
            border: 1px solid #555555;
            color: #ffffff;
            border-radius: 5px;
            padding: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .form-control:focus {
            border-color: #4682b4;
            background-color: #3a3a3a;
            box-shadow: 0 0 5px rgba(70, 130, 180, 0.5);
            outline: none;
            color: white;
        }

        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
        }

        input::placeholder,
        textarea::placeholder {
            color: #777777;
            font-style: italic;
        }

        textarea[name="mensagem"] {
            resize: none;
            height: 80px;
        }

        .word-count {
            color: #a9a9a9;
            font-size: 10px;
            text-align: right;
            margin-top: 4px;
        }

        .btn-lg {
            padding: 8px 16px;
            font-size: 14px;
        }

        .email-valid {
            border-color: #32cd32 !important;
            box-shadow: 0 0 5px rgba(50, 205, 50, 0.5) !important;
        }

        .email-invalid {
            border-color: #ff4500 !important;
            box-shadow: 0 0 5px rgba(255, 69, 0, 0.5) !important;
        }

        .alert-container {
            width: 100%;
            max-width: 32rem;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="https://imageshack.com/i/pmmxUi4hp" class="logo-central" alt="Logo Melody Chat">
        <div class="alert-container">
            <?php echo $alert; ?>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form id="notificationForm" method="post">
                            <h4 class="font-weight-bold">Criar Nova Notificação</h4>
                            <div class="form-group">
                                <label for="email">EMAIL DO DESTINATÁRIO:</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Digite o email do destinatário" oninput="checkEmail(this)" required>
                            </div>
                            <div class="form-group">
                                <label for="assunto">ASSUNTO:</label>
                                <input type="text" name="assunto" id="assunto" class="form-control" placeholder="Digite o assunto" required>
                            </div>
                            <div class="form-group">
                                <label for="mensagem">MENSAGEM (máx. ~20 palavras):</label>
                                <textarea name="mensagem" id="mensagem" class="form-control" placeholder="Digite a mensagem" rows="3" maxlength="150" oninput="countWords(this)" required></textarea>
                                <div class="word-count" id="word-count">0 palavras</div>
                            </div>
                            <div class="form-group">
                                <label for="link">LINK DE REDIRECIONAMENTO:</label>
                                <select name="link" id="link" class="form-control" required>
                                    <option value="" disabled selected>Selecione uma página</option>
                                    <option value="admin.php">Painel de Administração</option>
                                    <option value="friends.php">Lista de Amigos</option>
                                    <option value="chats.php">Chats</option>
                                    <option value="server.php">Grupos</option>
                                    <option value="profile.php">Perfil</option>
                                    <option value="status.php">Status do Spotify</option>
                                    <option value="melodychat.php">Inicio</option>
                                </select>
                            </div>
                            <div class="form-group" style="display: flex; justify-content: space-between;">
                                <a href="admin.php" class="btn btn-outline-danger btn-lg">Voltar</a>
                                <input type="submit" name="mandar" class="btn btn-outline-success btn-lg" value="Enviar">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <!-- JavaScript (Opcional) -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script>
        function countWords(textarea) {
            const text = textarea.value.trim();
            const words = text === '' ? 0 : text.split(/\s+/).length;
            document.getElementById('word-count').textContent = `${words} palavras`;
            if (words > 20) {
                textarea.value = text.split(/\s+/).slice(0, 20).join(' ');
                document.getElementById('word-count').textContent = '20 palavras';
            }
        }

        function checkEmail(input) {
            const email = input.value.trim();
            if (email === '') {
                input.classList.remove('email-valid', 'email-invalid');
                return;
            }

            $.ajax({
                url: 'ajax/check_email.php',
                type: 'POST',
                data: { email: email },
                dataType: 'json',
                success: function(response) {
                    if (response.exists) {
                        input.classList.remove('email-invalid');
                        input.classList.add('email-valid');
                    } else {
                        input.classList.remove('email-valid');
                        input.classList.add('email-invalid');
                    }
                },
                error: function() {
                    input.classList.remove('email-valid', 'email-invalid');
                }
            });
        }
    </script>
</body>
</html>