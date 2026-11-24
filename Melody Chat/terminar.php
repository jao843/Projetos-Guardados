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
$query = "DELETE FROM faixas WHERE email = '$email'"; // Consulta SQL para deletar

$result = mysqli_query($con, $query); // Executa a consulta diretamente

if ($result) {
    $_SESSION['flag'] = NULL;
    // Redirecionar para a página de login ou outra página
    echo "<script>
            alert('SESSÃO TERMINADA COM O SPOTIFY.');
            window.location.href = 'profile.php';
          </script>";
} else {
    // Caso haja erro na execução da query
    echo "<script>
            alert('Erro ao terminar conta. Tente novamente.');
            window.location.href = 'profile.php'; // ou qualquer outra página
          </script>";
}

// Fechar a conexão
mysqli_close($con);
?>
