<?php
session_start();
require('ligacao_melody.php'); // Ajuste para o seu arquivo de conexão ao banco de dados

if (!isset($_SESSION['email']) || !isset($_SESSION['nome_grupo'])) {
    echo json_encode([]);
    exit();
}
$lastFetchTime = $_SESSION['last_message_time'] ?? '2025-01-01 00:00:00'; // Hora inicial

$from = $_SESSION['email'];
$nome_grupo = $_SESSION['nome_grupo'];

// Consulta para pegar as novas mensagens após o último tempo armazenado
$messagesQuery = "
    SELECT gm.message, gm.created_at, gm.from_email, mc.util AS nome, mc.path
    FROM group_messages gm
    LEFT JOIN melodychat mc ON gm.from_email = mc.email
    WHERE gm.server = '$nome_grupo' AND gm.created_at > '$lastFetchTime'
    ORDER BY gm.created_at ASC
";

$messagesResult = mysqli_query($con, $messagesQuery);
$messages = [];

if ($messagesResult && mysqli_num_rows($messagesResult) > 0) {
    while ($row = mysqli_fetch_assoc($messagesResult)) {
        $messages[] = [
            'message' => $row['message'],
            'hora' => $row['created_at'],
            'from_email' => $row['from_email'],
            'nome' => $row['nome'] ?? 'Usuário desconhecido',
            'path' => $row['path'] ?? 'default-profile.png'
        ];
    }
    $_SESSION['last_message_time'] = end($messages)['hora']; // Última hora das mensagens carregadas
}

echo json_encode($mensagens);
exit();
