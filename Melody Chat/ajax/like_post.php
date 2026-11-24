<?php
session_start();
require('../ligacao.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
    echo json_encode(['success' => false, 'error' => 'Requisição inválida ou post_id inválido.']);
    exit();
}

$post_id = intval($_POST['post_id']);
$user_id = intval($_SESSION['id']);

// Check database connection
if (!$con) {
    echo json_encode(['success' => false, 'error' => 'Falha na conexão com o banco de dados: ' . mysqli_connect_error()]);
    exit();
}

// Check if the user has already liked the post
$check_query = "SELECT * FROM forum_likes WHERE id_post = $post_id AND id_user = $user_id";
$check_result = mysqli_query($con, $check_query);

if (!$check_result) {
    echo json_encode(['success' => false, 'error' => 'Erro na query de verificação: ' . mysqli_error($con)]);
    exit();
}

if (mysqli_num_rows($check_result) === 0) {
    // User hasn't liked, so add like
    $query = "INSERT INTO forum_likes (id_post, id_user) VALUES ($post_id, $user_id)";
    if (!mysqli_query($con, $query)) {
        echo json_encode(['success' => false, 'error' => 'Erro ao adicionar like: ' . mysqli_error($con)]);
        exit();
    }
} else {
    // User has liked, so remove like
    $query = "DELETE FROM forum_likes WHERE id_post = $post_id AND id_user = $user_id";
    if (!mysqli_query($con, $query)) {
        echo json_encode(['success' => false, 'error' => 'Erro ao remover like: ' . mysqli_error($con)]);
        exit();
    }
}

// Get updated like count
$count_query = "SELECT COUNT(*) AS like_count FROM forum_likes WHERE id_post = $post_id";
$count_result = mysqli_query($con, $count_query);

if (!$count_result) {
    echo json_encode(['success' => false, 'error' => 'Erro na query de contagem: ' . mysqli_error($con)]);
    exit();
}

$like_count = mysqli_fetch_assoc($count_result)['like_count'];

echo json_encode(['success' => true, 'like_count' => (int)$like_count]);

mysqli_close($con);
?>