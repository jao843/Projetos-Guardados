<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');


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
            background-color: #1e1e1e;
            /* Fundo escuro para modernizar */
            color: #ffffff;
            /* Texto branco */
        }

        .terms-container {
            background-color: #2c2c2c;
            /* Fundo levemente mais claro para contraste */
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
    if (isset($aviso)) {
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
    if (isset($erros)) {
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
                <a href="profile.php" class="btn btn-danger btn-lg">Voltar</a>
                <h2>Spotify Limitado no Melodychat</h2>
                <p>
                    Olá! Queremos explicar como funciona a ligação do Melodychat com o Spotify, para não haver dúvidas.
                </p>
                <ul style="text-align: left;">
                    <li>
                        <b>API do Spotify com acesso limitado:</b> O nosso site usa a API do Spotify, mas com limitações. Não temos permissão para controlar a tua conta Spotify, tocar músicas diretamente ou aceder a todas as funcionalidades. Só podemos ver informações públicas, como playlists ou músicas que já ouviste, se nos autorizares.
                    </li>
                    <li>
                        <b>O que podemos fazer:</b> Com a tua autorização, podemos guardar um código (token) do Spotify para mostrar sugestões de músicas baseadas no que ouves ou para criar playlists personalizadas. Tudo depende do que permitires.
                    </li>
                    <li>
                        <b>Segurança garantida:</b> Qualquer informação que o Spotify nos deixe usar, como o token de ligação, é guardada de forma segura e só serve para melhorar a tua experiência no Melodychat.
                    </li>
                    <li>
                        <b>Sem autorização, sem integração:</b> Se não quiseres conectar a tua conta Spotify, não há problema! Mas algumas funcionalidades, como Spotify Player, não estarão disponíveis.
                    </li>
                </ul>
                <p>
                    Contacta o nosso email para, se tivermos vaga, adicionarmos a funcionalidade do spotify.

                    Lembrando que deve incluir o nome e o email do Spotify.
                </p>

            </div>
            <a href="contact.php">Conctacte nos</a>
        </div>
        <div class="col-md-4">&nbsp;</div>
    </div>
<br><br>


    <!-- JavaScript (Opcional) -->
    <!-- jQuery primeiro, depois Popper.js, depois Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>

</html>