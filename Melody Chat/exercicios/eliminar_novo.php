<?php
session_start();
require('ligacao.php');
if(!$con){
    header('erro.php');
}
$erros='';
if(isset($_POST['selecionar'])){
    
    if(!is_numeric($_POST['nr_proc']) || strlen($_POST['nr_proc'])<5){
        $erros='ERRO NO Nº DE PROCESSO <br>';
    } else {
        $sql_proc="select * from alunos_teste_2 where nr_proc=".$_POST['nr_proc'].";";
        $res_proc=mysqli_query($con,$sql_proc);
        $num_proc=mysqli_num_rows($res_proc);
        if($num_proc==1){
            $reg_proc=mysqli_fetch_array($res_proc);
            $nome=$reg_proc['nome'];
            $ano=$reg_proc['ano'];
            $turma=$reg_proc['turma'];
            $ano_turma=$reg_proc['ano_turma'];
            $estado=$reg_proc['estado'];
        } else {
            $erros='[ERRO] O Nº DE PROCESSO NÃO EXISTE';
        }
    }
}
if(isset($_POST['eliminar'])){
    $eli=0;
    if($erros==''){        
        //verificar se o nr_proc já existe
        $sql_proc="select * from alunos_teste_2 where nr_proc=".$_POST['nr_proc'].";";
        $res_proc=mysqli_query($con,$sql_proc);
        $num_proc=mysqli_num_rows($res_proc);
        if($num_proc==1){
            $sql_upd=sprintf("delete from alunos_teste_2 where nr_proc=%d;",$_POST['nr_proc']);
            
            if(!mysqli_query($con,$sql_upd)){
                $erros.='OCORREU UM ERRO NA ELIMINAÇÃO. POR FAVOR TENTE DE NOVO';
            } else {
                $eli=1;
            }
        } else {
            $erros.='Nº DE PROCESSO INEXISTENTE!';
        }
    }
}

$sql_ano="select distinct ano from alunos_teste_2 order by ano asc;";
$res_ano=mysqli_query($con,$sql_ano);

$sql_turma="select distinct turma from alunos_teste_2 order by turma asc;";
$res_turma=mysqli_query($con,$sql_turma);
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
      <div class="container" style="margin-top: 150px;"> 
          <?php
          if(isset($erros)){      
              if($erros!=''){
                 ?>
                 <div class="row" align="center">
                     <div class="col-md-12" align="center">                  
                           <div class="alert alert-danger" role="alert">
                               <?php echo $erros; ?>
                           </div>                       
                     </div>
                     <a href="eliminar.php" class="btn btn-outline-danger btn-lg">OK</a>
                 </div>
                 <?php    
            } else {
                  if(isset($eli)){
                      if($eli==1){
                            ?>
                            <div class="row" align="center">
                                 <div class="col-md-12" align="center">                  
                                       <div class="alert alert-success" role="alert">
                                           ALUNO ELIMINADO COM SUCESSO.
                                       </div>                       
                                  </div>
                            </div>
                            <a href="eliminar_novo.php" class="btn btn-outline-danger btn-lg">OK</a>
                            <?php  
                      } else {
                          ?>
                            <div class="row" align="center">
                                 <div class="col-md-12" align="center">                  
                                       <div class="alert alert-danger" role="alert">
                                           <?php echo $erros; ?>
                                       </div>                       
                                 </div>
                                 <a href="eliminar_novo.php" class="btn btn-outline-danger btn-lg">OK</a>
                             </div>
                            <?php  
                      }
                  } else {
                      ?>
                      <div class="row">
                          <div class="col-md-3">&nbsp;</div>
                          <div class="col-md-6" align="center">
                              <?php
                              if(!isset($_POST['nr_proc'])){
                                  ?>
                                    <form  method="post">
                                        Nº DE PROCESSO PARA ELIMINAR
                                        <input type="text" name="nr_proc"  value="" class="form-control">
                                        <a href="index_exercicio.php" class="btn btn-outline-danger btn-lg">VOLTAR</a>
                                        <input type="submit" name="selecionar" class="btn btn-outline-success btn-lg" value="Submeter">
                                    </form>

                                  <?php
                              } else {
                                  ?>
                                    <form  method="post">
                                        Nº PROC.:
                                        <input type="text" value="<?php echo $_POST['nr_proc']; ?>" class="form-control" disabled>
                                        <br>
                                        NOME DO ALUNO
                                        <input type="text" value="<?php echo $nome; ?>" class="form-control" disabled>
                                        <br>
                                        ANO
                                        <select name="ano" class="form-control" disabled>
                                            <option value="">...</option>
                                            <?php
                                            while($reg_ano=mysqli_fetch_array($res_ano)){
                                                if($reg_ano['ano']==$ano){
                                                    ?>
                                                    <option value="<?php echo $reg_ano['ano']; ?>" selected><?php echo $reg_ano['ano']; ?>º</option>
                                                    <?php 
                                                } else {
                                                    ?>
                                                    <option value="<?php echo $reg_ano['ano']; ?>"><?php echo $reg_ano['ano']; ?>º</option>
                                                    <?php    
                                                }
                                            }
                                            ?>
                                        </select>
                                        <br>
                                        TURMA
                                        <select name="turma" class="form-control" disabled>
                                            <option value="">...</option>
                                            <?php
                                            while($reg_turma=mysqli_fetch_array($res_turma)){
                                                if($reg_turma['turma']==$turma){
                                                        ?>
                                                        <option value="<?php echo $reg_turma['turma']; ?>" selected><?php echo $reg_turma['turma']; ?>º</option>
                                                        <?php 
                                                    } else {
                                                        ?>
                                                        <option value="<?php echo $reg_turma['turma']; ?>"><?php echo $reg_turma['turma']; ?>º</option>
                                                        <?php    
                                                    }
                                            }
                                            ?>
                                        </select>
                                        <br>
                                        <select name="estado" class="form-control" disabled>
                                             <?php           
                                                 if($estado==1){
                                                     echo '<option value="1" selected>ATIVO</option>';
                                                     echo '<option value="0">INATIVO</option>';
                                                 } else {
                                                     echo '<option value="1" >ATIVO</option>';
                                                     echo '<option value="0" selected>INATIVO</option>';
                                                 }                                             
                                             ?>
                                         </select>
                                        <br>
                                        <a href="index_exercicio.php" class="btn btn-outline-danger btn-lg">VOLTAR</a>
                                        <a href="eliminar_novo.php" class="btn btn-outline-warning btn-lg">CANCELAR</a>
                                        <input type="submit" name="eliminar" class="btn btn-outline-success btn-lg" value="ELIMINAR">
                                        <input type="hidden" name="nr_proc" value="<?php echo $_POST['nr_proc']; ?>">
                                    </form>
                                    <?php
                              }
                              ?>
                            </div>
                            <div class="col-md-3">&nbsp;</div>
                        </div>
                        <?php
                  }
              }
          }
          ?>
      </div>

    <!-- JavaScript (Opcional) -->
    <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>