<?php
session_start();

if(isset($_POST["sub"])){
    $divisores=1;
    if($_POST['num']!='' && is_numeric($_POST['num'])){
        $divisores=0;
        $count=$_POST['num']-1;
        $resto=0;
        $numprimo=0;
        if(!isset($_SESSION['num'])){
            $_SESSION['num'] = $_POST['num'];
        }
        while($count>=2){
            $resto=$_POST['num'] % 2;
           
            if($resto == 0){
                $numprimo=1;
            }
            $count=$count-1;
        }
        if($numprimo==1){
            $string = "O número ".$_SESSION['num']." não é primo";
        }else{ 
            $string="O número ".$_SESSION['num']." é primo";
        }
    }else{
        $string="<span style='color:red'> [ERRO]: INTRUDUZA UM NÚMERO!!<span>";
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
        <div>
            <?php
            if(isset($divisores)){
                if($numprimo==1){
                    ?>
                    <div class="row" style="margin-top: 150px;" align="center">
                        <div class="col-med-12" align="center">
                            <div class="alert alert-danger" role="alert">
                                <?php echo $string; ?>
                            </div>
                            <a href="fimprimo.php">NOVO numero</a>
                        </div>
                    </div>
                <?php
                    }else{
                 
                    if(isset($_SESSION['num'])){
                    ?>
                        <div class="row" style="margin-top: 150px;" align="center">
                            <div class="col-med-12" align="center">
                                <div class="alert alert-success"role="alert">
                                    <?php echo $string; ?>
                                </div>
                                <a href="fimprimo.php">NOVO numero</a>
                            </div>
                        </div>
                    <?php
                       
                        }
                    }  
                    }else{
            ?>
        </div>
        <form method="post">
            <div class="row" style="margin-top: 150px;">
                <div class="col-med-6" align="center">
                    <input type="text" value="" name="num" class="form-control" placeholder="num">
                </div>
                <div class="col-med-6" align="center">
                    <input type="submit" value="submeter" name="sub" class="btn btn-outline-primary">
                </div>
            </div>
        </form>
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
