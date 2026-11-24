<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

if (!isset($_SESSION['email'])) {

    header('location:melodychat.php');
    exit();
}

$est = "SELECT estado FROM manutencao WHERE id = 1;";
$estado = mysqli_query($con, $est);
$row = mysqli_fetch_assoc($estado);

if ($row['estado'] == FALSE and isset($_SESSION['email'])) {

    $sql = "SELECT * from melodychat where email = '" . $_SESSION['email'] . "'";
    $sql_cargo = mysqli_query($con, $sql);

    $cargo_email = mysqli_fetch_assoc($sql_cargo);

    if ($cargo_email['cargo'] == FALSE) {

        header('location:dsession_melodychat.php');
        exit();
    }
}

date_default_timezone_set('Europe/Lisbon'); // Ajusta para o fuso horário desejado
$time_now = date("Y-m-d H:i:s");

$login_sql = "UPDATE melodychat set login = 1,login_time = '$time_now' where email = '" . $_SESSION['email'] . "'";
$login_query = mysqli_query($con, $login_sql);



// Obtém a lista de grupos que o usuário participa
$groupsQuery = "SELECT * FROM servers WHERE nome = '" . $_SESSION['email'] . "'";
$groupsResult = mysqli_query($con, $groupsQuery);


if (isset($_POST['create_group'])) {
    $groupName = mysqli_real_escape_string($con, $_POST['group_name']);

    if (!empty($groupName)) {
        // Insere o novo grupo na tabela de grupos
        $createGroupQuery = "INSERT INTO grupo (nome_grupo, proprietario) VALUES ('$groupName', '" . $_SESSION['email'] . "')";
        if (mysqli_query($con, $createGroupQuery)) {

            // Busca o último ID inserido ordenando por id_grupo descendente e pegando o primeiro
            $sql = "SELECT id_grupo FROM grupo ORDER BY id_grupo DESC LIMIT 1";
            $resultId = mysqli_query($con, $sql);
            $groupId = mysqli_fetch_assoc($resultId);

            // Certifique-se de acessar o id_grupo corretamente
            $groupIdValue = $groupId['id_grupo'];

            // Adiciona o criador ao grupo na tabela servers
            $addUserToGroupQuery = "INSERT INTO servers (id, nome_grupo, nome) VALUES ('" . $groupIdValue . "', '" . $groupName . "', '" . $_SESSION['email'] . "')";
            if (mysqli_query($con, $addUserToGroupQuery)) {
                // Redireciona para evitar reenvio do formulário
                header("Location: " . $_SERVER['PHP_SELF']);
                exit(); // Garante que o script pare aqui
            } else {
                echo "Erro ao adicionar o usuário ao grupo: " . mysqli_error($con);
            }
        } else {
            echo "Erro ao criar o grupo: " . mysqli_error($con);
        }
    } else {
        echo "O nome do grupo não pode estar vazio.";
    }
}

if (isset($_POST['voltar'])) {
    $_SESSION['group_id'] = null;

?>

    <script>
        function refreshPage() {
            location.reload(); // Esta função recarrega a página atual
        }
    </script>

<?php
}


if (isset($_POST['send-adicionar'])) {

    $sql = "SELECT nome_grupo from servers where id = " . $_SESSION['group_id'] . "";
    $verificar_nome_grupo = mysqli_query($con, $sql);
    $nome_grupo = mysqli_fetch_assoc($verificar_nome_grupo);

    $sql = "INSERT INTO servers (id, nome_grupo, nome) VALUES (" . $_SESSION['group_id'] . ", '" . $nome_grupo['nome_grupo'] . "' , '" . $_POST['email_amigo'] . "')";
    $enviar_sql = mysqli_query($con, $sql);

    $aviso = "Membro adicionado!";
}

if (isset($_POST['remover_membro'])) {

    $email_membro = $_POST['email_amigo_membro'];

    // Cria a consulta para remover o membro da tabela servers
    $sql = "DELETE FROM servers WHERE nome = '$email_membro' AND id = '" . $_SESSION['group_id'] . "'";

    // Executa a consulta
    if (mysqli_query($con, $sql)) {
        $aviso = "Membro removido com sucesso!";
    } else {
        $aviso = "Erro ao remover membro: " . mysqli_error($con);
    }
}



if (isset($confirmacao)) {

    if (mysqli_num_rows($confirmacao) > 0) {
        $bandeira_confirmacao = true;
    } else {
        $bandeira_confirmacao = false;
    }
}




if (isset($_POST['delete_message'])) {

    $sql = "DELETE FROM group_messages where id_message = '" . $_POST['id_message'] . "' ";
    $stmt = mysqli_prepare($con, $sql);

    if ($stmt) {
        // Liga o parâmetro de forma segura
        mysqli_stmt_bind_param($stmt, "i", $_POST['id_message']);
        mysqli_stmt_execute($stmt);

        // Verifica se a mensagem foi realmente deletada
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "<script>alert('Mensagem apagada com sucesso.'); window.location.href='server.php';</script>";
        } else {
            echo "<script>alert('Erro ao apagar mensagem ou ID não encontrado.'); window.location.href='server.php';</script>";
        }

        // Fecha o statement
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Erro na preparação da consulta.'); window.location.href='server.php';</script>";
    }

    // Fecha a conexão
    mysqli_close($con);
}




