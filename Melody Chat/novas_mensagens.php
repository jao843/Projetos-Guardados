<?php
session_start();
require('ligacao_melody.php'); // Ajuste para o seu arquivo de conexão ao banco de dados

if (!isset($_SESSION['email']) || !isset($_SESSION['email_friend'])) {
    echo json_encode([]);
    exit();
}

$from = $_SESSION['email'];
$to = $_SESSION['email_friend'];

// Busca todas as mensagens
$query = "
    SELECT * FROM mensagens 
    WHERE (from_email = '$from' AND to_email = '$to') 
    OR (from_email = '$to' AND to_email = '$from') 
    ORDER BY created_at ASC
";

$result = mysqli_query($con, $query);

$mensagens = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $mensagens[] = [
            'from_email' => $row['from_email'],
            'message' => htmlspecialchars($row['message']), // Evitar XSS
            'hora' => $row['created_at']
        ];
    }
}

echo json_encode($mensagens);
exit();
