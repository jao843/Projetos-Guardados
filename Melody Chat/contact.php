<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

$bandeira = 0;
if ($estado = FALSE) {
?> <span align="center" class="btn btn-outline-danger">SITE EM MANUTENÇÃO</span> <?php
                                                                                } else {
                                                                                  if (isset($_POST['acesso'])) {
                                                                                    $bandeira = 1;
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
        margin-top: 100px;
      }

      .card {
        background-color: #2c2c2c;
        color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        padding: 30px;
        width: 30rem;
        max-height: 40rem;
        margin: 0 auto;
        text-align: center;
      }

      .contact-card {
        margin-left: 50px;
        /* Move o card mais para a direita */
      }

      .row {
        display: flex;
        justify-content: center;
        width: 100%;
      }

      .col-md-4 {
        display: flex;
        justify-content: center;
        align-items: center;
      }

      .logo-central {
        display: block;
        margin: 0 auto 20px;
        width: 110px;
        height: auto;
        opacity: 0.8;
      }

      .btn-outline-primary {
        color: #4682b4;
        border-color: #4682b4;
      }

      .btn-outline-primary:hover {
        background-color: #4682b4;
        color: white;
      }

      .btn-outline-success {
        color: #32cd32;
        border-color: #32cd32;
      }

      .btn-outline-success:hover {
        background-color: #32cd32;
        color: white;
      }

      .btn-outline-danger {
        color: #ff6347;
        border-color: #ff6347;
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

      h4,
      h2 {
        color: #ffffff;
      }

      input::placeholder {
        color: #a9a9a9;
      }

      .form-control {
        background-color: #333333;
        border: 1px solid #a9a9a9;
        color: #ffffff;
      }

      .form-control:focus {
        border-color: #4682b4;
        box-shadow: none;
      }

      input[name="assunto"],
      textarea[name="mensagem"] {
        background-color: #333333;
        color: #ffffff;
        border: 1px solid #4682b4;
        padding: 10px;
        width: 100%;
        border-radius: 5px;
      }

      input[name="assunto"]:focus,
      textarea[name="mensagem"]:focus {
        border-color: #32cd32;
        box-shadow: none;
      }

      textarea[name="mensagem"] {
        resize: none;
      }

      input[type="email"],
      input[type="password"] {
        background-color: transparent;
        border: none;
        border-bottom: 2px solid #333;
        outline: none;
        padding: 5px;
        width: 100%;
        font-size: 16px;
      }

      input[type="email"]:focus,
      input[type="password"]:focus {
        border-bottom: 2px solid #007BFF;
        transition: border-color 0.3s ease;
        background-color: transparent;
      }
    </style>
  </head>

  <body>
    <?php
                                                                                  if (isset($_POST['acesso']) && !isset($_SESSION['email'])) {
                                                                                    $_SESSION['email_2'] = $_POST['email'];
                                                                                  } elseif (isset($_SESSION['email'])) {
                                                                                    $_SESSION['email_2'] = $_SESSION['email'];
                                                                                    $bandeira = 1;
                                                                                  }



                                                                                  if (isset($_POST['mandar'])) {



                                                                                    //para enviar para o nova senha



                                                                                    require("class.phpmailer.php");
                                                                                    $mail_destino = "aluno18404@damiaodegoes.pt";

                                                                                    $sql_nome = "SELECT util FROM melodychat WHERE email = '" . $_SESSION['email_2'] . "';";
                                                                                    $resultado = mysqli_query($con, $sql_nome);

                                                                                    if ($resultado && mysqli_num_rows($resultado) > 0) {
                                                                                      $linha = mysqli_fetch_assoc($resultado);
                                                                                      $util_nome = $linha['util']; // O nome do usuário
                                                                                      $email = $_SESSION['email_2'];

                                                                                      $mensagem = 'Mensagem de: ' . $util_nome . '<br><br> ' . $_POST['mensagem'];



                                                                                      if (!isset($util_nome)) {
                                                                                        $util_nome = "guest";
                                                                                      }


                                                                                      // Enviar o e-mail
                                                                                      $mail_send = new PHPMailer();
                                                                                      $mail_send->Host = "localhost";  // especifica o servidor de e-mail
                                                                                      $mail_send->From = "$email";
                                                                                      $mail_send->FromName = "$util_nome";
                                                                                      $mail_send->AddAddress($mail_destino);
                                                                                      $mail_send->AddCC('melodychat@damiaodegoes.pt');
                                                                                      $mail_send->AddReplyTo("$email");
                                                                                      $mail_send->IsHTML(true);  // define o formato do e-mail como HTML

                                                                                      $mail_send->Subject = "Apoio";
                                                                                      $mail_send->Body = utf8_decode($mensagem);

                                                                                      if ($mail_send->Send()) { //ENVIA O MAIL
                                                                                        $envia = 1;
                                                                                      }
                                                                                    } else {
                                                                                      $email = $_SESSION['email_2'];
                                                                                      $mensagem = 'Mensagem de: guest <br><br> ' . $_POST['mensagem'];

                                                                                      $mail_send = new PHPMailer();
                                                                                      $mail_send->Host = "localhost";  // especifica o servidor de e-mail
                                                                                      $mail_send->From = "$email";
                                                                                      $mail_send->FromName = "guest";
                                                                                      $mail_send->AddAddress($mail_destino);
                                                                                      $mail_send->AddCC('apoio@melodychat.pt');
                                                                                      $mail_send->AddReplyTo("$email");
                                                                                      $mail_send->IsHTML(true);  // define o formato do e-mail como HTML

                                                                                      $mail_send->Subject = "Apoio";
                                                                                      $mail_send->Body = utf8_decode($mensagem);

                                                                                      if ($mail_send->Send()) { //ENVIA O MAIL
                                                                                        $envia = 1;
                                                                                      }
                                                                                    }
                                                                                  }




                                                                                  if (isset($_POST['mudar'])) {
                                                                                    // Verificar se o código enviado no formulário é o mesmo da sessão
                                                                                    if ($_POST['cod'] == $_SESSION['cod']) {
                                                                                      // Atualizar a senha no banco de dados
                                                                                      $pass = mysqli_real_escape_string($con, $_POST['pass']); // Protege a senha contra injeção de SQL
                                                                                      $sql = "UPDATE melodychat SET pass = '$pass' WHERE email = '" . $_SESSION['email_2'] . "';";

                                                                                      if (mysqli_query($con, $sql)) {
                                                                                        $bandeira = 1; // Sucesso
                                                                                      } else {
                                                                                        $bandeira = 0; // Falha na atualização
                                                                                      }
                                                                                    } else {
                                                                                      $bandeira = 0; // Código não corresponde
                                                                                    }
                                                                                  }


                                                                                  if (isset($envia)) {
                                                                                    if ($envia == 1) {
                                                                                      if (isset($envia)) {
    ?>
          <div class="row" align="center">
            <div class="col-md-12" align="center">
              <div class="alert alert-success" role="alert">
                EMAIL ENVIADO COM SUCESSO
              </div>
              <a href="melodychat.php" class="btn btn-outline-danger">Voltar</a>
            </div>
          </div>
        <?php
                                                                                      } else {
        ?>
          <br><br><br><br><br>
          <div class="row" align="center">
            <div class="col-md-12" align="center">
              <div class="alert alert-danger" role="alert">
                [ERRO] - O EMAIL QUE INSERIU NÃO EXISTE REGISTRADO NO MELODYCHAT
              </div>
              <a href="contact.php" class="btn btn-outline-warning btn-lg">Voltar</a>
            </div>
          </div>
        <?php
                                                                                      }
                                                                                    }
                                                                                  }


                                                                                  if (isset($bandeira)) {
                                                                                    if ($bandeira == 1) {
        ?>

        <div class="container" style="margin-top: 100px;">
          <img src="https://imageshack.com/i/pmmxUi4hp" class="logo-central" alt="Logo Melody Chat">
          <div class="row">
            <div class="col-md-4">&nbsp;</div>
            <div class="col-md-4" align-items="center">
              <div class="card">
                <div class="card-body">
                  <form method="post">
                    <h4 class="font-weight-bold">Contacte-nos</h4>
                    <label>ASSUNTO: </label>
                    <input type="text" name="assunto">
                    <label>MENSAGEM: </label>
                    <textarea name="mensagem" rows="8" style="width: 400Çpx;"></textarea>
                    <br>
                    <a href="melodychat.php" class="btn btn-outline-danger btn-lg">Voltar</a>
                    <input type="submit" name="mandar" class="btn btn-outline-success btn-lg" value="Enviar">
                  </form>
                </div>
              </div>
            </div>
            <div class="col-md-4">&nbsp;</div>
          </div>
        </div>
        <br><br><br>
      <?php
                                                                                    } else {
      ?>
        <div class="container" style="margin-top: 100px;">
          <img src="https://imageshack.com/i/pmmxUi4hp" class="logo-central" alt="Logo Melody Chat">
          <div class="row">
            <div class="col-md-4">&nbsp;</div>
            <div class="col-md-4" align="center">
              <div class="card" style="width: 18rem;">
                <div class="card-body">
                  <form method="post">
                    <h4 class="font-weight-bold">Conctacte nos</h4>
                    <input type="email" name="email" class="form-control" placeholder="Email...">
                    <br>
                    <a href="melodychat.php" class="btn btn-danger btn-lg">Voltar</a>
                    <input type="submit" name="acesso" class="btn btn-success btn-lg" value="Enviar">
                  </form>
                </div>
              </div>
            </div>
            <div class="col-md-4">&nbsp;</div>
          </div>
        </div>
  <?php
                                                                                    }
                                                                                  }
                                                                                }
  ?>
  <!-- JavaScript (Opcional) -->
  <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>

  </html>