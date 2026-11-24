<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO

if(isset($_POST['botao'])){
    
    if( strlen($_POST['nome'])>0 ){ // TESTAR SE A VARIÁVEL nome TEM CARACTERES
        
        if(!is_numeric($_POST['nome'])){
            
            if(!isset($_SESSION['nome'])){ // VERIFICAR SE JÁ EXISTE A VARIÁVEL DE SESSÃO

                $_SESSION['nome'] = $_POST['nome']; // CRIAR VARIÁVEL DE SESSÃO
            }
        } else {
            
            $msg='SEM LETRAS!!!';
        }
        
    } else {
        
        $msg='SEM NOME!!!';
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
  </head>
  <body>
      <?php
      if(isset($msg)){
          ?>
          <div class="row" align="center">
              <div class="col-md-12" align="center" style="top: 150px;">                  
                    <div class="alert alert-danger" role="alert">
                        <?php echo $msg; ?>
                    </div>                       
              </div>
          </div>
          <?php
      }
      if(isset($_SESSION['nome'])){
        ?>
          <div class="row" align="center">
              <div class="col-md-12" align="center" style="top: 150px;">                  
                    <div class="alert alert-success" role="alert">
                        <?php echo 'OLÁ '.$_SESSION['nome'] ?>
                        
                    </div>   
                    <a href="fim.php">NOVO NOME</a>
              </div>
              
          </div>
          <?php
      } else {
          ?>
          <div class="row">
              <div class="col-md-12" align="center" style="top: 150px;">
                  <form method="post">
                        <input type="text" name="nome">
                        <input type="submit" name="botao" value="SUBMETER">
                  </form>

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