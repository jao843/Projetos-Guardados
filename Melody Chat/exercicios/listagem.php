<?php
//PROGRAMA PARA LISTAR UMA TABELA DE UMA BASE DE DADOS
session_start();
require('ligacao.php'); // FICHEIRO EM PHP PARA FAZER A LIGAÇÃO À BD


if(isset($_POST['estado'])){
    switch($_POST['estado']){
        case 'todos':
            $sql="select * from alunos_teste_2 order by nome asc;";
            break;
        case 'ativos':
            $sql="select * from alunos_teste_2 where estado=1 order by nome asc;";
            break;
        case 'inativos':
            $sql="select * from alunos_teste_2 where estado=0 order by nome asc;";
            break;
        default:
            $sql="select * from alunos_teste_2 order by nome asc;";
            break;
    }
} else {
    $sql="select * from alunos_teste_2 order by nome asc;";
}
if(isset($_POST['nr_proc'])){
    if($_POST['nr_proc']!='' && $_POST['nr_proc']!=0){       
        if(is_numeric($_POST['nr_proc'])){
            $sql="select * from alunos_teste_2 where nr_proc=".$_POST['nr_proc'].";";
        } else {
            
        }
    }
}
if(isset($_POST['nome'])){
    if($_POST['nome']!='' && !is_numeric($_POST['nome'])){ 
        $sql="select * from alunos_teste_2 where nome like '%".$_POST['nome']."%';";
    }
}

if(isset($_POST['anoturma'])){
    if($_POST['anoturma']!=''){ 
        $sql="select * from alunos_teste_2 where ano_turma='".$_POST['anoturma']."' order by nome asc;";
    }
}


$res=mysqli_query($con,$sql);
$num=mysqli_num_rows($res);

$sql_turma="select distinct ano_turma from alunos_teste_2 order by ano_turma asc;";
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
    <div class="row" style="margin-top:150px;">
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-3" ><a href="index_exercicio.php" class="btn btn-outline-danger btn-lg">Voltar</a></div>
    </div>
    <div class="container" style="margin-top: 50px;">
         <div class="row">
             <div class="col-md-3">
                 <form method="post">
                     <input type="text" name="nr_proc" placeholder="nº proc." maxlength="5" class="form-control">
                 </form>                   
             </div>
             <div class="col-md-3">
                 <form method="post">
                     <input type="text" name="nome" placeholder="nome..." maxlength="" class="form-control">
                 </form>
             </div>
             <div class="col-md-3">
                 <form method="post">
                     <select name="anoturma" class="form-control" onchange="this.form.submit()">  
                         <option value="">TODAS</option>
                         <?php
                         while($reg_turma=mysqli_fetch_array($res_turma)){
                             if(isset($_POST['anoturma'])){
                                 if($_POST['anoturma']==$reg_turma['ano_turma']){
                                     ?>
                                      <option value="<?php echo $reg_turma['ano_turma']; ?>" selected><?php echo $reg_turma['ano_turma']; ?></option>
                                    <?php
                                 } else {
                                     ?>
                                      <option value="<?php echo $reg_turma['ano_turma']; ?>"><?php echo $reg_turma['ano_turma']; ?></option>
                                    <?php
                                 }
                             } else {
                                 ?>
                                 <option value="<?php echo $reg_turma['ano_turma']; ?>"><?php echo $reg_turma['ano_turma']; ?></option>
                                 <?php
                             }
                         }
                         ?>
                     </select>
                 </form>
             </div>
             <div class="col-md-3">
                 <form method="post">
                     <select name="estado" class="form-control" onchange="this.form.submit()">
                         <?php
                         if(isset($_POST['estado'])){
                             if($_POST['estado']=='todos'){
                                 echo '<option value="todos" selected>TODOS</option>';
                             } else {
                                 echo '<option value="todos">TODOS</option>';
                             }
                             if($_POST['estado']=='ativos'){
                                 echo '<option value="ativos" selected>ATIVOS</option>';
                             } else {
                                 echo '<option value="ativos">ATIVOS</option>';
                             }
                             if($_POST['estado']=='inativos'){
                                 echo '<option value="inativos" selected>INATIVOS</option>';
                             } else {
                                 echo '<option value="inativos">INATIVOS</option>';
                             }
                         } else {
                             ?>
                             <option value="todos">TODOS</option>
                             <option value="ativos">ATIVOS</option>
                             <option value="inativos">INATIVOS</option>
                            <?php
                         }
                         ?>
                     </select>
                 </form>
             </div>
         </div>
         <br>
         <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">Nº PROC.(<?php echo $num ?>)</th>
                      <th scope="col">NOME</th>
                      <th scope="col">TURMA</th>
                      <th scope="col">ESTADO</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php
                      if($num>0){
                          while($reg=mysqli_fetch_array($res)){
                              if($reg['estado']==1){
                                  $estado='<span class="badge badge-success">Ativo</span>';
                              } else {
                                  $estado='<span class="badge badge-danger">Inativo</span>';
                              }
                              ?>
                                <tr>
                                  <td><?php echo $reg['nr_proc']; ?></td>
                                  <td><?php echo $reg['nome']; ?></td>
                                  <td><?php echo $reg['ano_turma']; ?>º</td>
                                  <td><?php echo $estado; ?></td>
                                </tr>
                              <?php
                          }
                      } else {
                          ?>
                          <tr>
                                <td colspan="4">
                                    <div class="alert alert-danger" role="alert" align="center">SEM REGISTOS PARA MOSTRAR <br> <a href="listar.php" class="btn btn-outline-dark">OK</a></div> 
                                </td>
                          </tr>
                          <?php  
                      }
                      ?>
                  </tbody>
            </table>
         
    </div>
    <!-- JavaScript (Opcional) -->
    <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>

