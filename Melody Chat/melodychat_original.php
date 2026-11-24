<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

if(!isset($_SESSION['email'])){
  header('location:index_login.php');
  exit();
}else{

    if(isset($_POST['chats'])){
        header('location:chats.php');
        exit();
    }

    if(isset($_POST['friends'])){
        header('location:friends.php');
        exit();
    }

    if(isset($_POST['group'])){
        header('location:server.php');
        exit();
    }


  if(isset($_POST['ok'])){
    $_SESSION['ok']=1;
  }
  
  if(isset($_POST['logout'])){
    header('location:dsession_melodychat.php');
    exit();
  }

  if(isset($_POST['editar'])){
    header('location:editar.php');
    exit();
  }
  
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Meta tags Obrigatórias -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>Melody Chat</title>

    <style>
    
    body {
    font-family: "Arial", sans-serif;
    margin: 0;
    background-color: #121212; /* Fundo preto suave */
    color: #e0e0e0; /* Texto branco acinzentado */
}

#main {
    margin-left: 270px;
    padding: 20px;
    width: calc(100% - 270px);
    flex-direction: column; /* Mude para coluna para que os itens fiquem um embaixo do outro */
    gap: 20px; /* Espaço entre os itens */
    display: flex;
}

footer {
    background-color: #1f1f1f; /* Preto mais claro */
    color: #ffffff; /* Texto branco */
    text-align: center;
    padding: 10px;
    position: fixed;
    left: 270px;
    z-index: 999;
    bottom: 0;
    width: calc(100% - 270px);
}


.logo {
    width: 100px;
    height: 100px;
    display: block;
    margin-bottom: 20px;
    border-radius: 8px;
}

.bottom-box {
    background-color: #181818; /* Preto ainda mais escuro */
    color: white;
    padding: 15px;
    position: relative;
    bottom: 0;
    width: 270px;
    left: 0;
}

.bottom-box .editar, .bottom-box .logout {
    font-size: 15px;
    background-color: #333333; /* Cinza escuro */
    color: #ffffff;
    border: none;
    padding: 10px;
    margin-top: 10px;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s;
}

.bottom-box .logout:hover {
    background-color: #444444; /* Cinza mais escuro */
}

.notification-box {
    background-color: #2b2b2b; /* Cinza escuro */
    padding: 15px;
    border-radius: 5px;
    width: 200px;
    height: 100px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 14px;
}

.notification-box:hover {
    transform: scale(1.05);
}

.right-sidebar {
    height: 100vh;
    width: 350px;
    position: fixed;
    top: 0;
    right: 0;
    background-color: #1f1f1f; /* Fundo do sidebar direito */
    color: #ffffff;
    padding: 20px;
    box-shadow: -4px 0 10px rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    gap: 20px;
    overflow-y: auto;
}

.right-sidebar h4 {
    margin: 0;
    font-size: 20px;
    font-weight: bold;
    color: #ffffff;
    text-align: center;
    margin-bottom: 20px;
}

.update-box {
    background-color: #333333; /* Fundo do box de atualizações */
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    font-size: 13px;
    text-align: center;
    transition: transform 0.3s;
    width: 90%;
    max-width: 300px;
    margin: 0 auto;
}

.update-box:hover {
    background-color: #444444; /* Fundo do box em hover */
    transform: scale(1.05);
}

.notification-box {
    background-color: #2b2b2b; /* Fundo da caixa de notificação */
    color: #e0e0e0; /* Texto branco acinzentado */
    transition: background-color 0.3s;
}

.notification-box:hover {
    background-color: #3b3b3b; /* Fundo da caixa de notificação em hover */
}

.toggle-button {
    position: fixed;
    right: 20px; /* Distância da borda direita */
    bottom: 20px; /* Distância da borda inferior */
    padding: 10px 15px;
    background-color: #444; /* Cor de fundo */
    color: white; /* Cor do texto */
    border: none; /* Sem borda */
    border-radius: 5px; /* Bordas arredondadas */
    cursor: pointer; /* Muda o cursor para pointer */
    z-index: 1001; /* Coloca acima de outros elementos */
}

.toggle-button:hover {
    background-color: #555; /* Cor ao passar o mouse */
}



.left-column {
    flex: 1;
    padding: 20px;
    max-width: 250px;
}

.right-column {
    flex: 2;
    padding: 20px;
}

/* Atualizações para o conteúdo principal */
#main .content-container {
    display: flex;
    justify-content: space-between;
    padding-right: 370px; /* Espaço para a sidebar */
    transition: padding-right 0.3s; /* Transição suave quando a sidebar aparece */
}

#main .right-column {
    flex: 2;
    padding: 20px;
    z-index: 1; /* Certifica que ficará atrás da right-sidebar */
}

    </style>
</head>
<body>


    <?php
    if(!isset($_SESSION['ok'])){
    ?>

    <div class="row" style="margin-top: 20px;">
      <div class="col-md-12" align="center">
        <div class="alert alert-success" role="alert">
            SUCESSO
        </div>  
      </div>
      <div class="col-md-4" align="center">&nbsp;</div>
      <div class="col-md-4" align="center">
        <form method="post">
          <input type="submit" name="ok" class="btn btn-outline-danger btn-lg" value="OK">
        </form>
      </div>
      <div class="col-md-4" align="center">&nbsp;</div>
    </div>     
  </div>
<?php
    }
?>


<div id="main">

    <nav>
        <div class="logo">
            <img src="https://imageshack.com/i/pmmxUi4hp" alt="Logo Image">
        </div>
        <div class="hamburger">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
        <ul class="nav-links">
            <li><a href="#">Home</a></li>
            <li><a href="#">Solutions</a></li>
            <li><a href="#">Products</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><button class="login-button" href="#">Login</button></li>
            <li><button class="join-button" href="#">Join</button></li>
        </ul>
    </nav>

    <script src="nav.js"></script>


</div>

<button id="toggle-sidebar" class="toggle-button">Atualizações</button>

<div class="right-sidebar">
        <h4>Atualizações</h4>

        <?php
        $update=0;
        if($update!=0){?>
        
        <?php
        }else{?>

            <div class="update-box">
            <p>Sem Atualizações de momento!</p>
            </div>

        <?php
        }
        ?> 

        <!-- Adicione mais caixas de atualização conforme necessário -->
</div>

    <div class="bottom-box">
        Utilizador: 
        <?php 
            echo $_SESSION['util']; 
        ?>
        <br>
        <form method="post">
            <input type="submit" name="editar" class="editar" value="Editar">
            <input type="submit" name="logout" value="Log Out" class="logout">
        </form>
    </div>


<?php
  }
?>





<script>
        const toggleButton = document.getElementById('toggle-sidebar');
        const rightSidebar = document.querySelector('.right-sidebar');
        let sidebarVisible = true;

        toggleButton.addEventListener('click', () => {
            sidebarVisible = !sidebarVisible;
            rightSidebar.style.display = sidebarVisible ? 'flex' : 'none';
        });
</script>


<!-- Footer -->
<footer>
    &copy; 2024 Melody Chat. Todos os direitos reservados.
</footer>

    <!-- JavaScript (Opcional) -->
    <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>
