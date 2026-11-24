<?php
session_start();
require('ligacao.php');


if ($estado = FALSE) {
?> <span align="center" class="btn btn-outline-danger">SITE EM MANUTENÇÃO</span> <?php
                                                                                    } else {
                                                                                        $erros = '';
                                                                                        $ponte = 0;

                                                                                        $sql = "SELECT id, pass FROM melodychat";
                                                                                        $result = mysqli_query($con, $sql);

                                                                                        if ($result) {
                                                                                            while ($row = mysqli_fetch_assoc($result)) {
                                                                                                $id = $row['id'];
                                                                                                $senha = $row['pass'];

                                                                                                // Verifica se a senha já está criptografada (md5 tem exatamente 32 caracteres)
                                                                                                if (strlen($senha) != 32) {
                                                                                                    // Criptografa a senha usando MD5
                                                                                                    $senha_criptografada = md5($senha);

                                                                                                    // Atualiza a senha no banco de dados
                                                                                                    $sql_update = "UPDATE melodychat SET pass = '$senha_criptografada' WHERE id = $id";
                                                                                                    mysqli_query($con, $sql_update);
                                                                                                }
                                                                                            }
                                                                                        } else {
                                                                                            echo "[ERRO] - Falha ao buscar dados: " . mysqli_error($con);
                                                                                        }



                                                                                        if (isset($_POST['criar'])) {
                                                                                            $bandeira_erro = 0;


                                                                                            if (isset($_POST['util'])) {


                                                                                                if (strlen($_POST['util']) < 40 && $_POST['util'] != '') {
                                                                                                    if ($_POST['pass'] != '' && strlen($_POST['pass']) <= 35) {
                                                                                                        $pass_str = strlen($_POST['pass']);
                                                                                                        if ($pass_str >= 8) {

                                                                                                            $sql_email = 'SELECT email FROM melodychat WHERE email ="' . $_POST['email'] . '";';
                                                                                                            $email_ver = mysqli_query($con, $sql_email);
                                                                                                            $aux = mysqli_num_rows($email_ver);
                                                                                                            // verificar se o email já existe
                                                                                                            if ($aux == 0) {
                                                                                                                // Criptografa a senha usando MD5

                                                                                                                if (isset($_POST['direitos'])) {

                                                                                                                    $senha_criptografada = md5($_POST['pass']);

                                                                                                                    $sql = sprintf(
                                                                                                                        "INSERT INTO melodychat (util, email, pass, cargo, estado) VALUES ('%s', '%s', '%s', %d, %d);",
                                                                                                                        $_POST['util'],
                                                                                                                        $_POST['email'],
                                                                                                                        $senha_criptografada,
                                                                                                                        0, // Valor numérico para cargo
                                                                                                                        0  // Valor numérico para estado
                                                                                                                    );


                                                                                                                    $direitos = "INSERT INTO direitos (email, termos) values ('" . $_POST['email'] . "', TRUE);";




                                                                                                                    if (mysqli_query($con, $sql)) {
                                                                                                                        $termos = mysqli_query($con, $direitos);
                                                                                                                        $bandeira_erro = 1;
                                                                                                                        $bandeira = 1;
                                                                                                                        if (isset($bandeira)) {
                                                                                                                            header("location:logion.php");
                                                                                                                            exit();
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        $erros = '[ERRO] - ERRO DE CÓDIGO: ' . mysqli_error($con); // Inclua o erro do MySQL para depuração
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    $erros = '[ERRO] - Tem de aceitar os termos senão não poderá progredir';
                                                                                                                }
                                                                                                            } else {
                                                                                                                $erros = '[ERRO] - EMAIL JÁ EXISTENTE';
                                                                                                            }
                                                                                                        } else {
                                                                                                            $erros = "[ERRO] - Formato da Password não é aceite.. pelo menos 8 palavras";
                                                                                                        }
                                                                                                    } else {
                                                                                                        $erros = "[ERRO] - Formato da Password não é aceite.. pelo menos 8 palavras";
                                                                                                    }
                                                                                                } else {
                                                                                                    $erros = '[ERRO] - NOME DE UTILIZADOR INVÁLIDO';
                                                                                                }
                                                                                            } else {
                                                                                                $erros = '[ERRO] - NOME DE UTILIZADOR INVÁLIDO';
                                                                                            }
                                                                                        }



                                                                                        ?>
    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
        <!-- Meta tags Obrigatórias -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="icon" href="transparente.png" type="image/png">
        <title>Criar Conta - Melodychat</title>

        <style>
            body {
                background-color: #1e1e1e;
                /* Fundo escuro para modernizar */
                color: #ffffff;
                /* Texto branco */
            }

            .card {
                background-color: #2c2c2c;
                /* Fundo escuro moderado para caixas */
                color: #ffffff;
                /* Texto branco */
                border: none;
            }

            .btn-outline-primary {
                color: #4682b4;
                /* Azul mais sutil */
                border-color: #4682b4;
            }

            .btn-outline-primary:hover {
                background-color: #4682b4;
                /* Azul ao passar o mouse */
                color: white;
            }

            .btn-outline-success {
                color: #32cd32;
                /* Verde moderado */
                border-color: #32cd32;
            }

            .btn-outline-success:hover {
                background-color: #32cd32;
                /* Verde ao passar o mouse */
                color: white;
            }

            .btn-outline-danger {
                color: #ff6347;
                /* Tom vermelho mais suave */
                border-color: #ff6347;
            }

            .btn-outline-danger:hover {
                background-color: #ff6347;
                /* Vermelho ao passar o mouse */
                color: white;
            }

            .alert-success {
                background-color: #32cd32;
                /* Verde para sucesso */
                color: #ffffff;
            }

            .alert-danger {
                background-color: #ff4500;
                /* Tom vermelho para erro */
                color: #ffffff;
            }

            h4,
            h2 {
                color: #ffffff;
                /* Títulos em branco */
            }

            input::placeholder {
                color: #a9a9a9;
                /* Placeholder cinza claro */
            }

            .form-control {
                background-color: #333333;
                /* Fundo escuro para inputs */
                border: 1px solid #a9a9a9;
                /* Borda cinza */
                color: #ffffff;
                /* Texto branco */
            }

            .form-control:focus {
                border-color: #4682b4;
                /* Borda azul ao focar */
                box-shadow: none;
                color: white;
            }


            .logo-central {
                display: block;
                margin: 0 auto;
                width: 110px;
                /* Ajuste para um tamanho menor */
                height: auto;
                opacity: 0.8;
                /* Leve transparência */
            }


            .separator {
                font-size: 14px;
                color: #888;
            }

            input[type="email"] {
                background-color: transparent;
                border: none;
                /* Remove as bordas padrão */
                border-bottom: 2px solid #333;
                /* Adiciona apenas a borda inferior */
                outline: none;
                /* Remove o contorno ao focar */
                padding: 5px;
                /* Ajusta o preenchimento para melhor aparência */
                width: 100%;
                /* Faz o input ocupar toda a largura disponível */
                font-size: 16px;
                /* Define um tamanho de fonte apropriado */
            }

            input[type="email"]:focus {
                border-bottom: 2px solid #007BFF;
                /* Cor da borda inferior ao focar */
                transition: border-color 0.3s ease;
                /* Animação suave na troca de cor */
                background-color: transparent;
            }

            input[type="password"] {
                background-color: transparent;
                border: none;
                /* Remove as bordas padrão */
                border-bottom: 2px solid #333;
                /* Adiciona apenas a borda inferior */
                outline: none;
                /* Remove o contorno ao focar */
                padding: 5px;
                /* Ajusta o preenchimento para melhor aparência */
                width: 100%;
                /* Faz o input ocupar toda a largura disponível */
                font-size: 16px;
                /* Define um tamanho de fonte apropriado */
            }

            input[type="password"]:focus {
                border-bottom: 2px solid #007BFF;
                /* Cor da borda inferior ao focar */
                transition: border-color 0.3s ease;
                /* Animação suave na troca de cor */
                background-color: transparent;
            }

            input[type="text"] {
                background-color: transparent;
                border: none;
                /* Remove as bordas padrão */
                border-bottom: 2px solid #333;
                /* Adiciona apenas a borda inferior */
                outline: none;
                /* Remove o contorno ao focar */
                padding: 5px;
                /* Ajusta o preenchimento para melhor aparência */
                width: 100%;
                /* Faz o input ocupar toda a largura disponível */
                font-size: 16px;
                /* Define um tamanho de fonte apropriado */
            }

            input[type="text"]:focus {
                border-bottom: 2px solid #007BFF;
                /* Cor da borda inferior ao focar */
                transition: border-color 0.3s ease;
                /* Animação suave na troca de cor */
                background-color: transparent;
            }

            a {
                font-size: 15px;
            }
        </style>
    </head>

    <body>

        <?php
                                                                                        if (isset($bandeira_erro)) {
                                                                                            if ($bandeira_erro == 0) {
        ?>
                <div class="row" align="center" style="margin-top: 5px;">
                    <div class="col-md-12" align="center">
                        <div class="alert alert-danger" role="alert">
                            <?php echo $erros; ?>
                        </div>
                    </div>
                </div>
            <?php
                                                                                            } else {
            ?>
                <div class="row" align="center" style="margin-top: 5px;">
                    <div class="col-md-12" align="center">
                        <div class="alert alert-success" role="alert">
                            CONTA CRIADA COM SUCCESSO
                            <?php


                            ?>

                        </div>
                    </div>
                </div>
        <?php
                                                                                            }
                                                                                        }
        ?>

        <!-- SIGN UP -->
        <div class="container" style="margin-top: 100px;">
            <img src="https://imageshack.com/i/pmmxUi4hp" class="logo-central" alt="Logo Melody Chat">
            <div class="row">
                <div class="col-md-4">&nbsp;</div>
                <div class="col-md-4" align="center">
                    <div class="card" style="width: 18rem;">
                        <div class="card-body">
                            <form method="post">
                                <h4 class="font-weight-bold">Criar Utilizador</h4>
                                <input type="text" name="util" class="form-control" placeholder="Utilizador..." required>
                                <br>
                                <input type="email" name="email" class="form-control" placeholder="Email..." required>
                                <input type="password" name="pass" class="form-control" placeholder="Password..." required>
                                <br>
                                <input type="checkbox" name="direitos" required> <a href="termos.php">Concordo com os Termos</a>
                                <br>
                                <a href="login.php">Já tens conta?</a>
                                <br>
                                <a href="melodychat.php" class="btn btn-danger btn-lg">Voltar</a>
                                <span class="separator">|</span>
                                <input type="submit" name="criar" class="btn btn-success btn-lg" value="Criar">
                                <input type="hidden" name="sign" value="">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">&nbsp;</div>
            </div>
        </div>

    <?php
                                                                                    }
    ?>
    <!-- JavaScript (Opcional) -->
    <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </body>

    </html>