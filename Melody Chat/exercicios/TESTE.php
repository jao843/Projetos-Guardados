<?php

if(isset($_POST['botao'])){
    $res=0;
    if($_POST['nome']!='' && is_numeric($_POST['nome'])==false && $_POST['ano']!='' && is_numeric($_POST['ano'])==true &&  strlen($_POST['ano'])==4){
        $res=1;
        $ano=$_POST['ano'];
        $idade=2024-$ano;
        $string='OLÁ '.$_POST['nome'].', TENS '.$idade.' ANOS';              
    } else {
        $string='<span style="color:red;">[ERRO] FORMATO INVÁLIDO</span>';
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
     <div class="container">
         <?php
            if(isset($res)){
                if($res==0){
         ?>
         <div class="row" align="center">
                    <div class="col-md-12" align="center" style="top: 100px;">                  
                        <div class="alert alert-danger" role="alert">
                            <?php echo $string; ?>
                        </div>                       
                    </div>
                 </div>
                 <?php
            } else {
                ?>
                <div class="row" align="center">
                    <div class="col-md-12" align="center" style="top: 100px;">                  
                        <div class="alert alert-success" role="alert">
                            <?php echo $string; ?>
                        </div>                       
                    </div>
                 </div>
         <?php
                }
            }
         ?>
         
        <br>
        <br>
    </div>
      
    <form method="post">
             <div class="row" style="margin-top: 150px;">
                <div class="col-md-9" align="center">
                    <input type="text" name="nome" value="" placeholder="qual o seu nome?" class="form-control"> 
                </div>
                 <div class="col-md-3" align="center">
                     <input type="text" name="ano" value="" placeholder="AAAA" class="form-control">
                 </div>
             </div>
             <br>
             <div class="row">
                 <div class="col-md-12" align="center">
                     <input type="submit" name="botao" class="btn btn-outline-primary" value="submeter">
                 </div>
             </div>
         </form> 
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>