<?php
session_start();
require('ligacao.php');



$erros='';
$ponte=0;

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
  if (strlen($_POST['util']) < 40 && $_POST['util'] != '') {
      if ($_POST['pass'] != '' && strlen($_POST['pass']) <= 35) {
          $sql_email = 'SELECT email FROM melodychat WHERE email ="' . $_POST['email'] . '";';
          $email_ver = mysqli_query($con, $sql_email);
          $aux = mysqli_num_rows($email_ver);
          // verificar se o email já existe
          if ($aux == 0) {
              // Criptografa a senha usando MD5
              $senha_criptografada = md5($_POST['pass']);
              
              $sql = sprintf(
                  "INSERT INTO melodychat (util, pass, email) VALUES ('%s', '%s', '%s');",
                  $_POST['util'],
                  $senha_criptografada,
                  $_POST['email']
              );
              
              if (mysqli_query($con, $sql)) {
                  $bandeira_erro = 1;
              } else {
                  $erros = '[ERRO] - ERRO DE CÓDIGO';
              }
          } else {
              $erros = '[ERRO] - EMAIL JÁ EXISTENTE';
          }
      } else {
          $erros = '[ERRO] - O FORMATO DA PALAVRA PASS ESTÁ ERRADO';
      }
  } else {
      $erros = '[ERRO] - NOME DE UTILIZADOR INVÁLIDO';
  }
}

if (isset($_POST['entrar'])) {
  if ($_POST['email'] != '' && $_POST['pass'] != '') {
      // Criptografa a senha inserida pelo usuário para comparação
      $senha_criptografada = md5($_POST['pass']);
      
      $sql_id = 'SELECT * FROM melodychat WHERE email LIKE "' . $_POST['email'] . '" AND pass LIKE "' . $senha_criptografada . '";';
      $id_resul = mysqli_query($con, $sql_id);
      $num_id = mysqli_num_rows($id_resul);
      
      if ($num_id > 0) {
          $row_nome = mysqli_fetch_assoc($id_resul);
          $_SESSION['email'] = $_POST['email'];
          $_SESSION['util'] = $row_nome['util'];
          header('location:melodychat.php');
          exit();
      } else {
          $ponte = 1;
          $erros .= '[ERRO] - PALAVRA PASSE OU EMAIL ERRADOS';
      }
  } else {
      $erros .= '[ERRO] - SEM VALORES NOS CAMPOS';
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

    <title>AULAS</title>

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
     if(!isset($_POST['sign']) && !isset($_POST['login'])){
        ?>
        <!-- MENU -->
         
          <div class="conatiner" style="margin-top: 50px;">
          <img src="https://imageshack.com/i/pmmxUi4hp" class="logo-central" alt="Logo Melody Chat">
            <div class="row" align="center">
              <div class="col-md-4">&nbsp;</div>
              <div class="col-md-4">
                <h2 class="font-weight-bold">Melody Chat</h2>
              </div>
              <div class="col-md-4">&nbsp;</div>
            </div>
          </div>


          <div class="container" style="margin-top: 50px;"></div>
            <div class="row">
                <div class="col-md-4">&nbsp;</div>
                <div class="col-md-4" align="center">
                    <form method="post">
                        <input type="submit" name="sign" class="btn btn-outline-primary btn-lg btn-block" value="Sign up">
                        <input type="submit" name="login" class="btn btn-outline-primary btn-lg btn-block" value="Login">
                    </form>
                </div>
                <div class="col-md-4">&nbsp;</div>
              </div>
            </div>
          <?php
     } else {
     
            if(isset($bandeira_erro)){
                if($bandeira_erro==0){
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
                                    </div>                       
                            </div>
                        </div>
                        <?php
                }
              }
              
              if(isset($_POST['entrar'])){
                   if(isset($ponte)){
                      ?>
                      <div class="row" align="center" style="margin-top: 5px;">
                        <div class="col-md-12" align="center">                  
                            <div class="alert alert-danger" role="alert">
                                <?php echo $erros; ?>
                            </div>                       
                          </div>
                      </div>
                      <?php
                      }
                  }

        

        if(isset($_POST['login'])){
          ?>
          <!-- LOGIN -->
          <div class="container" style="margin-top: 100px;">
          <img src="https://imageshack.com/i/pmmxUi4hp" class="logo-central" alt="Logo Melody Chat">
            <div class="row">
              <div class="col-md-4">&nbsp;</div>
              <div class="col-md-4" align="center">
                <div class="card" style="width: 18rem;">
                  <div class="card-body">
                    <form method="post">
                        <h4 class="font-weight-bold">Iniciar Sessão</h4>
                        <input type="email" name="email" class="form-control" placeholder="Email...">
                        <input type="password" name="pass" class="form-control" placeholder="Password...">
                        <br>
                        <a href="index_login.php" class="btn btn-outline-danger btn-lg" >Voltar</a>
                        <input type="submit" name="entrar" class="btn btn-outline-success btn-lg" value="Entrar">
                        <input type="hidden" name="login" value="">
                    </form>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
              </div>
            </div>
          </div>
        <?php
        } 
        if(isset($_POST['sign'])){
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
                            <input type="text" name="util" class="form-control" placeholder="Utilizador...">
                            <br>
                            <input type="email" name="email" class="form-control" placeholder="Email...">
                            <input type="password" name="pass" class="form-control" placeholder="Password...">
                            <br>
                            <a href="index_login.php" class="btn btn-outline-danger btn-lg" >Voltar</a>
                            <input type="submit" name="criar" class="btn btn-outline-success btn-lg" value="Criar">
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
    }

    ?>



    <!-- JavaScript (Opcional) -->
    <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>