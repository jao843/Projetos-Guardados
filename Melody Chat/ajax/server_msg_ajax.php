<?php
session_start();
require('../ligacao_melody.php');

// Enable error reporting for development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if (!isset($_SESSION['email']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'error' => 'Sessão inválida ou ação não especificada'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

$action = $_POST['action'];
$from = $_SESSION['email'];
$group_id = $_POST['group_id'] ?? $_SESSION['group_id'] ?? '';

if ($action === 'load_messages') {
    $messagesQuery = "
        SELECT gm.id_message, gm.message, gm.created_at, gm.from_email, mc.util AS nome, mc.path
        FROM group_messages gm
        LEFT JOIN melodychat mc ON gm.from_email = mc.email
        WHERE gm.server_id = ?
        ORDER BY gm.created_at ASC
    ";
    $stmt = $con->prepare($messagesQuery);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Erro ao preparar a consulta SQL'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $html = '';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id_message = $row['id_message'];
            $message = htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8');
            $hora = $row['created_at'];
            $nome = htmlspecialchars($row['nome'] ?? 'Usuário desconhecido');
            $path = htmlspecialchars($row['path'] ?? 'default-profile.png');
            $isOwnMessage = ($row['from_email'] === $from);

            $html .= '<div class="message ' . ($isOwnMessage ? 'own' : 'member') . '">';
            if (!$isOwnMessage) {
                $html .= '<div class="header">';
                $html .= '<img src="' . $path . '" alt="' . $nome . '" class="photo">';
                $html .= '<span class="author">' . $nome . '</span>';
                $html .= '</div>';
            }
            $html .= '<br>';
            $html .= '<div class="message-content">';
            $html .= '<p class="text">' . $message . '</p>';
            if ($isOwnMessage) {
                $html .= '<small class="time" style="color:white;">' . $hora . '</small>';
            } else {
                $html .= '<small class="time">' . $hora . '</small>';
            }
            $html .= '</div>';
            if ($isOwnMessage) {
                $html .= '<button class="menu_dropdown_mensagens" onclick="toggleDropdown(this)">';
                $html .= '<i class="lni lni-menu-meatballs-1"></i>';
                $html .= '</button>';
                $html .= '<div class="dropdown_menu" style="display: none;">';
                $html .= '<form method="post" action="server.php">';
                $html .= '<input type="hidden" name="id_message" value="' . $id_message . '">';
                $html .= '<button type="submit" name="delete_message" class="delete_button">Apagar mensagem</button>';
                $html .= '</form>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }
    } else {
        $html = '<p>Nenhuma mensagem encontrada.</p>';
    }

    echo json_encode(['success' => true, 'html' => $html], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

if ($action === 'send_message') {
    $debug_logs = [];
    $debug_logs[] = "[SEND_MESSAGE] Iniciando ação send_message para group_id: $group_id, from: $from";

    // Step 1: Validate message
    $debug_logs[] = "[SEND_MESSAGE] Etapa 1: Validando mensagem";
    $message = trim($_POST['message'] ?? '');
    if (empty($message)) {
        $debug_logs[] = "[SEND_MESSAGE] Erro: Mensagem vazia";
        echo json_encode(['success' => false, 'error' => 'Mensagem vazia', 'debug' => $debug_logs], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
    if (strlen($message) > 500) {
        $debug_logs[] = "[SEND_MESSAGE] Erro: Mensagem muito longa (tamanho: " . strlen($message) . ")";
        echo json_encode(['success' => false, 'error' => 'Mensagem muito longa', 'debug' => $debug_logs], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
    $debug_logs[] = "[SEND_MESSAGE] Mensagem válida: " . substr($message, 0, 50) . (strlen($message) > 50 ? '...' : '');

    // Step 2: Insert message into group_messages
    $debug_logs[] = "[SEND_MESSAGE] Etapa 2: Inserindo mensagem no banco de dados";
    $dataHora = date("Y-m-d H:i:s");
    $from_escaped = mysqli_real_escape_string($con, $from);
    $group_id_escaped = mysqli_real_escape_string($con, $group_id);
    $message_escaped = mysqli_real_escape_string($con, $message);
    
    $sql = "INSERT INTO group_messages (from_email, server_id, message, created_at) 
            VALUES ('$from_escaped', '$group_id_escaped', '$message_escaped', '$dataHora')";
    
    if (mysqli_query($con, $sql)) {
        $debug_logs[] = "[SEND_MESSAGE] Mensagem inserida com sucesso no group_messages";

        // Step 3: Fetch all group members' emails
        $debug_logs[] = "[SEND_MESSAGE] Etapa 3: Buscando todos os emails do grupo (ID: $group_id)";
        $sql = "SELECT nome FROM servers WHERE id = '$group_id_escaped'";
        $result = mysqli_query($con, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $emails = [];
            $raw_nomes = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $raw_nomes[] = $row['nome'];
                if (!empty($row['nome'])) {
                    $emails[] = trim($row['nome']);
                }
            }
            $emails = array_filter($emails); // Remove empty emails
            $emails = array_unique($emails); // Remove duplicates
            $debug_logs[] = "[SEND_MESSAGE] Valores brutos de nome: " . implode(', ', $raw_nomes);
            $debug_logs[] = "[SEND_MESSAGE] Emails armazenados no array: " . (empty($emails) ? 'Nenhum' : implode(', ', $emails));

            // Step 4: Get sender's username
            $debug_logs[] = "[SEND_MESSAGE] Etapa 4: Buscando nome do remetente (email: $from)";
            $sql = "SELECT util FROM melodychat WHERE email = '$from_escaped'";
            $user_result = mysqli_query($con, $sql);
            if ($user_result && mysqli_num_rows($user_result) > 0) {
                $sender = mysqli_fetch_assoc($user_result);
                $sender_name = $sender['util'] ?? 'Utilizador';
                $debug_logs[] = "[SEND_MESSAGE] Remetente encontrado: $sender_name";
            } else {
                $sender_name = 'Utilizador';
                $debug_logs[] = "[SEND_MESSAGE] Erro ao buscar remetente: " . mysqli_error($con);
            }

            // Step 5: Send notifications one by one
            $debug_logs[] = "[SEND_MESSAGE] Etapa 5: Enviando notificações";
            $notification_count = 0;
            if (count($emails) === 1 && strtolower($emails[0]) === strtolower($from)) {
                $debug_logs[] = "[SEND_MESSAGE] Grupo com apenas o remetente; nenhuma notificação será enviada";
            } else {
                foreach ($emails as $email) {
                    $debug_logs[] = "[SEND_MESSAGE] Avaliando email: " . ($email ? $email : 'Vazio');
                    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $debug_logs[] = "[SEND_MESSAGE] Email válido: $email";
                        if (strtolower($email) !== strtolower($from)) {
                            $debug_logs[] = "[SEND_MESSAGE] Processando notificação para: $email";
                            $email_escaped = mysqli_real_escape_string($con, $email);
                            $notification_title = "Mensagem de Grupo";
                            $notification_text = "Tem uma mensagem nova de grupo de $sender_name: " . 
                                                substr($message, 0, 50) . (strlen($message) > 50 ? '...' : '');
                            $notification_link = "server.php?id=$group_id";
                            $is_read = 0;

                            $notification_text_escaped = mysqli_real_escape_string($con, $notification_text);
                            $notification_title_escaped = mysqli_real_escape_string($con, $notification_title);
                            $notification_link_escaped = mysqli_real_escape_string($con, $notification_link);

                            $sql = "INSERT INTO notifications (email_user, email_user_causa, link, is_read, titulo, texto, data) 
                                    VALUES ('$email_escaped', '$from_escaped', '$notification_link_escaped', $is_read, 
                                            '$notification_title_escaped', '$notification_text_escaped', '$dataHora')";
                            $debug_logs[] = "[SEND_MESSAGE] SQL da notificação: $sql";
                            
                            if (mysqli_query($con, $sql)) {
                                $debug_logs[] = "[SEND_MESSAGE] Notificação inserida para $email";
                                $notification_count++;
                            } else {
                                $debug_logs[] = "[SEND_MESSAGE] Erro ao inserir notificação para $email: " . mysqli_error($con);
                            }
                        } else {
                            $debug_logs[] = "[SEND_MESSAGE] Ignorando notificação para remetente: $email";
                        }
                    } else {
                        $debug_logs[] = "[SEND_MESSAGE] Email inválido ou vazio ignorado: " . ($email ? $email : 'Vazio');
                    }
                }
            }
            $debug_logs[] = "[SEND_MESSAGE] Concluído: $notification_count notificações enviadas";
            echo json_encode(['success' => true, 'debug' => $debug_logs], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit();
        } else {
            $debug_logs[] = "[SEND_MESSAGE] Erro: Grupo não encontrado ou consulta falhou: " . mysqli_error($con);
            echo json_encode(['success' => false, 'error' => 'Grupo não encontrado', 'debug' => $debug_logs], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit();
        }
    } else {
        $debug_logs[] = "[SEND_MESSAGE] Erro ao inserir mensagem: " . mysqli_error($con);
        echo json_encode(['success' => false, 'error' => 'Erro ao inserir mensagem: ' . mysqli_error($con), 'debug' => $debug_logs], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }
}

echo json_encode(['success' => false, 'error' => 'Ação inválida'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit();
?>