if (isset($_POST['confirmar_nome'])) {


    $novo_nome = $_POST['novo_nome_grupo'];


    $sql = "UPDATE grupo SET nome_grupo = '$novo_nome' WHERE id_grupo = " . $_SESSION['group_id'] . ""; // Ajuste conforme sua tabela
    $stmt = mysqli_query($con, $sql);

    if ($stmt == true) {
        echo "<script>alert('Nome do grupo alterado com sucesso!'); window.location.href='server.php';</script>";
        $_SESSION['acesso_nome_change'] == false;
    } else {
        echo "<script>alert('Erro ao mudar o nome do grupo!');</script>";
    }
}

if (isset($_POST['fechar_popup'])) {
    $acesso_nome_change == false;
    echo "<script>window.location.href='server.php';</script>";
}

?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://cdn.lineicons.com/5.0/lineicons.css" rel="stylesheet" />
    <link rel="icon" href="transparente.png" type="image/png">
    <title>Grupos - Melodychat</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Roboto', sans-serif;
            background-color: #1f1f1f;
            color: white;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #111;
        }

        .topbar .tools {
            display: flex;
            gap: 10px;
        }

        .tool-btn {
            background: #222;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        .friends-container {
            padding: 20px;
        }

        .group-card {
            background: #333;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .group-card button {
            background: #444;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .chat-container {
            padding: 20px;
        }

        .messages {
            background: #222;
            padding: 20px;
            border-radius: 5px;
            height: 400px;
            overflow-y: scroll;
        }

        .own-message {
            text-align: right;
            margin: 10px 0;
            color: #00ff00;
        }

        .group-message {
            text-align: left;
            margin: 10px 0;
            color: #fff;
        }

        .input-area {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            align-items: center;
            /* Centraliza verticalmente todos os itens no contêiner */
            justify-content: flex-end;
            /* Mantém os itens alinhados à direita, se necessário */
        }

        #send-button {
            padding: 10px 20px;
            /* Mantém o padding original para largura, mas ajusta altura */
            height: 50px;
            /* Altura fixa do botão */
            border: none;
            background: #444;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
            display: flex;
            /* Usa flex para centralizar o texto */
            align-items: center;
            /* Centraliza o texto verticalmente dentro do botão */
            justify-content: center;
            /* Centraliza o texto horizontalmente dentro do botão */
            line-height: normal;
            /* Remove qualquer altura de linha fixa que possa conflitar */
            font-size: 16px;
            /* Garante que o texto seja legível (ajuste se necessário) */
        }

        #message-input {
            flex-grow: 1;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #444;
            background: #111;
            color: #fff;
        }

        .group-card {
            background-color: #333333;
            /* Fundo mais escuro */
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 100%;
            max-width: 300px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            /* Sombra mais forte para contrastar */
        }

        .group-name {
            font-size: 1.2em;
            font-weight: bold;
            color: white;
            /* Texto em branco para contraste */
            margin-bottom: 5px;
        }

        .group-owner {
            font-size: 1em;
            color: #ccc;
            /* Cor mais clara para o proprietário */
            margin-bottom: 10px;
            width: 20%;
            text-align: left;
        }

        form {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        #groupForm {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-size: 1em;
            color: #ccc;
            margin-bottom: 5px;
            display: block;
            font-weight: 600;
        }

        .form-group input {
            padding: 10px;
            border: 1px solid #555;
            border-radius: 5px;
            background-color: #444;
            color: white;
            font-size: 1em;
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #007bff;
            outline: none;
            background-color: #333;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
            /* Botões lado a lado */
            margin-top: 15px;
        }

        .form-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            width: 48%;
            /* Botões com largura de 48% para ficarem lado a lado */
            transition: background-color 0.3s ease;
        }

        /* Botão Criar Grupo (verde mais escuro) */
        .submit-btn {
            background-color: #218838;
            /* Verde escuro */
            color: white;
        }

        .submit-btn:hover {
            background-color: #1e7e34;
            /* Tom mais escuro ao passar o mouse */
        }

        /* Botão Fechar (vermelho mais escuro) */
        .close-btn {
            background-color: #c82333;
            /* Vermelho escuro */
            color: white;
        }

        .close-btn:hover {
            background-color: #bd2130;
            /* Tom mais escuro ao passar o mouse */
        }

        /* Container do modal */
        .form-container {
            background: #1a1a1a;
            /* Fundo escuro */
            color: #fff;
            /* Texto branco */
            padding: 30px 20px;
            /* Espaçamento interno */
            border-radius: 10px;
            /* Cantos arredondados */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.6);
            /* Sombra para destacar o modal */
            width: 350px;
            text-align: center;
            position: relative;
            font-family: Arial, sans-serif;
        }

        /* Título do modal */
        .form-container h2 {
            font-size: 20px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* Texto descritivo (se necessário) */
        .form-container p {
            font-size: 14px;
            margin-bottom: 15px;
            color: #ccc;
            line-height: 1.5;
        }

        /* Botão de fechar */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: transparent;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            font-weight: bold;
        }

        /* Campo de senha */
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 15px 0;
            border: 1px solid #555;
            /* Bordas escuras */
            border-radius: 5px;
            background: #2a2a2a;
            /* Fundo escuro */
            color: white;
            /* Texto branco */
            font-size: 16px;
        }



        /* Responsividade (para telas pequenas) */
        @media (max-width: 400px) {
            .form-container {
                width: 90%;
                padding: 20px;
            }

            .form-container h2 {
                font-size: 18px;
            }

            button[type="submit"] {
                font-size: 14px;
            }
        }

        /* Estilo do contêiner das mensagens */
        #messages {
            background: #222;
            padding: 20px;
            border-radius: 5px;
            height: 300px;
            /* Reduzido de 500px para 300px */
            overflow-y: scroll;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* Estilo base para mensagens */

        .message {
            position: relative;
            padding: 8px;
            /* Igual ao chats.php */
            border-radius: 10px;
            /* Igual ao chats.php */
            max-width: 70%;
            /* Ajustado para 70%, como no chats.php */
            min-width: 150px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            /* Mantido o espaçamento para os meatballs */
        }

        .message.own {
            align-self: flex-end;
            background-color: #007bff;
            /* Mantido consistente com chats.php */
            color: #fff;
            text-align: right;
            margin-left: auto;
            /* Igual ao chats.php */
        }

        .message.member {
            align-self: flex-start;
            background-color: #333;
            /* Ajustado para ser mais parecido com chats.php */
            color: #fff;
            text-align: left;
            margin-right: auto;
            /* Igual ao chats.php */
        }

        .message-content {
            flex: 1;
        }

        .message .text {
            margin: 0;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            /* Igual ao chats.php */
        }

        .time {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }

        .photo {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #ccc;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }

        .author {
            font-weight: bold;
            font-size: 13px;
            color: #fff;
            margin-left: 10px;
        }

        .menu_dropdown_mensagens {
            width: 28px;
            height: 28px;
            background: transparent;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu_dropdown_mensagens i {
            font-size: 18px;
            color: white;
        }

        .menu_dropdown_mensagens:hover i {
            color: #000;
        }

        .dropdown_menu {
            position: absolute;
            top: 5px;
            left: -120px;
            background: white;
            border: 1px solid #ccc;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            display: none;
            z-index: 10;
            width: 110px;
        }

        .delete_button {
            background: red;
            color: white;
            border: none;
            padding: 8px 12px;
            width: 100%;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
        }

        .delete_button:hover {
            background: darkred;
        }

        .voltar {
            display: inline-flex;
            /* Permite adicionar ícones ou alinhamentos */
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            /* Espaçamento interno */
            background-color: #dc3545;
            /* Vermelho padrão */
            color: #fff;
            /* Texto branco */
            font-size: 16px;
            /* Tamanho do texto */
            font-weight: bold;
            /* Texto em negrito */
            border: none;
            /* Remove a borda padrão */
            border-radius: 8px;
            /* Cantos arredondados */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Sombra leve */
            cursor: pointer;
            /* Cursor de mão */
            transition: background-color 0.3s, transform 0.2s;
            /* Animação para hover */
        }

        .voltar:hover {
            background-color: #bb2d3b;
            /* Vermelho mais escuro ao passar o mouse */
            transform: translateY(-2px);
            /* Efeito de elevação */
        }

        .voltar:active {
            background-color: #a71d2a;
            /* Vermelho ainda mais escuro ao clicar */
            transform: translateY(0);
            /* Remove elevação */
        }


        /* Barra superior com as informações do grupo */
        .top-bar {
            display: flex;
            flex-direction: column;
            /* Centraliza as informações em uma coluna */
            align-items: center;
            /* Alinha tudo ao centro */
            background-color: #222;
            /* Fundo escuro */
            color: #fff;
            /* Cor do texto em branco */
            padding: 15px 30px;
            /* Ajuste no espaçamento */
            border-radius: 10px;
            /* Bordas arredondadas */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Sombra suave */
            position: relative;
            /* Necessário para posicionar o botão à direita */
        }

        /* As informações (Grupo e Proprietário) */
        .top-bar h2,
        .top-bar h3 {
            margin: 5px 0;
            /* Ajuste no espaçamento */
            font-size: 16px;
            /* Tamanho de fonte maior */
            font-weight: normal;
            /* Fonte sem negrito */
            text-align: center;
            /* Centraliza o texto */
        }

        /* Formulário para o botão de share */
        .share {
            position: absolute;
            right: 90px;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            max-width: 120px;
            margin-top: 10px;
        }



        /* Estilo da div que será exibida */
        .share-popup {
            display: none;
            /* Inicialmente invisível */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Fundo transparente */
            color: white;
            text-align: center;
            padding-top: 20%;
            /* Centraliza o conteúdo */
            z-index: 9999;
            /* Garante que fique acima de outros elementos */
            font-size: 24px;
        }

        .share-popup button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .share-popup button:hover {
            background-color: #218838;
        }

        .adicionar {
            background-color: #444444;
            /* Fundo cinzento escuro */
            width: 300px;
            /* Largura maior */
            height: 600px;
            /* Altura maior na vertical */
            border-radius: 8px;
            /* Cantos arredondados */
            padding: 16px;
            /* Espaço interno */
            color: #ffffff;
            /* Texto branco para contraste */

            /* Centralizar no site */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);

            overflow-y: auto;
            /* Ativa o scroll vertical quando necessário */
            padding-right: 10px;
            /* Adiciona um pequeno espaço à direita para a barra de rolagem */
        }


        .amigos-container {
            position: fixed;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            width: 300px;
            /* Ajuste conforme necessário */
            height: 65vh;
            /* Define a altura da área de rolagem */
            overflow-y: auto;
            /* Adiciona a rolagem vertical */
            padding: 10px;
            border-radius: 8px;
        }

        .gestao-grupos {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            height: 80vh;
            background: #1e1e1e;
            /* Cor sólida em vez de transparente */
            padding: 20px;
            border-radius: 10px;
            z-index: 1000;
            display: flex;
            overflow: hidden;
        }

        .ferramentas {
            position: fixed;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            width: 15%;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #282828;
            /* Cor sólida em vez de transparente */
            padding: 10px;
            border-right: 1px solid #555;
            height: 80vh;
            /* Para a altura ficar fixa */
        }

        .section-adicionar {
            position: fixed;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            width: 25%;
            padding: 10px;
            background: #3c3c3c;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 80vh;
            max-height: 80vh;
            overflow-y: auto;
        }

        .amigos-container {
            width: 100%;
            max-height: 100%;
            overflow-y: auto;
            padding: 5px;
            border-radius: 0;
            position: relative;
            transform: none;
            top: auto;
            right: auto;
        }

        /* Manter os estilos dos botões exatamente como estão, sem alterar cores */

        .btn-outline-warning {
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            white-space: nowrap;
        }


        /* Novo estilo para o botão, encostado à direita */
        .btn-partilhar {
            background-color: #4CAF50;
            color: white;
            padding: 6px 12px;
            /* Mantém padding reduzido */
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: normal;
            cursor: pointer;
            margin-left: auto;
            /* Empurra o botão para a direita */
            white-space: nowrap;
            /* Previne quebra de linha */
        }


        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 999;
            display: none;
        }

        .blur-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* Fundo escuro semi-transparente */
            backdrop-filter: blur(5px);
            /* Aplica o desfoque */
            -webkit-backdrop-filter: blur(5px);
            /* Compatibilidade com navegadores WebKit */
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }


        .voltar_popup {
            display: inline-flex;
            /* Permite adicionar ícones ou alinhamentos */
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            /* Espaçamento interno */
            background-color: #dc3545;
            /* Vermelho padrão */
            color: #fff;
            /* Texto branco */
            font-size: 16px;
            /* Tamanho do texto */
            font-weight: bold;
            /* Texto em negrito */
            border: none;
            /* Remove a borda padrão */
            border-radius: 8px;
            /* Cantos arredondados */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Sombra leve */
            cursor: pointer;
            /* Cursor de mão */
            transition: background-color 0.3s, transform 0.2s;
            /* Animação para hover */
            z-index: 3000;
        }

        .voltar_popup:hover {
            background-color: #bb2d3b;
            /* Vermelho mais escuro ao passar o mouse */
            transform: translateY(-2px);
            /* Efeito de elevação */
        }

        .voltar_popup:active {
            background-color: #a71d2a;
            /* Vermelho ainda mais escuro ao clicar */
            transform: translateY(0);
            /* Remove elevação */
        }



        .mudar_nome_grupo {
            background: black;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        /* Input */
        .mudar_nome_grupo input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Botões */
        .mudar_nome_grupo .btn {
            padding: 8px 12px;
            margin: 5px;
            cursor: pointer;
        }

        #create-group-form {
            background: #2a2a2a;
            /* Fundo escuro */
            border-radius: 10px;
            /* Bordas arredondadas */
            padding: 20px;
            /* Espaço interno */
            border: 2px solid #444;
            /* Borda cinza escuro */
            max-width: 400px;
            /* Largura máxima */
            margin: 0 auto;
            /* Centraliza o formulário */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            /* Sombra suave para profundidade */
            color: #fff;
            /* Texto branco para contraste */
        }

        #create-group-form .form-group {
            margin-bottom: 15px;
            /* Espaço entre os elementos do formulário */
        }

        #create-group-form label {
            font-size: 1em;
            color: #ccc;
            /* Cor clara para os labels */
            margin-bottom: 5px;
            display: block;
            font-weight: 600;
        }

        #create-group-form input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #555;
            /* Borda cinza escuro */
            border-radius: 5px;
            /* Bordas ligeiramente arredondadas no input */
            background-color: #333;
            /* Fundo mais escuro para o input */
            color: #fff;
            /* Texto branco no input */
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        #create-group-form input[type="text"]:focus {
            border-color: #007bff;
            /* Borda azul ao focar */
            outline: none;
        }

        #create-group-form .form-buttons {
            display: flex;
            justify-content: space-between;
            /* Botões lado a lado */
            margin-top: 15px;
        }

        #create-group-form button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            /* Bordas arredondadas nos botões */
            cursor: pointer;
            font-size: 1em;
            width: 48%;
            /* Botões com largura de 48% para ficarem lado a lado */
            transition: background-color 0.3s ease;
        }

        #create-group-form .btn-success {
            background-color: #28a745;
            /* Verde para o botão de criar */
            color: #fff;
        }

        #create-group-form .btn-success:hover {
            background-color: #218838;
            /* Verde mais escuro ao passar o mouse */
        }

        #create-group-form .btn-danger {
            background-color: #dc3545;
            /* Vermelho para o botão de fechar */
            color: #fff;
        }

        #create-group-form .btn-danger:hover {
            background-color: #c82333;
            /* Vermelho mais escuro ao passar o mouse */
        }


        .group-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            /* Cria 4 colunas de largura igual */
            gap: 15px;
            /* Espaço entre os cards */
            margin-top: 20px;
        }

        .group-card {
            background: #333;
            padding: 15px;
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        .group-name,
        .group-owner {
            text-align: center;
            width: 100%;
        }

        .group-card button {
            background: #444;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            /* Garante que o botão ocupe toda a largura do card */
        }

        /* Responsividade para telas menores */
        @media (max-width: 1200px) {
            .group-grid {
                grid-template-columns: repeat(3, 1fr);
                /* 3 colunas em telas médias */
            }
        }

        @media (max-width: 900px) {
            .group-grid {
                grid-template-columns: repeat(2, 1fr);
                /* 2 colunas em telas pequenas */
            }
        }

        @media (max-width: 600px) {
            .group-grid {
                grid-template-columns: 1fr;
                /* 1 coluna em telas muito pequenas */
            }
        }

        .ferramentas .btn-warning {
            padding: 4px 8px;
            /* Mantém o tamanho reduzido */
            font-size: 12px;
            /* Mantido para legibilidade */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            white-space: nowrap;
            color: black;
            /* Mantido como no seu estilo original */
            background-color: #ffc107;
            /* Restaurado o amarelo padrão do Bootstrap .btn-warning */
        }

        .membros {
            flex: 1;
            overflow-y: auto;
            /* Mantém o scroll vertical */
            padding: 15px;
            /* Cor de fundo mantida */
            margin-left: 15%;
            /* Mantido como estava */
            margin-right: 25%;
            /* Mantido como estava */
            display: flex;
            /* Usa flex para centralizar */
            flex-direction: column;
            /* Organiza os itens em coluna */
            align-items: center;
            /* Centraliza horizontalmente */
            justify-content: flex-start;
            /* Alinha no topo, mas permite scroll */
            width: 70%;
            /* Garante que ocupe toda a largura disponível */
            max-height: 100%;
            /* Limita a altura máxima */
        }

        .friend-item {
            display: flex;
            /* Centraliza verticalmente */
            justify-content: space-between;
            /* Distribuí os elementos, com botões à direita */
            margin-bottom: 15px;
            /* Reduzido o espaço entre itens */
            padding: 10px;
            /* Reduzido o padding interno */
            background: #444;
            /* Fundo mais claro para contraste */
            border-radius: 8px;
            /* Bordas ligeiramente arredondadas */
            width: 90%;
            /* Reduzido para 90% para melhor encaixe */
            max-width: 350px;
            /* Limita a largura máxima para se alinhar melhor */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            /* Sombra mais leve */
            transition: transform 0.2s;
            /* Animação ao passar o mouse */
        }

        .friend-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }

        .friend-name {
            font-size: 16px;
            /* Reduzido o tamanho do texto */
            font-weight: bold;
            /* Mantém negrito */
            color: #fff;
            /* Cor do texto */
            text-align: left;
            /* Alinha o texto à esquerda dentro do item */
            margin-left: 10px;
        }

        .friend-item form {
            margin-left: auto;
            display: flex;
            /* Usa flex para controle de posicionamento */
            align-items: center;
            /* Centraliza verticalmente o botão */
        }

        .btn-outline-danger,
        .btn-warning {

            margin-left: auto;
            padding: 8px 16px;
            /* Reduzido o tamanho dos botões */
            font-size: 14px;
            /* Reduzido o tamanho da fonte dos botões */
            border-radius: 6px;
            /* Bordas menos arredondadas */
            border: 1px solid;
            /* Borda mais fina */
            transition: background-color 0.3s, color 0.3s;
            /* Animação de hover */
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            /* Cor de fundo ao passar o mouse */
            color: #fff;
            /* Cor do texto ao passar o mouse */
            border-color: #dc3545;
            /* Mantém a borda */
        }
    </style>
