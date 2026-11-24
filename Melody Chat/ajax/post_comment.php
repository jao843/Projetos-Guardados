<?php
session_start();
require('../ligacao.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['mensagem']) || empty($_POST['post_id'])) {
    echo json_encode(['success' => false, 'error' => 'Mensagem ou ID do post inválido.']);
    exit();
}

$user_id = intval($_SESSION['id']);
$post_id = intval($_POST['post_id']);
$mensagem = mysqli_real_escape_string($con, $_POST['mensagem']);

// Fetch user info for response
$query = "SELECT util, path FROM melodychat WHERE id = $user_id";
$result = mysqli_query($con, $query);
if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode(['success' => false, 'error' => 'Erro ao obter dados do usuário.']);
    exit();
}
$user = mysqli_fetch_assoc($result);

// Insert comment
$query = "INSERT INTO forum_coment (id_post, id_user, coment, timestamp) VALUES ($post_id, $user_id, '$mensagem', NOW())";
if (mysqli_query($con, $query)) {
    echo json_encode([
        'success' => true,
        'user_name' => $user['util'],
        'user_photo' => $user['path'] ?: 'melodychat.png',
        'timestamp' => date('d \d\e F, Y \à\s H:i')
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erro ao salvar o comentário: ' . mysqli_error($con)]);
}

mysqli_close($con);
?>