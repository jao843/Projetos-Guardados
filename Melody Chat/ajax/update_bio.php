<?php
session_start();
require("../ligacao_melody.php");

if (!isset($_SESSION['visu_perfil']) || !isset($_POST['bio']) || !isset($_POST['user_id'])) {
    echo 'error: Dados inválidos';
    exit();
}

$user_id = intval($_POST['user_id']);
$new_bio = trim($_POST['bio']);
$max_words = 300;

// Conta palavras
$word_count = $new_bio === '' ? 0 : count(preg_split('/\s+/', $new_bio, -1, PREG_SPLIT_NO_EMPTY));

if ($word_count > $max_words) {
    echo 'error: A biografia excede o limite de ' . $max_words . ' palavras';
    exit();
}

// Escapa a entrada para evitar SQL Injection
$new_bio = mysqli_real_escape_string($con, $new_bio);

// Atualiza a biografia no banco de dados
$sql = "UPDATE melodychat SET bio = '$new_bio' WHERE id = $user_id";
if (mysqli_query($con, $sql)) {
    echo 'success';
} else {
    echo 'error: Falha ao atualizar o banco de dados';
}
?>