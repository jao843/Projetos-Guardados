<?php
session_start();
require('ligacao.php');

if(!isset($_SESSION['email'])){
    header('location:melodychat.php');
    exit();
}else{
    $erros = '[Atualização] - Está funcionalidade ainda está em desenvolvimento, aguarde as proximas atualizações';

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
    <title>AULAS</title>

    <style>
         
    body {
    background-color: #1e1e1e; /* Fundo escuro para modernizar */
    color: #ffffff; /* Texto branco */
    }
    
    </style>
  </head>
  <body>



        <div class="row" align="center" style="margin-top: 5px;">
            <div class="col-md-12" align="center">                  
                    <div class="alert alert-sucess" role="alert">
                        <?php echo $erros; ?>
                    </div>         
                    <a href="melodychat.php" class="btn btn-outline-danger">Voltar</a>              
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