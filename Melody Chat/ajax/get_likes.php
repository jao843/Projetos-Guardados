<?php
session_start();
require('../ligacao.php');

header('Content-Type: application/json');

// Check database connection
if (!$con) {
    echo json_encode(['success' => false, 'error' => 'Falha na conexão com o banco de dados: ' . mysqli_connect_error()]);
    exit();
}

$query = "SELECT id_post, COUNT(*) AS like_count FROM forum_likes GROUP BY id_post";
$result = mysqli_query($con, $query);

if (!$result) {
    echo json_encode(['success' => false, 'error' => 'Erro na query: ' . mysqli_error($con)]);
    exit();
}

$likes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $likes[] = [
        'post_id' => (int)$row['id_post'],
        'like_count' => (int)$row['like_count']
    ];
}

echo json_encode(['success' => true, 'likes' => $likes]);

mysqli_close($con);
?>