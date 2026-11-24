<?php
session_start();
require("../ligacao_melody.php");

if (!isset($_SESSION['visu_perfil']) || !isset($_POST['user_id']) || !isset($_POST['action'])) {
    echo 'error: Dados inválidos';
    exit();
}

$user_id = intval($_POST['user_id']);
$action = $_POST['action'];
$max_genres = 10;

if ($action === 'add') {
    if (!isset($_POST['genre'])) {
        echo 'error: Gênero não fornecido';
        exit();
    }

    $genre = trim($_POST['genre']);
    if ($genre === '') {
        echo 'error: Gênero inválido';
        exit();
    }

    // Verifica o limite de gêneros
    $sql = "SELECT COUNT(*) as total FROM generos_profile WHERE id_user = $user_id";
    $query = mysqli_query($con, $sql);
    if (!$query) {
        echo 'error: Erro na consulta ao banco de dados';
        exit();
    }
    $row = mysqli_fetch_assoc($query);
    if ($row['total'] >= $max_genres) {
        echo 'error: Limite de gêneros atingido';
        exit();
    }

    // Sanitiza o gênero
    $genre = mysqli_real_escape_string($con, $genre);

    // Insere o novo gênero
    $sql = "INSERT INTO generos_profile (id_user, generos) VALUES ($user_id, '$genre')";
    if (mysqli_query($con, $sql)) {
        $new_id = mysqli_insert_id($con);
        echo 'success:' . $new_id;
    } else {
        echo 'error: Falha ao adicionar gênero';
    }
} elseif ($action === 'remove') {
    if (!isset($_POST['id'])) {
        echo 'error: ID não fornecido';
        exit();
    }

    $id = intval($_POST['id']);

    // Verifica se o gênero existe
    $sql = "SELECT id FROM generos_profile WHERE id = $id AND id_user = $user_id";
    $query = mysqli_query($con, $sql);
    if (!$query || mysqli_num_rows($query) === 0) {
        echo 'error: Gênero não encontrado';
        exit();
    }

    // Remove o gênero
    $sql = "DELETE FROM generos_profile WHERE id = $id AND id_user = $user_id";
    if (mysqli_query($con, $sql)) {
        echo 'success';
    } else {
        echo 'error: Falha ao remover gênero';
    }
} else {
    echo 'error: Ação inválida';
}

mysqli_close($con);
?>