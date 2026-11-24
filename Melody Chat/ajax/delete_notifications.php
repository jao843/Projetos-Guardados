<?php
header('Content-Type: application/json');

$response = array('success' => false, 'error' => '');

if (!isset($_SESSION['email'])) {
    $response['error'] = 'Usuário não autenticado';
    echo json_encode($response);
    exit;
}

if (!isset($_POST['id_ntf_delete'])) {
    $response['error'] = 'ID da notificação não fornecido';
    echo json_encode($response);
    exit;
}

$notification_id = intval($_POST['id_ntf_delete']);
$email = $_SESSION['email'];


// Escapar o e-mail para evitar SQL Injection
$email_escaped = mysqli_real_escape_string($con, $email);

// Executar a exclusão
$delete_sql = "DELETE FROM notifications WHERE id = $notification_id AND email_user = '$email_escaped'";
if (mysqli_query($con, $delete_sql)) {
    $response['success'] = true;
} else {
    $response['error'] = 'Erro ao excluir a notificação: ' . mysqli_error($con);
}

mysqli_close($con);
echo json_encode($response);
?>