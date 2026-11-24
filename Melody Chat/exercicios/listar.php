<?php
//PROGRAMA PARA LISTAR UMA TABELA DE UMA BASE DE DADOS
session_start();
require('ligacao.php');

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
     <div class="container" style="margin-top: 50px;"> 

          <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">Nº PROC.</th>
                      <th scope="col">NOME</th>
                      <th scope="col">TURMA</th>
                      <th scope="col">ESTADO</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php
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

