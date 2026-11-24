<?php
session_start();
require('ligacao.php');

// Set locale to Portuguese for date formatting
setlocale(LC_TIME, 'pt_PT.UTF-8');

// Redirect to login if not authenticated
if (!isset($_SESSION['id'])) {
    header('location:dsession_melodychat.php');
    exit();
}

// Handle profile view form submission
if (isset($_POST['view_profile'])) {
    $_SESSION['visu_perfil'] = intval($_POST['user_id']); // Store user ID
    header('Location: profile_user.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="transparente.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.lineicons.com/5.0/lineicons.css" rel="stylesheet" />
    <title>Melody Chat - Fórum</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            font-family: 'Arial', sans-serif;
            background-image: url('fundo.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .navbar-top {
            width: 100%;
            height: 70px;
            background-color: #181818;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .navbar-top .logo {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            margin-right: 20px;
        }

        .navbar-top form {
            display: flex;
            gap: 20px;
            margin-left: auto;
        }

        .navbar-top input {
            padding: 10px;
            background-color: #181818;
            font-size: 15px;
            font-weight: bold;
            border: none;
            color: white;
            cursor: pointer;
        }

        .navbar-top input:hover {
            color: #a0a0a0;
        }

        .dropdown {
            position: relative;
            display: inline-block;
            margin-left: 30px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #181818;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 8px;
            left: -120px;
            top: 40px;
        }

        .dropdown-content a {
            color: white;
            padding: 10px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        .dropdown-content a:hover {
            background-color: #3a3a3a;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .left-section {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 50px;
            height: 50px;
            margin-right: 10px;
            border-radius: 8px;
        }

        .navbar-options {
            display: flex;
            gap: 10px;
        }

        .navbar-options input {
            background-color: #181818;
            font-size: 15px;
            font-weight: bold;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
        }

        .navbar-options input:hover {
            color: #a0a0a0;
        }

        #main {
            margin-top: 90px;
            padding: 20px;
            width: 100%;
            height: calc(100vh - 110px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }

        .background-img {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: blur(8px);
            z-index: -1;
        }

        .forum-card {
            background-color: #1f1f1f;
            padding: 40px;
            border-radius: 10px;
            width: 100%;
            max-width: 1200px;
            height: 1300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            position: relative;
        }

        .forum-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .forum-card h2 {
            font-size: 24px;
            margin: 0;
            color: #ffffff;
        }

        .forum-thread {
            background-color: #2c2c2c;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 13px;
        }

        .thread-header {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .thread-header img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #555555;
        }

        .thread-header .username {
            font-weight: bold;
            color: #ffffff;
            font-size: 14px;
        }

        .thread-header .timestamp {
            color: #cccccc;
            font-size: 11px;
        }

        .thread-content {
            color: #ffffff;
            font-size: 13px;
            line-height: 1.4;
        }

        .thread-stats {
            display: flex;
            gap: 15px;
            color: #cccccc;
            font-size: 11px;
        }

        .thread-stats i {
            margin-right: 4px;
        }

        .thread-stats .like-btn, .thread-stats .comment-btn {
            cursor: pointer;
            transition: color 0.2s;
        }

        .thread-stats .like-btn.liked {
            color: #e74c3c;
        }

        .thread-stats .like-btn:hover:not(.disabled), .thread-stats .comment-btn:hover:not(.disabled) {
            color: #e74c3c;
        }

        .thread-stats .like-btn.disabled, .thread-stats .comment-btn.disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }

        .post-modal, .comment-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .post-modal.show, .comment-modal.show {
            display: flex;
        }

        .post-modal-content, .comment-modal-content {
            background-color: #2c2c2c;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            color: #ffffff;
        }

        .post-modal-content h3, .comment-modal-content h3 {
            margin: 0 0 15px;
            font-size: 20px;
            text-align: center;
        }

        .post-modal-content textarea, .comment-modal-content textarea {
            width: 100%;
            background-color: #3a3a3a;
            border: 1px solid #555555;
            border-radius: 5px;
            padding: 10px;
            color: #ffffff;
            font-size: 14px;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 10px;
        }

        .post-modal-content input[type="file"] {
            width: 100%;
            padding: 10px 0;
            color: #ffffff;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .post-modal-content input[type="file"]::-webkit-file-upload-button {
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            color: #ffffff;
            font-size: 13px;
            cursor: pointer;
        }

        .post-modal-content input[type="file"]::-webkit-file-upload-button:hover {
            background-color: #45a049;
        }

        .post-modal-content .button-group, .comment-modal-content .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .post-modal-content button, .comment-modal-content button {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }

        .post-modal-content .submit-btn, .comment-modal-content .submit-btn {
            background-color: #4CAF50;
            color: #ffffff;
        }

        .post-modal-content .submit-btn:hover, .comment-modal-content .submit-btn:hover {
            background-color: #45a049;
        }

        .post-modal-content .cancel-btn, .comment-modal-content .cancel-btn {
            background-color: #555555;
            color: #ffffff;
        }

        .post-modal-content .cancel-btn:hover, .comment-modal-content .cancel-btn:hover {
            background-color: #666666;
        }

        @media (max-width: 600px) {
            .post-modal-content, .comment-modal-content {
                width: 95%;
                padding: 15px;
            }

            .post-modal-content h3, .comment-modal-content h3 {
                font-size: 18px;
            }

            .post-modal-content textarea, .comment-modal-content textarea {
                font-size: 13px;
            }

            .post-modal-content button, .comment-modal-content button {
                font-size: 12px;
                padding: 7px 14px;
            }
        }

        .forum-card::before {
            content: '\f001';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            font-size: 24px;
            color: #ffffff;
            position: absolute;
            top: 10px;
            left: 10px;
            opacity: 0.3;
        }

        footer {
            background-color: #181818;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .create-post-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .create-post-btn:hover {
            background-color: #45a049;
        }

        .create-comment-btn {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .create-comment-btn:hover {
            background-color: #0056b3;
        }

        .create-comment-btn i {
            font-size: 12px;
        }

        .thread-media {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .media-preview {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .media-preview img, .media-preview video {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
        }

        .media-preview i {
            font-size: 16px;
            color: #cccccc;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .media-preview i.expanded {
            transform: rotate(180deg);
        }

        .media-full {
            display: none;
            margin-top: 10px;
        }

        .media-full img, .media-full video {
            max-width: 100%;
            border-radius: 5px;
        }

        .media-full video {
            width: 100%;
            max-height: 500px;
        }

        .media-full.show {
            display: block;
        }

        .comments-section {
            display: none;
            margin-top: 10px;
            padding: 10px;
            background-color: #3a3a3a;
            border-radius: 5px;
        }

        .comments-section.show {
            display: block;
        }

        .comment {
            padding: 8px;
            border-bottom: 1px solid #555555;
            font-size: 12px;
        }

        .comment:last-child {
            border-bottom: none;
        }

        .comment-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
        }

        .comment-header img {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #555555;
        }

        .comment-header .username {
            font-weight: bold;
            color: #ffffff;
            font-size: 12px;
        }

        .comment-header .timestamp {
            color: #cccccc;
            font-size: 10px;
        }

        .comment-content {
            color: #ffffff;
            font-size: 12px;
            line-height: 1.4;
        }

        .profile-btn {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php
    require("header.php");
    ?>
    <div id="main">
        <img src="pexels-edwardeyer-811838.jpg" alt="Imagem de fundo" class="background-img">

        <div class="forum-card">
            <div class="forum-header">
                <h2>Fórum Público</h2>
                <button id="openPostForm" class="create-post-btn">
                    <i class="fas fa-plus"></i> Criar Post
                </button>
            </div>

            <?php
            $query = "SELECT fm.id, fm.id_user, fm.mensagem, fm.foto, fm.is_video, fm.timestamp,
                             (SELECT COUNT(*) FROM forum_likes fl WHERE fl.id_post = fm.id) AS like_count,
                             (SELECT COUNT(*) FROM forum_coment fc WHERE fc.id_post = fm.id) AS comment_count
                      FROM forum_mensagens fm
                      ORDER BY like_count DESC";
            $result = mysqli_query($con, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $post_id = $row['id'];
                    $timestamp = strftime('%d de %B, %Y às %H:%M', strtotime($row['timestamp']));
                    $mensagem = htmlspecialchars($row['mensagem']);
                    $foto_post = htmlspecialchars($row['foto']);
                    $is_video = $row['is_video'];
                    $like_count = $row['like_count'];
                    $comment_count = $row['comment_count'];

                    $sql = "SELECT util, path FROM melodychat WHERE id = " . intval($row['id_user']);
                    $query_user = mysqli_query($con, $sql);
                    $dados = mysqli_fetch_assoc($query_user);

                    $liked = false;
                    if (isset($_SESSION['id'])) {
                        $user_id = $_SESSION['id'];
                        $like_query = "SELECT * FROM forum_likes WHERE id_post = " . intval($post_id) . " AND id_user = " . intval($user_id);
                        $like_result = mysqli_query($con, $like_query);
                        $liked = mysqli_num_rows($like_result) > 0;
                    }
                    $foto_user = !empty($dados['path']) ? $dados['path'] : 'melodychat.png';
            ?>
                    <div class="forum-thread" data-post-id="<?php echo $post_id; ?>">
                        <div class="thread-header">
                            <form method="POST" action="">
                                <input type="hidden" name="user_id" value="<?php echo $row['id_user']; ?>">
                                <button type="submit" name="view_profile" class="profile-btn">
                                    <img src="<?php echo $foto_user; ?>" alt="Foto de perfil">
                                </button>
                            </form>
                            <div>
                                <div class="username"><?php echo htmlspecialchars($dados['util']); ?></div>
                                <div class="timestamp"><?php echo $timestamp; ?></div>
                            </div>
                        </div>
                        <div class="thread-content">
                            <?php echo $mensagem; ?>
                            <?php if ($foto_post !== 'melodychat.png') { ?>
                                <div class="thread-media">
                                    <div class="media-preview">
                                        <?php if ($is_video) { ?>
                                            <video src="<?php echo $foto_post; ?>" poster="" muted></video>
                                        <?php } else { ?>
                                            <img src="<?php echo $foto_post; ?>" alt="Post image">
                                        <?php } ?>
                                        <i class="fa-solid fa-chevron-down media-toggle"></i>
                                    </div>
                                    <div class="media-full">
                                        <?php if ($is_video) { ?>
                                            <video src="<?php echo $foto_post; ?>" controls></video>
                                        <?php } else { ?>
                                            <img src="<?php echo $foto_post; ?>" alt="Post image">
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="thread-stats">
                            <span>
                                <i class="fas fa-comment comment-btn"></i>
                                <span class="comment-count"><?php echo $comment_count; ?></span> Respostas
                            </span>
                            <span>
                                <i class="fas fa-heart like-btn <?php echo $liked ? 'liked' : ''; ?>" data-post-id="<?php echo $post_id; ?>"></i>
                                <span class="like-count"><?php echo $like_count; ?></span> Likes
                            </span>
                        </div>
                        <div class="comments-section">
                            <button class="create-comment-btn">
                                <i class="fas fa-plus"></i> Adicionar Comentário
                            </button>
                            <div class="comments-list">
                                <?php
                                $comment_query = "SELECT fc.id, fc.id_user, fc.coment, fc.timestamp,
                                                m.util, m.path
                                         FROM forum_coment fc
                                         JOIN melodychat m ON fc.id_user = m.id
                                         WHERE fc.id_post = " . intval($post_id) . "
                                         ORDER BY fc.timestamp ASC";
                                $comment_result = mysqli_query($con, $comment_query);
                                if ($comment_result && mysqli_num_rows($comment_result) > 0) {
                                    while ($comment = mysqli_fetch_assoc($comment_result)) {
                                        $comment_timestamp = strftime('%d de %B, %Y às %H:%M', strtotime($comment['timestamp']));
                                        $comment_mensagem = htmlspecialchars($comment['coment']);
                                        $comment_foto_user = !empty($comment['path']) ? $comment['path'] : 'melodychat.png';
                                ?>
                                        <div class="comment">
                                            <div class="comment-header">
                                                <img src="<?php echo $comment_foto_user; ?>" alt="Foto de perfil">
                                                <div>
                                                    <div class="username"><?php echo htmlspecialchars($comment['util']); ?></div>
                                                    <div class="timestamp"><?php echo $comment_timestamp; ?></div>
                                                </div>
                                            </div>
                                            <div class="comment-content"><?php echo $comment_mensagem; ?></div>
                                        </div>
                                <?php
                                    }
                                } else {
                                    echo '<p style="color: #cccccc; font-size: 12px;">Nenhum comentário.</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p style="color: #cccccc; text-align: center;">Nenhum post encontrado.</p>';
            }
            ?>

            <div id="postModal" class="post-modal">
                <div class="post-modal-content">
                    <h3>Criar Novo Post</h3>
                    <form id="postForm" enctype="multipart/form-data">
                        <textarea rows="4" name="mensagem" placeholder="Escreve o teu post no fórum..." required></textarea>
                        <p style="color: #cccccc; font-size: 12px;">Máx 2MB para imagens ou vídeos. Comprima vídeos em <a href='https://clideo.com/pt/compress-video' target='_blank'>Clideo</a>.</p>
                        <input type="file" name="media" accept="image/jpeg,image/png,image/gif,video/mp4,video/webm,video/ogg">
                        <div class="button-group">
                            <button type="button" class="cancel-btn" id="cancelPost">Cancelar</button>
                            <button type="submit" class="submit-btn">Publicar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="commentModal" class="comment-modal">
                <div class="comment-modal-content">
                    <h3>Adicionar Comentário</h3>
                    <form id="commentForm">
                        <textarea rows="4" name="mensagem" placeholder="Escreve o teu comentário..." required></textarea>
                        <input type="hidden" name="post_id">
                        <div class="button-group">
                            <button type="button" class="cancel-btn" id="cancelComment">Cancelar</button>
                            <button type="submit" class="submit-btn">Comentar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
    $ano = date("Y");
    ?>

    <footer>
        © <?php echo $ano ?> Melody Chat. Todos os direitos reservados.
    </footer>

    <script>
        const openPostBtn = document.getElementById("openPostForm");
        const postModal = document.getElementById("postModal");
        const cancelPostBtn = document.getElementById("cancelPost");
        const postForm = document.getElementById("postForm");
        const commentModal = document.getElementById("commentModal");
        const cancelCommentBtn = document.getElementById("cancelComment");
        const commentForm = document.getElementById("commentForm");
        const fileInput = postForm.querySelector('input[name="media"]');

        // Validate file size on selection
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const isVideo = file.type.startsWith('video/');
                const maxSize = isVideo ? 40 * 1024 * 1024 : 10 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('Arquivo demasiado grande: Máx 2MB para imagens e vídeos.');
                    e.target.value = ''; // Clear the input
                }
            }
        });

        // Open post modal
        openPostBtn.addEventListener("click", () => {
            postModal.classList.add("show");
        });

        // Close post modal
        cancelPostBtn.addEventListener("click", () => {
            postModal.classList.remove("show");
            postForm.reset();
        });

        // Handle post form submission
        postForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const file = fileInput.files[0];
            if (file) {
                const isVideo = file.type.startsWith('video/');
                const maxSize = isVideo ? 40 * 1024 * 1024 : 10 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('Arquivo demasiado grande: Máx 2MB para imagens e vídeos.');
                    fileInput.value = '';
                    return;
                }
            }

            const formData = new FormData(postForm);
            fetch('ajax/post_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    postModal.classList.remove("show");
                    postForm.reset();
                    location.reload();
                } else {
                    alert(data.error || 'Erro ao publicar o post.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao publicar o post: ' + error.message);
            });
        });

        // Open comment modal
        document.querySelectorAll('.create-comment-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const postId = btn.closest('.forum-thread').getAttribute('data-post-id');
                commentForm.querySelector('input[name="post_id"]').value = postId;
                commentModal.classList.add("show");
            });
        });

        // Close comment modal
        cancelCommentBtn.addEventListener("click", () => {
            commentModal.classList.remove("show");
            commentForm.reset();
        });

        // Handle comment form submission
        commentForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const formData = new FormData(commentForm);
            const postId = formData.get('post_id');

            fetch('ajax/post_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    commentModal.classList.remove("show");
                    commentForm.reset();
                    const commentsSection = document.querySelector(`.forum-thread[data-post-id="${postId}"] .comments-section`);
                    const commentsList = commentsSection.querySelector('.comments-list');
                    const commentCountSpan = document.querySelector(`.forum-thread[data-post-id="${postId}"] .comment-count`);
                    const newComment = document.createElement('div');
                    newComment.className = 'comment';
                    newComment.innerHTML = `
                        <div class="comment-header">
                            <img src="${data.user_photo || 'melodychat.png'}" alt="Foto de perfil">
                            <div>
                                <div class="username">${data.user_name}</div>
                                <div class="timestamp">${data.timestamp}</div>
                            </div>
                        </div>
                        <div class="comment-content">${formData.get('mensagem')}</div>
                    `;
                    commentsList.appendChild(newComment);
                    commentsSection.style.display = 'block';
                    commentCountSpan.textContent = parseInt(commentCountSpan.textContent) + 1;
                    commentsList.querySelector('p')?.remove(); // Remove "Nenhum comentário" if present
                } else {
                    alert(data.error || 'Erro ao publicar o comentário.');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao publicar o comentário: ' + error.message);
            });
        });

        // Handle like button clicks
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (this.classList.contains('disabled')) return;

                const postId = this.getAttribute('data-post-id');
                const likeCountSpan = this.nextElementSibling;
                const isLiked = this.classList.contains('liked');

                this.classList.add('disabled');

                fetch('ajax/like_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `post_id=${postId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        likeCountSpan.textContent = data.like_count;
                        this.classList.toggle('liked', !isLiked);
                    } else {
                        alert(data.error || 'Erro ao processar o like.');
                    }
                    this.classList.remove('disabled');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    this.classList.remove('disabled');
                    alert('Erro ao processar o like: ' + error.message);
                });
            });
        });

        // Periodically fetch like counts
        function updateLikeCounts() {
            fetch('ajax/get_likes.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.likes.forEach(like => {
                            const likeCountSpan = document.querySelector(`.like-btn[data-post-id="${like.post_id}"] ~ .like-count`);
                            if (likeCountSpan && likeCountSpan.textContent !== like.like_count.toString()) {
                                likeCountSpan.textContent = like.like_count;
                            }
                        });
                    } else {
                        console.error('Erro ao obter likes:', data.error);
                    }
                })
                .catch(error => console.error('Erro ao atualizar likes:', error));
        }

        // Handle media toggle
        document.querySelectorAll('.media-toggle').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const fullMedia = toggle.closest('.thread-media').querySelector('.media-full');
                const isExpanded = fullMedia.classList.contains('show');
                fullMedia.classList.toggle('show', !isExpanded);
                toggle.classList.toggle('expanded', !isExpanded);
            });
        });

        // Handle comment toggle
        document.querySelectorAll('.comment-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const commentsSection = btn.closest('.forum-thread').querySelector('.comments-section');
                commentsSection.classList.toggle('show');
            });
        });

        setInterval(updateLikeCounts, 5000);
        updateLikeCounts();
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>

</html>