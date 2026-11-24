<?php
// Iniciar sessão
session_start();

// Incluir arquivo de conexão com o banco de dados
require('ligacao_melody.php');

// Verificar se o usuário está logado
if (!isset($_SESSION['email'])) {
    // Redirecionar para a página de login caso não esteja logado
    header('Location: login.php');
    exit;
}

// Obter o e-mail do usuário
$email = $_SESSION['email'];

// Deletar o usuário do banco de dados
$query = "DELETE FROM faixas WHERE email = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    // Destruir a sessão do usuário para garantir que ele seja desconectado
    session_destroy();

    // Redirecionar para a página de login ou outra página
    echo "<script>
            alert('Sua conta foi deletada com sucesso.');
            window.location.href = 'login.php';
          </script>";
} else {
    echo "<script>
            alert('Erro ao deletar conta. Tente novamente.');
            window.location.href = 'perfil.php'; // ou qualquer outra página
          </script>";
}

// Fechar a declaração e a conexão
$stmt->close();
$mysqli->close();
?>