</head>

<body>

    <?php
    // Verifica se o grupo está selecionado
    if (!isset($_SESSION['group_id'])) {
    ?>
        <!-- Topbar com lista de grupos -->
        <div class="topbar">
            <div class="tools">
                <button class="tool-btn" onclick="location.href='melodychat.php';">Início</button>
                <button class="tool-btn" onclick="location.href='profile.php';">Perfil</button>
            </div>
        </div>

        <div class="friends-container">

            <form method="post">
                <button name="criar_grupo" id="botao_criar" class="btn btn-primary" onclick="showForm(); return false;">Criar Novo Grupo</button>
            </form>

            <!-- Formulário para criar um novo grupo (inicialmente escondido) -->
            <div id="create-group-form" style="display:none; margin-top: 20px;">
                <form method="post" id="groupForm">
                    <div class="form-group">
                        <label for="group_name">Nome do Grupo:</label>
                        <input type="text" id="group_name" name="group_name" class="form-control" required>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" name="create_group" class="btn btn-success">Criar Grupo</button>
                        <button type="button" id="close-form" class="btn btn-danger" onclick="hideForm()">Fechar</button>
                    </div>
                </form>
            </div>


            <script>
                function showForm() {
                    document.getElementById('create-group-form').style.display = 'block';
                    document.getElementById('grupos').style.display = 'none';
                    document.getElementById('botao_criar').style.display = 'none';
                }

                function hideForm() {
                    document.getElementById('create-group-form').style.display = 'none';
                    document.getElementById('grupos').style.display = 'block';
                    document.getElementById('botao_criar').style.display = 'block';
                }
            </script>

            <script>
                function refreshPage() {
                    // Recarregar a página
                    location.reload();
                }
            </script>

            <div id="grupos">
                <h2>Seus Grupos</h2>
                <div class="group-grid">
                    <?php
                    // Primeiro, buscamos todos os grupos onde o email está na tabela 'servers'
                    $sql = "SELECT * FROM servers WHERE nome = '" . $_SESSION['email'] . "'";
                    $gruposdisplay = mysqli_query($con, $sql);

                    // Verificamos se a consulta retornou algum grupo
                    if ($gruposdisplay) {
                        if (mysqli_num_rows($gruposdisplay) > 0) {
                            $groupCount = 0; // Contador para controlar o layout
                            while ($server = mysqli_fetch_assoc($gruposdisplay)) {
                                $grupo_id = $server['id']; // ID do grupo do servidor

                                // Buscamos as informações do grupo na tabela 'grupo'
                                $sql_grupo = "SELECT * FROM grupo WHERE id_grupo = '$grupo_id'";
                                $gruposdados = mysqli_query($con, $sql_grupo);

                                if (mysqli_num_rows($gruposdados) > 0) {
                                    $grupo = mysqli_fetch_assoc($gruposdados); // Pega os dados do grupo
                    ?>
                                    <div class="group-card">
                                        <span class="group-name"><?= htmlspecialchars($grupo['nome_grupo']); ?></span>
                                        <span class="group-owner"><?= htmlspecialchars($grupo['proprietario']); ?></span>
                                        <form method="post">
                                            <input type="hidden" name="group_id" value="<?= htmlspecialchars($grupo['id_grupo']); ?>">
                                            <button name="select_group" id="openForm">Entrar</button>
                                        </form>
                                    </div>
                    <?php
                                }
                                $groupCount++;
                                // Adiciona uma quebra de linha após cada 4 grupos (se não for o último)
                                if ($groupCount % 4 == 0 && $groupCount < mysqli_num_rows($gruposdisplay)) {
                                    echo '</div><div class="group-grid">'; // Fecha e abre um novo contêiner de grade
                                }
                            }
                        } else {
                            echo "Você não participa de nenhum grupo.";
                        }
                    } else {
                        echo "Erro ao buscar grupos: " . mysqli_error($con);
                    }
                    ?>
                </div>
            </div>

            <?php
            if (isset($_POST['select_group'])) {
                $_SESSION['pass_aux'] = 1;
            }
            if (isset($_SESSION['pass_aux'])) {
                if (isset($_POST['group_id'])) {

                    $_SESSION['id'] = intval($_POST['group_id']);
                    $sql = "SELECT nome_grupo FROM grupo WHERE id_grupo = " . $_SESSION['id'];
                    $result = mysqli_query($con, $sql);
                    if ($result && mysqli_num_rows($result) > 0) {
                        $groupData = mysqli_fetch_assoc($result);
                        $_SESSION['nome_grupo'] = $groupData['nome_grupo'];

                        $_SESSION['group_id'] = $_SESSION['id'];
                        echo "Você entrou no grupo sem senha!";
                        echo '<script>location.reload();</script>'; // Recarrega a página

                    } else {
                        echo "Erro, grupo não encontrado.";
                    }
                }
            }



            ?>


        </div>
    <?php
    } else {


        $sql = "SELECT * from grupo where id_grupo like '" . $_SESSION['group_id'] . "'";
        $dadosGrupo = mysqli_query($con, $sql);
        $dados = mysqli_fetch_assoc($dadosGrupo);
        $_SESSION['proprietario'] = $dados['proprietario'];
        $_SESSION['nome_grupo'] = $dados['nome_grupo'];
    ?>
        <div class="top-bar">
            <h2>Grupo: <?= htmlspecialchars($dados['nome_grupo'] ?? 'Grupo Desconhecido'); ?></h2>
            <h3>Proprietário: <?= htmlspecialchars($dados['proprietario'] ?? 'Desconhecido'); ?></h3>
            <?php
            if ($_SESSION['email'] == $dados['proprietario']) {
            ?>
                <form method="post">
                    <button type="button" style="margin-left: 0px;" class="btn btn-warning" name="open_popup" id="shareButton">
                        <i class="lni lni-gears-3"></i> Gestão do Grupo
                    </button>
                </form>

            <?php
            }
            ?>
        </div>

        <?php


        if (isset($_POST['mudar_nome_grupo'])) {
            $acesso_nome_change = true;
        }

        if (isset($acesso_nome_change)) {
            $nome_atual = $_SESSION['nome_grupo'];
        ?>
            <!-- Fundo desfocado -->
            <div class="blur-background" id="blurBackground">
                <div class="mudar_nome_grupo">
                    <h3>Mudar Nome do Grupo</h3>

                    <!-- Exibe o nome atual do grupo -->
                    <p>Nome atual: <strong><?= htmlspecialchars($nome_atual) ?></strong></p>

                    <!-- Formulário para mudar o nome -->
                    <form method="post">
                        <input type="text" name="novo_nome_grupo" placeholder="Digite o novo nome">
                        <button type="submit" name="confirmar_nome" class="btn btn-success">Confirmar</button>
                        <button type="submit" class="btn btn-danger" name="fechar_popup">Cancelar</button>
                    </form>
                </div>
            </div>


        <?php
        }
        ?>

        <div class="blur-background" id="blurBackground" style="display: none;">
            <div class="gestao-grupos" id="gestaoPopup" style="display: none;">

                <div class="ferramentas">
                    <button class="voltar_popup" id="closePopup">Fechar</button>
                    <br>
                    <form method="post">
                        <input type="submit" style="margin-left: 0px;" name="mudar_nome_grupo" class="btn btn-warning" value="Mudar Nome do Grupo">
                    </form>
                    <br>
                    <form method="post">
                        <input type="submit" style="margin-left: 0px;" name="eliminar_grupo" class="btn btn-outline-danger" value="Eliminar Grupo">
                    </form>
                    <?php

                    if (isset($_POST['eliminar_grupo'])) {
                        $id_grupo = $_SESSION['group_id'];

                        $sql_eliminar_grupo = "DELETE FROM grupo WHERE id_grupo = $id_grupo";
                        $sql_eliminar_grupo_query = mysqli_query($con, $sql_eliminar_grupo);

                        $sql_eliminar_servers = "DELETE FROM servers WHERE id = $id_grupo";
                        $sql_eliminar_servers_query = mysqli_query($con, $sql_eliminar_servers);





                        $_SESSION['group_id'] = null;

                        $bandeira_eliminar = true;

                        if ($bandeira_eliminar = true) {


                            echo "<script>window.location.reload();</script>";

                            $bandeira_eliminar = false;
                        }
                    }
                    ?>

                </div>

                <div class="membros">
                    <?php
                    $membros = "SELECT * from servers where id like " . $_SESSION['group_id'] . "";
                    $query_membros = mysqli_query($con, $membros);

                    if (mysqli_num_rows($query_membros) > 0) {
                        while ($membros_row = mysqli_fetch_assoc($query_membros)) {
                            $sql = "SELECT * from melodychat where email = '" . $membros_row['nome'] . "'";
                            $sql_query = mysqli_query($con, $sql);

                            while ($member = mysqli_fetch_assoc($sql_query)) {
                    ?>
                                <div class="friend-item">
                                    <img src="<?php echo htmlspecialchars($member['path'] ?? 'melodychat.png'); ?>" alt="Foto de perfil" class="friend-img">
                                    <span class="friend-name"><?php echo htmlspecialchars($member['util']); ?></span>
                                    <form method="post">
                                        <?php
                                        if ($member['email'] == $_SESSION['proprietario']) {
                                        ?>
                                            <input type="submit" class="btn btn-warning" value="Dono">
                                    </form>
                                <?php
                                        } else {
                                ?>
                                    <input type="submit" name="remover_membro" class="btn btn-outline-danger" value="Remover">
                                    <input type="hidden" name="email_amigo_membro" value="<?php echo $member['email']; ?>">
                                    </form>
                                <?php
                                        }
                                ?>
                                </div>
                    <?php
                            }
                        }
                    } else {
                        echo "<p style='text-align: center; color: #ccc;'>Sem membros adicionados</p>";
                    }
                    ?>
                </div>

                <div class="section-adicionar">
                    <h3>Adicionar Amigos</h3>
                    <?php

                    if (isset($aviso)) {

                        echo $aviso;
                    }
                    ?>
                    <div class="amigos-container">
                        <?php
                        $amigos = "SELECT * FROM friends WHERE estado = TRUE AND (solicitacao = '" . $_SESSION['email'] . "' OR pedido = '" . $_SESSION['email'] . "')";
                        $Partilhar_Amigo = mysqli_query($con, $amigos);

                        if ($Partilhar_Amigo === false) {
                            die('Erro na consulta: ' . mysqli_error($con));
                        } else {

                            if (mysqli_num_rows($Partilhar_Amigo) > 0) {
                                while ($friend = mysqli_fetch_assoc($Partilhar_Amigo)) {
                                    if ($friend['pedido'] == $_SESSION['email']) {
                                        $sql = "SELECT * from melodychat where email = '" . $friend['solicitacao'] . "'";

                                        $confirmacao = "SELECT * from servers where nome = '" . $friend['solicitacao'] . "' AND id = '" . $_SESSION['group_id'] . "' ";
                                    } else {
                                        $sql = "SELECT * from melodychat where email = '" . $friend['pedido'] . "'";

                                        $confirmacao = "SELECT * from servers where nome = '" . $friend['pedido'] . "' AND id = '" . $_SESSION['group_id'] . "' ";
                                    }




                                    $resposta_confirmacao = mysqli_query($con, $confirmacao);
                                    if (mysqli_num_rows($resposta_confirmacao) == 0) { // Se não estiver, exibe o amigo
                                        $resultado = mysqli_query($con, $sql);
                                        if ($resultado === false) {
                                            die('Erro na consulta do perfil: ' . mysqli_error($con));
                                        }
                                        while ($row = mysqli_fetch_assoc($resultado)) {
                        ?>
                                            <div class="friend-item">
                                                <img src="<?php echo htmlspecialchars($row['path'] ?? 'melodychat.png'); ?>" alt="Foto de perfil" class="friend-img">
                                                <span class="friend-name"><?php echo htmlspecialchars($row['util']); ?></span>
                                                <form method="post">
                                                    <input type="submit" name="send-adicionar" class="btn-partilhar" value="+">
                                                    <input type="hidden" name="email_amigo" value="<?php echo $row['email']; ?>">
                                                </form>
                                            </div>
                        <?php
                                        }
                                    }
                                }
                            } else {
                                echo 'Não tens amigos adicionados';
                            }
                        }
                        ?>
                    </div>
                </div>


            </div>
        </div>


        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const blurBackground = document.getElementById("blurBackground");
                const gestaoPopup = document.getElementById("gestaoPopup");
                const openButton = document.getElementById("shareButton");
                const closeButton = document.getElementById("closePopup");

                function openPopup() {
                    blurBackground.style.display = "flex"; // Exibe o fundo escuro
                    gestaoPopup.style.display = "block"; // Exibe o popup
                }

                function closePopup() {
                    blurBackground.style.display = "none"; // Esconde o fundo escuro
                    gestaoPopup.style.display = "none"; // Esconde o popup
                }

                // Abre o popup ao clicar no botão
                openButton.addEventListener("click", openPopup);

                // Fecha o popup ao clicar no botão "Fechar"
                closeButton.addEventListener("click", closePopup);
            });
        </script>


        <form method="post">
            <input type="submit" class="voltar" name="voltar" value="Voltar">
        </form>


        <!-- Chat de grupo -->
        <div class="chat-container">
            <div class="messages" id="messages">
                <p>Carregando mensagens...</p>
            </div>
        </div>

        <div class="input-area">
            <form id="message-form">
                <input type="text" id="message-input" name="message" placeholder="Digite sua mensagem..." required>
                <input type="submit" value="Enviar" id="send-button">
            </form>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                function loadMessages() {
                    $.ajax({
                        url: 'ajax/server_msg_ajax.php',
                        type: 'POST',
                        data: {
                            from: '<?= $_SESSION['email'] ?? '' ?>',
                            group_id: '<?= $_SESSION['group_id'] ?? '' ?>',
                            action: 'load_messages'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#messages').html(response.html);
                            } else {
                                console.error('Erro ao carregar mensagens:', response.error);
                                alert('Erro ao carregar mensagens');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro AJAX:', status, error, xhr.responseText);
                            alert('Erro ao carregar mensagens.');
                        }
                    });
                }

                loadMessages();
                setInterval(loadMessages, 1000);

                $('#message-form').submit(function(e) {
                    e.preventDefault();
                    var message = $('#message-input').val();
                    var submitButton = $(this).find('#send-button');

                    if (!message.trim()) {
                        alert('Por favor, digite uma mensagem.');
                        return;
                    }

                    submitButton.prop('disabled', true);

                    $.ajax({
                        url: 'ajax/server_msg_ajax.php',
                        type: 'POST',
                        data: {
                            action: 'send_message',
                            from: '<?= $_SESSION['email'] ?? '' ?>',
                            group_id: '<?= $_SESSION['group_id'] ?? '' ?>',
                            message: message
                        },
                        dataType: 'json',
                        success: function(response) {
                            // Log debug messages
                            if (response.debug && Array.isArray(response.debug)) {
                                response.debug.forEach(function(log) {
                                    console.log(log);
                                });
                            }
                            // Handle response
                            if (response.success) {
                                $('#message-input').val('');
                                loadMessages();
                                var hasScrolled = localStorage.getItem('hasScrolled') === 'true';
                                setTimeout(function() {
                                    if (!hasScrolled) { // Scroll if not already scrolled
                                        var messages = document.getElementById("messages");
                                        if (messages) {
                                            console.log("Elemento #messages encontrado, rolando para o final...");
                                            messages.scrollTop = messages.scrollHeight;
                                            localStorage.setItem('hasScrolled', 'true');
                                        } else {
                                            console.error("Elemento #messages não encontrado!");
                                        }
                                    } else {
                                        console.log("Scroll já foi aplicado anteriormente, pulando...");
                                    }
                                }, 500);
                            } else {
                                console.error('Erro ao enviar mensagem:', response.error);
                                alert('Erro ao enviar mensagem: ' + (response.error || 'Erro desconhecido'));
                            }
                            submitButton.prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro AJAX:', status, error, xhr.responseText);
                            alert('Erro ao enviar mensagem.');
                            submitButton.prop('disabled', false);
                        }
                    });
                });

                $(document).on('click', '.menu_dropdown_mensagens', function() {
                    let dropdown = $(this).next('.dropdown_menu');
                    dropdown.css('display', dropdown.css('display') === 'block' ? 'none' : 'block');
                });
            });
        </script>


        <script>
            $(function() {
                var hasScrolled = localStorage.getItem('hasScrolled') === 'true';
                console.log('hasScrolled:', hasScrolled);

                setTimeout(function() {
                    if (hasScrolled) { // Só rola se ainda não tiver rolado
                        var messages = document.getElementById("messages");
                        if (messages) {
                            console.log("Elemento #messages encontrado, rolando para o final...");
                            messages.scrollTop = messages.scrollHeight; // Rola o conteúdo interno do div para o final
                            localStorage.setItem('hasScrolled', 'true'); // Marca como já scrollado
                        } else {
                            console.error("Elemento #messages não encontrado!");
                        }
                    } else {
                        console.log("Scroll já foi aplicado anteriormente, pulando...");
                    }
                }, 500); // Atraso de 500ms, como no seu exemplo
            });
        </script>


        <?php

        function getHoraAtual()
        {
            date_default_timezone_set('Europe/Lisbon'); // Ajusta para o fuso horário desejado
            $dataHora = date("Y-m-d H:i:s"); // Formato aceito pelo MySQL: YYYY-MM-DD HH:MM:SS
            return $dataHora;
        }





        if (isset($_POST['send_message'])) {
            $from = $_SESSION['email'];
            $message = trim($_POST['message']);
            $dataHora = getHoraAtual(); // Supondo que esta função retorna um valor válido
            $group_id = $_SESSION['group_id'];

            if (!empty($message)) {
                if (strlen($message) <= 500) {
                    // Query segura usando prepared statement
                    $stmt = $con->prepare("INSERT INTO group_messages (from_email, server_id, message, created_at) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("siss", $from, $group_id, $message, $dataHora);

                    if ($stmt->execute()) {

                        date_default_timezone_set('Europe/Lisbon'); // Ajusta para o fuso horário desejado
                        $time_now = date("Y-m-d H:i:s");

                        $login_sql = "UPDATE melodychat set login = 1,login_time = '$time_now' where email = '" . $_SESSION['email'] . "'";
                        $login_query = mysqli_query($con, $login_sql);
                    } else {
                        echo "Erro ao enviar mensagem: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    echo "A mensagem deve ter no máximo 200 caracteres.";
                }
            } else {
                echo "Por favor, digite uma mensagem.";
            }
        }

        ?>

        </div>
    <?php
    }
    ?>

</body>

</html>