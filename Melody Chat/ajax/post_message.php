<?php
session_start();
require('../ligacao.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['mensagem'])) {
    echo json_encode(['success' => false, 'error' => 'Mensagem inválida ou ausente.']);
    exit();
}

$user_id = intval($_SESSION['id']);
$mensagem = mysqli_real_escape_string($con, $_POST['mensagem']);
$default_foto = 'melodychat.png';
$foto_path = $default_foto;
$is_video = 0;

// Handle file upload
if (isset($_FILES['media'])) {
    $arquivo = $_FILES['media'];
    $max_size = 2 * 1024 * 1024; // 2MB for all files
    $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif'];
    $allowed_video_types = ['video/mp4', 'video/webm', 'video/ogg'];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg'];

    // Check upload error codes
    switch ($arquivo['error']) {
        case UPLOAD_ERR_OK:
            // Proceed with validation
            break;
        case UPLOAD_ERR_INI_SIZE:
            echo json_encode(['success' => false, 'error' => 'Arquivo demasiado grande: Máx 2MB para imagens ou vídeos.']);
            exit();
        case UPLOAD_ERR_FORM_SIZE:
            echo json_encode(['success' => false, 'error' => 'Arquivo demasiado grande: Máx 2MB para imagens ou vídeos.']);
            exit();
        case UPLOAD_ERR_PARTIAL:
            echo json_encode(['success' => false, 'error' => 'O arquivo foi enviado parcialmente.']);
            exit();
        case UPLOAD_ERR_NO_FILE:
            // No file is okay, use default
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            echo json_encode(['success' => false, 'error' => 'Erro no servidor: diretório temporário ausente.']);
            exit();
        case UPLOAD_ERR_CANT_WRITE:
            echo json_encode(['success' => false, 'error' => 'Erro no servidor: falha ao escrever arquivo.']);
            exit();
        case UPLOAD_ERR_EXTENSION:
            echo json_encode(['success' => false, 'error' => 'Erro no servidor: upload interrompido.']);
            exit();
        default:
            echo json_encode(['success' => false, 'error' => 'Erro desconhecido ao enviar o arquivo.']);
            exit();
    }

    if ($arquivo['error'] === UPLOAD_ERR_OK && $arquivo['size'] > 0) {
        // Verify temporary file
        if (!is_uploaded_file($arquivo['tmp_name'])) {
            echo json_encode(['success' => false, 'error' => 'Erro no servidor: arquivo temporário inválido.']);
            exit();
        }

        $file_type = mime_content_type($arquivo['tmp_name']);
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

        // Check file size
        if ($arquivo['size'] > $max_size) {
            echo json_encode(['success' => false, 'error' => 'Arquivo demasiado grande: Máx 2MB para imagens ou vídeos. Comprima vídeos em https://clideo.com/pt/compress-video.']);
            exit();
        }

        // Validate file type and extension
        if (!in_array($file_type, array_merge($allowed_image_types, $allowed_video_types)) || !in_array($extensao, $allowed_extensions)) {
            echo json_encode(['success' => false, 'error' => 'Tipo de arquivo não aceite. Use JPG, PNG, GIF, MP4, WebM ou OGG.']);
            exit();
        }

        if (in_array($file_type, $allowed_video_types)) {
            $is_video = 1;
        }

        $pasta = 'Uploads/';
        $novonomedoarquivo = uniqid();
        $file_path = $pasta . $novonomedoarquivo . '.' . $extensao;

        // Create upload directory if it doesn't exist
        if (!is_dir('../' . $pasta)) {
            if (!mkdir('../' . $pasta, 0755, true)) {
                echo json_encode(['success' => false, 'error' => 'Erro no servidor: falha ao criar diretório de upload.']);
                exit();
            }
        }

        // Check directory permissions
        if (!is_writable('../' . $pasta)) {
            echo json_encode(['success' => false, 'error' => 'Erro no servidor: diretório de upload não gravável.']);
            exit();
        }

        // Move uploaded file
        if (!move_uploaded_file($arquivo['tmp_name'], '../' . $file_path)) {
            echo json_encode(['success' => false, 'error' => 'Erro ao enviar o arquivo.']);
            exit();
        }

        $foto_path = mysqli_real_escape_string($con, $file_path);
    }
}

// Insert post using mysqli_query
$query = "INSERT INTO forum_mensagens (id_user, mensagem, foto, is_video, timestamp) 
          VALUES ($user_id, '$mensagem', '$foto_path', $is_video, NOW())";

if (mysqli_query($con, $query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erro ao salvar o post: ' . mysqli_error($con)]);
}

mysqli_close($con);
