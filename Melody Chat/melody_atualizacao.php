<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

if(!isset($_SESSION['email'])){

    header('location:melodychat.php');
    exit();

}else{
    if(isset($_SESSION['aux'])){
            
              
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
            
                <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="style_waves.css">
                <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
            
                <title>Melody Chat</title>
            
                <style>
            body {
                margin: 0;
                padding: 0;
                height: 100vh;
                overflow: hidden; /* Impede o scroll */
                font-family: 'Arial', sans-serif; /* Fonte opcional */
            }
            
                /* Navbar fixo no topo */
                .navbar-top {
                    width: 100%;
                    height: 70px;
                    background-color: #181818; /* Cor da barra */
                    display: flex;
                    align-items: center;
                    padding: 0 20px;
                    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
                    position: fixed;
                    top: 0;
                    left: 0;
                    z-index: 1000;
                }
            
                .navbar-top .logo {
                    width: 50px;
                    height: 50px;
                    border-radius: 8px;
                    margin-right: 20px;
                }
            
                .navbar-top form {
                    display: flex;
                    gap: 20px;
                    margin-left: auto;
                }
            
                .navbar-top input {
                    padding: 10px;
                    background-color: #181818;
                    font-size: 15px;
                    font-weight: bold;
                    border: none;
                    color: white;
                    cursor: pointer;
                }
            
                .navbar-top input:hover {
                    color: #a0a0a0; /* Cor mais clara ao passar o mouse */
                }
            
                /* Ajuste do conteúdo principal */
                #main {
                    margin-top: 90px; /* Para compensar a altura da navbar */
                    padding: 20px;
                    width: 100%;
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                }
            
                footer {
                    background-color: #1f1f1f;
                    color: #ffffff;
                    text-align: center;
                    padding: 10px;
                    position: fixed;
                    bottom: 0;
                    width: 100%;
                }
            
                /* Estilos para o menu suspenso */
                .dropdown {
                    position: relative;
                    display: inline-block;
                    margin-left: 20px;
                }
            
                .dropdown-content {
                    display: none;
                    position: absolute;
                    background-color: #181818;
                    min-width: 160px;
                    box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
                    z-index: 1;
                    border-radius: 8px;
                }
            
                .dropdown-content a {
                    color: white;
                    padding: 10px;
                    text-decoration: none;
                    display: block;
                    text-align: left;
                }
            
                .dropdown-content a:hover {
                    background-color: #3a3a3a;
                }
            
                /* Exibir dropdown ao passar o mouse no botão "Definições" */
                .dropdown:hover .dropdown-content {
                    display: block;
                }
            
            
                .navbar-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 20px;
                background-color: #181818;
                height: 70px;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
                z-index: 1000;
            }
            
            .left-section {
                display: flex;
                align-items: center;
            }
            
            .logo {
                width: 50px;
                height: 50px;
                margin-right: 10px;
                border-radius: 8px;
            }
            
            .navbar-options {
                display: flex;
                gap: 10px;
            }
            
            .navbar-options input {
                background-color: #181818;
                font-size: 15px;
                font-weight: bold;
                color: white;
                border: none;
                cursor: pointer;
                padding: 10px;
            }
            
            .navbar-options input:hover {
                color: #a0a0a0;
            }
            
            .dropdown {
                margin-left: auto;
            }
            
            .title {
                position: absolute;
                top: 50px; /* Ajuste a posição do título para ficar mais para cima */
                left: 50%;
                transform: translateX(-50%); /* Centraliza o título */
                font-size: 60px;
                font-weight: bold;
                color: white;
                text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5); /* Sombra para o texto */
                margin-top: 20px;
            }
            
            .background-img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover; /* Faz a imagem cobrir toda a tela */
                filter: blur(8px); /* Aplica o desfoque na imagem */
                z-index: -1; /* Coloca a imagem desfocada atrás do conteúdo */
            }
            
            .subtitle {
                position: absolute;
                top: 150px; /* Ajuste a posição do subtítulo */
                left: 50%;
                transform: translateX(-50%);
                font-size: 30px;
                font-weight: normal;
                color: #d3d3d3; /* Cor do texto */
                text-align: center;
                margin-top: 50px;
            }
            
            /* Estilo para o parágrafo com fundo cinza e bordas arredondadas */
            .text-box {
                background-color: #1f1f1f; /* Cor de fundo cinza */
                padding: 20px;
                border-radius: 10px; /* Borda arredondada */
                width: 80%; /* Largura do parágrafo */
                margin: 20px auto; /* Centraliza o parágrafo */
                font-size: 16px; /* Tamanho da fonte */
                color: white; /* Cor do texto */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Sombra leve */
            }
            
                </style>
            </head>
            <body>
            
                <!-- Barra de Navegação Fixa no Topo -->
                <div class="navbar-top">
                    <div class="left-section">
                        <img src="https://imageshack.com/i/pmmxUi4hp" alt="Logo" class="logo">
                        <form method="post" class="navbar-options">
                            <input type="submit" name="chats" value="Chat">
                            <input type="submit" name="friends" value="Comunidade">
                            <input type="submit" name="group" value="Servers">
                        </form>
                    </div>
                    <form method="post">
                        <input type="submit" name="profile" value="Profile">
                        <div class="dropdown">
                            <input type="button" name="def" value="Definições">
                            <div class="dropdown-content">
                                <a href="editar.php">Editar</a>
                                <a href="dsession_melodychat.php">Log out</a>
                            </div>
                        </div>
                    </form>
                </div>
            
                <div id="main">
                    <img src="https://imageshack.com/i/pobXL3Dvj" alt="Imagem de fundo" class="background-img">
                    <br><br><br>
                    <div class="title">
                        MelodyChat!
                    </div>
                    <div class="">
                        <h4 class="subtitle">EM ATUALIZAÇÃO</h4>

                    </div>
                    <br>
                    <p class="text-box">O Melody Chat</p>
                </div>
            
                <!-- Footer -->
                <footer>
                    &copy; 2024 Melody Chat. Todos os direitos reservados.
                </footer>
                <?php
    }else{
        header('location:melodychat.php');
        exit();
    }
}
                ?>
                
    <!-- JavaScript (Opcional) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>