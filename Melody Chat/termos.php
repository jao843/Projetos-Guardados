
<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

if(isset($_POST['aceitartermos'])){
    
    if(isset($_SESSION['email'])){
        
        $sql="SELECT * FROM direitos WHERE email = '".$_SESSION['email']."'";
        $confirm = mysqli_query($con,$sql);

        if($verificacao = mysqli_num_rows($confirm) == 0){
            $direitos = "INSERT INTO direitos (email,termos) values ( '".$_SESSION['email']."', TRUE) ";
            $query = mysqli_query($con,$direitos);
            $aviso="Termos assinados no email de ".$_SESSION['email']."";
            header('location:melodychat.php');
            exit();
        }else{
            $aviso="Termos já forma assinados";
        }
    }else{
        $erros ="Esta funcionalidade eh so para contas que já estavam criadas mas n tinham os termos assinados";
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
    <link rel="icon" href="transparente.png" type="image/png">
    <title>Termos - Melodychat</title>

    <style>
    
    body {
    background-color: #1e1e1e; /* Fundo escuro para modernizar */
    color: #ffffff; /* Texto branco */
}

.terms-container {
            background-color: #2c2c2c; /* Fundo levemente mais claro para contraste */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            text-align: center;
        }
</style>

</head>
<body>

<?php
if(isset($aviso)){
    ?>
<div class="row" align="center" style="margin-top: 5px;">
            <div class="col-md-12" align="center">                  
                    <div class="alert alert-success" role="alert">
                        <?php echo $aviso; ?>
                    </div>                       
            </div>
        </div>


<?php
}

?>


<?php
if(isset($erros)){
    ?>
<div class="row" align="center" style="margin-top: 5px;">
            <div class="col-md-12" align="center">                  
                    <div class="alert alert-danger" role="alert">
                        <?php echo $erros; ?>
                    </div>                       
            </div>
        </div>


<?php
}

?>



<div class="row">

    <div class="col-md-4">&nbsp;</div>
    
    <div class="col-md-4" align="center">
    <div class="terms-container">
    <a href="signup.php" class="btn btn-danger btn-lg" >Voltar</a>
            <h2>Termos de Uso e Política de Privacidade</h2>
            <p>
                Bem-vindo! Para garantir o uso seguro e transparente de nossa plataforma, apresentamos a seguir os termos de uso e 
                nossa política de privacidade. É essencial que você leia atentamente antes de continuar.
            </p>
            <p>
                Ao clicar em "Aceito os Termos e Quero Prosseguir", você concorda com as seguintes condições:
            </p>
            <ul style="text-align: left;">
                <li>
                    <b>Armazenamento de Dados:</b> Autorizo o armazenamento de todas as informações fornecidas por mim, como 
                    nome, e-mail, e outros dados relevantes que sejam necessários para a utilização dos serviços da plataforma.
                </li>
                <li>
                    <b>Integração com Spotify:</b> Aceito que a plataforma realize a integração com o Spotify. Isso inclui a coleta e 
                    armazenamento de um token de autenticação gerado pelo Spotify, que será utilizado exclusivamente para oferecer 
                    funcionalidades como playlists personalizadas, acesso ao histórico de músicas e outras ferramentas relacionadas.
                </li>
                <li>
                    <b>Finalidade dos Dados:</b> Concordo que as informações coletadas sejam utilizadas para melhorar a experiência 
                    do usuário, desenvolver novas funcionalidades e oferecer um serviço mais personalizado.
                </li>
                <li>
                    <b>Segurança e Confidencialidade:</b> Garantimos que todas as informações coletadas serão tratadas de maneira 
                    segura e confidencial, respeitando todas as normas aplicáveis de proteção de dados.
                </li>
            </ul>
            <p>
                Se você optar por não aceitar estes termos, não será possível prosseguir com o uso da integração Spotify ou 
                aproveitar todas as funcionalidades que a nossa plataforma oferece.
            </p>

            <?php

                if(isset($_SESSION['email'])){

                    ?>
            <form method="post">
                
                <button class="btn btn-primary mt-3" name="aceitartermos">
                    Aceito os Termos e Quero Prosseguir
                </button>
            </form>
            <?php
        }
        ?>
        </div>

    </div>
    <div class="col-md-4">&nbsp;</div>
</div>
    
    

          <!-- JavaScript (Opcional) -->
          <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
          <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
          <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        </body>
      </html>