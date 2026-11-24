<?php
session_start();
require('ligacao_inserir_novo.php');

$sql_ano="select distinct ano from alunos_teste_2 order by ano asc;";
$res_ano=mysqli_query($con,$sql_ano);

$sql_turma="select distinct turma from alunos_teste_2 order by turma asc;";
$res_turma=mysqli_query($con,$sql_turma);

if(isset($_POST['inserir'])){
  $ins=0;
  $erros='';
  if(!is_numeric($_POST['nr_proc']) || strlen($_POST['nr_proc'])<5){
      $erros.='ERRO NO Nº DE PROCESSO <br>';
  }
  if(is_numeric($_POST['nome']) || strlen($_POST['nome'])==0){
      $erros.='ERRO NO NOME <br>';
  }
  if(!is_numeric($_POST['ano']) || $_POST['ano']==''){
      $erros.='ERRO NO ANO <br>';
  }
  if(is_numeric($_POST['turma']) || $_POST['turma']==''){
      $erros.='ERRO NA TURMA <br>';
  }
  if($erros==''){
      $ano_turma=$_POST['ano'].$_POST['turma'];
      //verificar se o nr_proc já existe
      $sql_proc="select * from alunos_teste_2 where nr_proc=".$_POST['nr_proc'].";";
      $res_proc=mysqli_query($con,$sql_proc);
      $num_proc=mysqli_num_rows($res_proc);
      if($num_proc==0){
          $sql_ins=sprintf("insert into alunos_teste_2 (nr_proc,nome,ano,turma,ano_turma,estado) values (%d,'%s',%d,'%s','%s',1);",$_POST['nr_proc'],$_POST['nome'],$_POST['ano'],$_POST['turma'],$ano_turma);
          
          
          //$sql_ins="insert into alunos_teste_2 (nr_proc,nome,ano,turma,ano_turma,estado) values (".$_POST['nr_proc'].",'".$_POST['nome']."',".$_POST['ano'].",'".$_POST['turma']."','".$ano_turma."',1);";
          if(mysqli_query($con,$sql_ins)){
              $ins=1;
          }
      } else {
          $erros.='Nº DE PROCESSO JÁ EXISTE!';
      }
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
    <div class="row" style="margin-top:50px;">
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-3" ><a href="index_exercicio.php" class="btn btn-outline-danger btn-lg">Voltar</a></div>
    </div>
    <?php
        if(isset($ins)){
                if($ins==0){
                    ?>
                    <div class="row" align="center">
                        <div class="col-md-12" align="center">                  
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $erros; ?>
                                </div>                       
                        </div>
                    </div>
                    <?php
                    } else {
                    ?>
                    <div class="row" align="center">
                        <div class="col-md-12" align="center">                  
                                <div class="alert alert-success" role="alert">
                                    ALUNO INSERIDO COM SUCESSO
                                </div>                       
                        </div>
                    </div>
                    <?php
                }
            }
        ?>
      <div class="row">
        <div class="col-md-4"></div>
            <div class="col-md-4" align="center" style="top: 50px;">
                <form  method="post">
                    Nº PROC.:
                    <input type="text" name="nr_proc"  value="" class="form-control">

                    NOME DO ALUNO
                    <input type="text" name="nome"  value="" class="form-control">
                        
                    ANO
                    <select name="ano" class="form-control" onchange="this.form.submit()">  
                         <option value="">TODAS</option>
                         <?php
                         while($reg_ano=mysqli_fetch_array($res_ano)){
                             if(isset($_POST['ano'])){
                                 if($_POST['ano']==$reg_ano['ano']){
                                     ?>
                                      <option value="<?php echo $reg_ano['ano']; ?>" selected><?php echo $reg_ano['ano']; ?></option>
                                    <?php
                                 } else {
                                     ?>
                                      <option value="<?php echo $reg_ano['ano']; ?>"><?php echo $reg_ano['ano']; ?></option>
                                    <?php
                                 }
                             } else {
                                 ?>
                                 <option value="<?php echo $reg_ano['ano']; ?>"><?php echo $reg_ano['ano']; ?></option>
                                 <?php
                             }
                         }
                         ?>
                     </select>
                        
                    TURMA
                    <select name="turma" class="form-control" onchange="this.form.submit()">  
                         <option value="">TODAS</option>
                         <?php
                         while($reg_turma=mysqli_fetch_array($res_turma)){
                             if(isset($_POST['turma'])){
                                 if($_POST['turma']==$reg_turma['turma']){
                                     ?>
                                      <option value="<?php echo $reg_turma['turma']; ?>" selected><?php echo $reg_turma['turma']; ?></option>
                                    <?php
                                 } else {
                                     ?>
                                      <option value="<?php echo $reg_turma['turma']; ?>"><?php echo $reg_turma['turma']; ?></option>
                                    <?php
                                 }
                             } else {
                                 ?>
                                 <option value="<?php echo $reg_turma['turma']; ?>"><?php echo $reg_turma['turma']; ?></option>
                                 <?php
                             }
                         }
                         ?>
                     </select>
                        
                    <input type="submit" name="inserir" class="btn btn-outline-success btn-lg" value="Submeter">
                </form>
            </div>
          <div class="col-md-4"></div>
      </div>

    <!-- JavaScript (Opcional) -->
    <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>