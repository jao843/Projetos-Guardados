<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');


if($estado = FALSE){
  ?> <span align="center" class="btn btn-outline-danger">SITE EM MANUTENÇÃO</span> <?php
}else{

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
        background-color: #1e1e1e; /* Fundo escuro para modernizar */
        color: #ffffff; /* Texto branco */
      }

      .card {
        background-color: #2c2c2c; /* Fundo escuro moderado para caixas */
        color: #ffffff; /* Texto branco */
        border: none;
      }

      .btn-outline-primary {
        color: #4682b4; /* Azul mais sutil */
        border-color: #4682b4;
      }

      .btn-outline-primary:hover {
        background-color: #4682b4; /* Azul ao passar o mouse */
        color: white;
      }

      .btn-outline-success {
        color: #32cd32; /* Verde moderado */
        border-color: #32cd32;
      }

      .btn-outline-success:hover {
        background-color: #32cd32; /* Verde ao passar o mouse */
        color: white;
      }

      .btn-outline-danger {
        color: #ff6347; /* Tom vermelho mais suave */
        border-color: #ff6347;
      }

      .btn-outline-danger:hover {
        background-color: #ff6347; /* Vermelho ao passar o mouse */
        color: white;
      }

      .alert-success {
        background-color: #32cd32; /* Verde para sucesso */
        color: #ffffff;
      }

      .alert-danger {
        background-color: #ff4500; /* Tom vermelho para erro */
        color: #ffffff;
      }

      h4, h2 {
        color: #ffffff; /* Títulos em branco */
      }

      input::placeholder {
        color: #a9a9a9; /* Placeholder cinza claro */
      }

      .form-control {
        background-color: #333333; /* Fundo escuro para inputs */
        border: 1px solid #a9a9a9; /* Borda cinza */
        color: #ffffff; /* Texto branco */
      }

      .form-control:focus {
        border-color: #4682b4; /* Borda azul ao focar */
        box-shadow: none;
      }

      .logo-central {
        display: block;
        margin: 0 auto;
        width: 110px; /* Ajuste para um tamanho menor */
        height: auto;
        opacity: 0.8; /* Leve transparência */
      }
    </style>
  </head>
  <body>
    <?php 


    if(isset($_POST['mandar'])){
        function gerarCodigo($tamanho = 8) {
            // Definir os caracteres possíveis (alfabeto)
            $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            
            // Embaralhar os caracteres e pegar apenas o número desejado
            $codigo = substr(str_shuffle($caracteres), 0, $tamanho);
            
            return $codigo;
        }


        
        
        // Gerar um código de 8 caracteres aleatórios
        $cod = gerarCodigo(8);

        //para enviar para o nova senha
        $_SESSION['email_2'] = $_POST['email'];
        
        // Salvar o código gerado na sessão
        $_SESSION['cod'] = $cod;

        require("class.phpmailer.php");
        $mail_destino = $_POST['email'];

        $sql_nome = "SELECT util FROM melodychat WHERE email = '".$_POST['email']."';";
        $resultado = mysqli_query($con, $sql_nome);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $linha = mysqli_fetch_assoc($resultado);
            $util = $linha['util']; // O nome do usuário
        

        $mensagem = 'Olá '.$util.', sabemos que perdeu a sua senha por isso vamos refaze-lá<br><br>Guarde este código para a confirmação: '.$cod.'';

        // Enviar o e-mail
        $mail_send = new PHPMailer();            
        $mail_send->Host = "localhost";  // especifica o servidor de e-mail
        $mail_send->From = "melodychat@damiaodegoes.pt";
        $mail_send->FromName = 'Melodychat';
        $mail_send->AddAddress($mail_destino);
        $mail_send->AddCC('melodychat@damiaodegoes.pt');
        $mail_send->AddReplyTo('aluno18404@damiaodegoes.pt');
        $mail_send->IsHTML(true);  // define o formato do e-mail como HTML

        $mail_send->Subject = "Recuperação";
        $mail_send->Body = utf8_decode($mensagem);

        if($mail_send->Send()){ //ENVIA O MAIL
          $envia=1;
        }
    }else{
      $envia=2;
    } 
  }




    if (isset($_POST['mudar'])) {
      // Verificar se o código enviado no formulário é o mesmo da sessão
      if ($_POST['cod'] == $_SESSION['cod']) {
          // Atualizar a senha no banco de dados
          $pass = mysqli_real_escape_string($con, $_POST['pass']); // Protege a senha contra injeção de SQL
          $sql = "UPDATE melodychat SET pass = '$pass' WHERE email = '".$_SESSION['email_2']."';";

          if (mysqli_query($con, $sql)) {
              $bandeira = 1; // Sucesso
          } else {
              $bandeira = 0; // Falha na atualização
          }
      } else {
          $bandeira = 0; // Código não corresponde
      }
    }


    if (isset($bandeira)) {
      if($bandeira==1){
          ?>
          <div class="row" align="center">
              <div class="col-md-12" align="center">
                  <div class="alert alert-success" role="alert">
                      PALAVRA PASSE RENOVADA
                      <?php $_SESSION['cod'] = 1; ?>
                  </div>
                  <br>
                  <a href="melodychat.php" class="btn btn-outline-warning btn-lg">Voltar</a>
              </div>
          </div>
          <?php
      } else {
          ?>
          <div class="row" align="center">
              <div class="col-md-12" align="center">
                  <div class="alert alert-danger" role="alert">
                      [ERRO] - TENTE DE NOVO
                  </div>
                  <br>
                  <a href="recuperacao.php" class="btn btn-outline-warning btn-lg">Voltar</a>
              </div>
          </div>
          <?php
      }

      }else{    





    if(isset($envia)){
            if($envia==1){
                ?>  
                <div class="row" align="center">
                    <div class="col-md-12" align="center" >                  
                        <div class="alert alert-success" role="alert">
                             EMAIL ENVIADO COM SUCESSO
                        </div>                    
                    </div>
                 </div>
                 
                 
                 <div class="container" style="margin-top: 100px;">
                    <img src="https://imageshack.com/i/pmmxUi4hp" class="logo-central" alt="Logo Melody Chat">
                    <div class="row">
                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-4" align="center">
                            <div class="card" style="width: 18rem;">
                                <div class="card-body">
                                    <form method="post">
                                        <h4 class="font-weight-bold">Recuperação</h4>
                                        <input type="text" name="cod" class="form-control" placeholder="Código..." required>
                                        <input type="password" name="pass" class="form-control" placeholder="Nova Pass..." required>
                                        <br>
                                        <a href="login.php" class="btn btn-danger btn-lg">Voltar</a>
                                        <input type="submit" name="mudar" class="btn btn-success btn-lg" value="Alterar">
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">&nbsp;</div>
                    </div>
                </div>
                        <?php
                    } else {
                        ?>  
                        <br><br><br><br><br>
                        <div class="row" align="center">
                            <div class="col-md-12" align="center" >                  
                                <div class="alert alert-danger" role="alert">
                                    [ERRO] - O EMAIL QUE INSERIU NÃO EXISTE REGISTRADO NO MELODYCHAT
                                </div>                       
                              <a href="recuperacao.php" class="btn btn-warning btn-lg">Voltar</a>
                            </div>
                        </div>                
                <?php
            }
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
                    <h4 class="font-weight-bold">Recuperação</h4>
                      <input type="email" name="email" class="form-control" placeholder="Email...">
                      <br>
                      <a href="login.php" class="btn btn-danger btn-lg" >Voltar</a>
                      <input type="submit" name="mandar" class="btn btn-success btn-lg" value="Enviar">
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
