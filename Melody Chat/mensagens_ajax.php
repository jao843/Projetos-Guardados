<?php
session_start();
require('../ligacao_melody.php');

header('Content-Type: application/json');

if (!isset($_SESSION['email']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'error' => 'Sessão inválida ou ação não especificada']);
    exit();
}

// Função para transformar URLs em links clicáveis
function makeLinksClickable($text)
{
    // Regex para encontrar URLs começando com http:// ou https://
    $pattern = '/(https?:\/\/[^\s]+)/i';
    return preg_replace($pattern, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>', $text);
}

$action = $_POST['action'];
$from = $_SESSION['email'];
$to = $_POST['to'] ?? '';

if ($action === 'load_messages') {
    $messagesQuery = "SELECT * FROM mensagens WHERE (from_email = ? AND to_email = ?) OR (from_email = ? AND to_email = ?) ORDER BY created_at ASC";
    $stmt = $con->prepare($messagesQuery);
    $stmt->bind_param("ssss", $from, $to, $to, $from);
    $stmt->execute();
    $result = $stmt->get_result();

    $html = '';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id_message = $row['id'];
            $message = htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8');
            $hora = $row['created_at'];
            $isOwnMessage = ($row['from_email'] === $from);

            // Transforma URLs em links clicáveis
            $messageWithLinks = makeLinksClickable($message);

            $html .= '<div class="message ' . ($isOwnMessage ? 'own' : 'member') . '">';
            $html .= '<br>';
            $html .= '<div class="message-content">';
            $html .= '<p class="text">' . $messageWithLinks . '</p>'; // Usa a mensagem com links
            if ($isOwnMessage) {
                $html .= '<small class="time" style="color:white;">' . $hora . '</small>';
            } else {
                $html .= '<small class="time">' . $hora . '</small>';
            }
            $html .= '</div>';

            if ($isOwnMessage) {
                $html .= '<div class="message-actions">';
                $html .= '<button class="menu_dropdown_mensagens" onclick="toggleDropdown(this)">';
                $html .= '<i class="lni lni-menu-meatballs-1"></i>';
                $html .= '</button>';
                $html .= '<div class="dropdown_menu" style="display: none;">';
                $html .= '<form method="post" action="chats.php">';
                $html .= '<input type="hidden" name="id_message" value="' . htmlspecialchars($id_message, ENT_QUOTES, 'UTF-8') . '">';
                $html .= '<button type="submit" name="delete_message" class="delete_button">Apagar</button>';
                $html .= '<button type="submit" name="edit_message" class="edit_message">Editar</button>';
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</div>'; // Fecha o message-actions
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
    $message = trim($_POST['message']);
    if (empty($message)) {
        echo json_encode(['success' => false, 'error' => 'Mensagem vazia']);
        exit();
    }
    if (strlen($message) > 2000) {
        echo json_encode(['success' => false, 'error' => 'Mensagem muito longa']);
        exit();
    }

    $dataHora = date("Y-m-d H:i:s");

    // Insert message
    $stmt = $con->prepare("INSERT INTO mensagens (from_email, to_email, message, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $from, $to, $message, $dataHora);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Erro ao enviar mensagem']);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Update login status
    $stmt = $con->prepare("UPDATE melodychat SET login = 1, login_time = ? WHERE email = ?");
    $stmt->bind_param("ss", $dataHora, $from);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar login']);
        $stmt->close();
        exit();
    }
    $stmt->close();

    // Fetch recipient's name
    $stmt = $con->prepare("SELECT util FROM melodychat WHERE email = ?");
    $stmt->bind_param("s", $from);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Destinatário não encontrado']);
        $stmt->close();
        exit();
    }
    $dados = $result->fetch_assoc();
    $nome = $dados['util'];
    $stmt->close();

    // Insert notification
    $titulo = 'Mensagem Nova';
    $texto = "Tem uma mensagem nova do seu amigo: $nome";
    $link = 'chats.php';
    $is_read = 0; // FALSE as integer for MySQL
    $stmt = $con->prepare("INSERT INTO notifications (email_user, email_user_causa, link, is_read, titulo, texto) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $to, $from, $link, $is_read, $titulo, $texto);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Erro ao criar notificação']);
        $stmt->close();
        exit();
    }
    $stmt->close();

    echo json_encode(['success' => true]);
    exit();
}


if ($action === 'edit_message') {
    $message_id = $_POST['message_id'] ?? 0;
    $new_message = trim($_POST['message'] ?? '');

    if (empty($new_message)) {
        echo json_encode(['success' => false, 'error' => 'Mensagem vazia']);
        exit();
    }
    if (strlen($new_message) > 2000) {
        echo json_encode(['success' => false, 'error' => 'Mensagem muito longa']);
        exit();
    }

    // Verificar se a mensagem pertence ao usuário
    $stmt = $con->prepare("SELECT from_email FROM mensagens WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $message_data = $result->fetch_assoc();
    $stmt->close();

    if (!$message_data || $message_data['from_email'] !== $from) {
        echo json_encode(['success' => false, 'error' => 'Mensagem inválida ou sem permissão']);
        exit();
    }

    // Atualizar mensagem
    $stmt = $con->prepare("UPDATE mensagens SET message = ?, updated_at = ? WHERE id = ?");
    $dataHora = date("Y-m-d H:i:s");
    $stmt->bind_param("ssi", $new_message, $dataHora, $message_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro ao atualizar mensagem']);
    }
    $stmt->close();
    exit();
}

echo json_encode(['success' => false, 'error' => 'Ação inválida']);
