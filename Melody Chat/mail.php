<?php
session_start();
require("class.phpmailer.php");


$erro="";
if (isset($_POST['enviar'])) {
    $envia = 0;
    // VARIÁVEIS PARA COMPOSIÇÃO
    if(isset($_POST['mensagem'])){
        $mensagem = 'A mensagem é: <br><br>' . $_POST['mensagem'] . '<br>';
    

        // ENVIO DE E-MAIL ATRAVÉS DA CLASSE
        $mail_send = new PHPMailer();
        $mail_send->Host = "localhost";  // specify main and backup server
        if(!isset($_SESSION['email'])){
            $mail_send->From = "Guest";
        }else{ 
            $mail_send->From = $_SESSION['email'];
        }
        
        if(!isset($_SESSION['email'])){
            $mail_send->From = "Guest";
        }else{ 
            $mail_send->FromName = $_SESSION['util'];
        }
        
        $mail_send->AddAddress("aluno18404@damiaodegoes.pt");
        if(isset($_SESSION['email'])){
            $mail_send->AddReplyTo($_SESSION['email']);
        }else{
            $mail_send->AddReplyTo("aluno18404@damiaodegoes.pt");
        }

        $mail_send->IsHTML(true);  // set email format to HTML
        if($_POST['assunto'] !=''){
            $mail_send->Subject = $_POST['assunto'];
        }else{
            $mail_send->Subject = 'Vazio';
        }

        $mail_send->Body = utf8_decode($mensagem);

        
        if($mail_send-> Send()) { // ENVIA O MAIL
            $envia = 1;
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

    <title>Enviar Email - Melody Chat</title>

    <style>
        /* Estilos globais */
        body {
            background-color: #1e1e1e;
            color: #ffffff;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 500px;
            margin-top: 70px;
        }

        .logo-central {
            display: block;
            margin: 0 auto 20px;
            width: 110px;
            height: auto;
            opacity: 0.8;
        }

        /* Card de envio de email */
        .card {
            background-color: #2c2c2c;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
        }

        h4 {
            color: #ffffff;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-control {
            background-color: #333333;
            border: 1px solid #a9a9a9;
            color: #ffffff;
            margin-bottom: 15px;
        }

        .form-control:focus {
            border-color: #4682b4;
            box-shadow: none;
        }

        .btn-outline-success, .btn-outline-secondary {
            width: 100%;
            font-weight: bold;
            margin-top: 10px;
        }

        .btn-outline-success:hover {
            background-color: #32cd32;
            color: white;
        }

        .btn-outline-secondary:hover {
            background-color: #a9a9a9;
            color: white;
        }

        .alert-success, .alert-danger {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://imageshack.com/i/pmmxUi4hp" class="logo-central" alt="Logo Melody Chat">
        
        <!-- Card de Envio de Email -->
        <div class="card">
            <div class="card-body">
                <h4 class="font-weight-bold">Enviar Email</h4>
                <?php if (isset($envia)){ ?>
                    <?php if ($envia == 1){ ?>
                        <div class="alert alert-success">EMAIL ENVIADO COM SUCESSO</div>
                    <?php }else{ ?>
                        
                        <div class="alert alert-danger">OCORREU UM PROBLEMA NO ENVIO DO EMAIL. TENTE NOVAMENTE.</div>
                    <?php }?>
                <?php } ?>
                    <div class="form-group">
                        <label for="assunto">Assunto:</label>
                        <form method="post">
                        <input type="text" name="assunto" id="assunto" class="form-control" placeholder="Assunto do email"></form>
                    </div>

                    <div class="form-group">
                        <label for="mensagem">Mensagem:</label>
                        <textarea name="mensagem" id="mensagem" rows="6" class="form-control" placeholder="Digite sua mensagem..."></textarea>
                    </div>

                    <a href="melodychat.php" class="btn btn-outline-secondary">Voltar</a>
                    <form method="post">
                    <input type="submit" class="btn btn-outline-success" name="enviar" value="Enviar"></form>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript (Opcional) -->
    <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
