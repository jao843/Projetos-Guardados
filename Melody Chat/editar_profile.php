<?php
session_start();
require("header.php");

if (!isset($_SESSION['visu_perfil'])) {
    header('location:melodychat.php');
    exit();
}

if (!isset($_SESSION['email'])) {
    header('location:melodychat.php');
    exit();
}

// Obtém dados do usuário
$user_id = intval($_SESSION['visu_perfil']);
$sql = "SELECT * FROM melodychat WHERE id = $user_id";
$query = mysqli_query($con, $sql);
if (!$query || mysqli_num_rows($query) === 0) {
    echo 'Erro: Usuário não encontrado';
}
$dados = mysqli_fetch_assoc($query);

// Obtém gêneros musicais
$sql = "SELECT id, generos FROM generos_profile WHERE id_user = $user_id";
$query = mysqli_query($con, $sql);
$genres = [];
if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        $genres[] = ['id' => $row['id'], 'generos' => $row['generos']];
    }
}

if (isset($_POST['visu_perfil'])) {
    $_SESSION['visu_perfil'] = $_SESSION['id'];
    echo "<script>window.location.href='profile_user.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script defer src="plugins/fontawesome/js/all.min.js"></script>
    <link rel="icon" href="melodychat.png" type="image">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.lineicons.com/5.0/lineicons.css" rel="stylesheet" />
    <title><?php echo $dados['util'] ?></title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Roboto', sans-serif;
            background-color: #1f1f1f;
            color: white;
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

        #main {
            margin-top: 90px;
            padding: 30px;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        footer {
            background-color: #1f1f1f;
            color: #ffffff;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .dropdown {
            position: relative;
            display: inline-block;
            margin: 0 5px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #181818;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 8px;
            right: 0;
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

        .profile-container {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .profile-picture {
            width: 200px;
            height: 200px;
            border-radius: 15px;
            object-fit: cover;
            border: 2px solid #3a3a3a;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .profile-name {
            font-size: 30px;
            font-weight: bold;
            color: #ffffff;
            margin-top: 100px;
        }

        .separator {
            width: 100%;
            height: 1px;
            background-color: #3a3a3a;
            margin: 20px 0;
        }

        .info-container {
            display: flex;
            gap: 20px;
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }

        .bio-container {
            background-color: #181818;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            min-height: 200px;
            display: flex;
            flex-direction: column;
        }

        .bio-title-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .bio-title {
            font-size: 20px;
            font-weight: bold;
            color: #ffffff;
        }

        .bio-content {
            font-size: 16px;
            color: #a0a0a0;
            line-height: 1.5;
            text-align: left;
            margin: 0;
            padding: 0;
            text-indent: 0;
            white-space: normal;
            display: block;
            overflow-wrap: break-word;
            flex-grow: 1;
        }

        .bio-text {
            white-space: pre-wrap;
            text-align: left;
            margin: 0;
            padding: 0;
        }

        .bio-placeholder {
            font-size: 16px;
            color: #a0a0a0;
            font-style: italic;
        }

        .bio-container .btn-primary {
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            line-height: 1;
            border-radius: 5px;
            background-color: #007bff;
            border: none;
        }

        .bio-container .btn-primary:hover {
            background-color: #0056b3;
        }

        .bio-textarea {
            width: 100%;
            min-height: 100px;
            background-color: #2a2a2a;
            color: #ffffff;
            border: 1px solid #3a3a3a;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
            resize: vertical;
            font-family: 'Roboto', sans-serif;
        }

        .bio-textarea:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-save {
            background-color: #28a745;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin-right: 10px;
        }

        .btn-save:hover {
            background-color: #218838;
        }

        .btn-cancel {
            background-color: #dc3545;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-cancel:hover {
            background-color: #c82333;
        }

        .bio-edit-container {
            position: relative;
        }

        .bio-buttons {
            display: flex;
            justify-content: flex-start;
            margin-top: 10px;
        }

        .word-count {
            font-size: 12px;
            color: #a0a0a0;
            margin-top: 5px;
            text-align: right;
        }

        .word-count.exceeded {
            color: #dc3545;
        }

        .genres-container {
            background-color: #181818;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            /* Aumentado de 40% para equilibrar */
            min-height: 200px;
            display: flex;
            flex-direction: column;
        }

        .genres-title-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .genres-content {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            flex-grow: 1;
        }

        .genre-item {
            background-color: #2a2a2a;
            padding: 5px 20px;
            height: 35px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: #ffffff;
        }

        .genre-item .remove-genre {
            cursor: pointer;
            color: #dc3545;
        }

        .genre-item .remove-genre:hover {
            color: #c82333;
        }

        .genres-placeholder {
            font-size: 16px;
            color: #a0a0a0;
            font-style: italic;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #2a2a2a;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        .modal-content h3 {
            margin: 0 0 15px;
            font-size: 18px;
            color: #ffffff;
        }

        .modal-content input[type="text"] {
            width: 100%;
            padding: 10px;
            background-color: #3a3a3a;
            border: 1px solid #3a3a3a;
            border-radius: 5px;
            color: #ffffff;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .modal-content input[type="text"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .modal-content .modal-buttons {
            display: flex;
            justify-content: flex-start;
            gap: 10px;
        }

        .function_tiny {
            position: relative;
            background-color: #0c547d;
            border: none;
            color: white;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            text-align: left;
            width: 100%;
            max-width: 200px;
            height: 50px;
        }

        .function_tiny:hover {
            background-color: #07324a;
        }

        .function_tiny input[type="submit"] {
            flex: 1;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: transparent;
            color: white;
            cursor: pointer;
            text-align: center;
            margin: 0;
        }

        .function_tiny i {
            margin-right: 5px;
            font-size: 18px;
            color: white;
        }

        @media (max-width: 768px) {
            .info-container {
                flex-direction: column;
            }

            .bio-container,
            .genres-container {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div id="main">
        <div class="profile-container">
            <img src="<?php echo !empty($dados['path']) ? $dados['path'] : 'melodychat.png'; ?>" alt="Foto de Perfil" class="profile-picture">
            <div class="profile-info">
                <div class="profile-name"><?php echo htmlspecialchars($dados['util']); ?></div>
                <form method="post" class="function_tiny">
                    <i class="fa-solid fa-user"></i>
                    <input type="submit" name="visu_perfil" value="Visualizar Perfil">
                </form>
            </div>
        </div>
        <div class="separator"></div>
        <div class="info-container">
            <div class="bio-container">
                <div class="bio-title-container">
                    <span class="bio-title">Biografia</span>
                    <button class="btn btn-primary" id="edit-bio-btn"><i class="fa-solid fa-pen"></i></button>
                </div>
                <div class="bio-content" id="bio-content">
                    <?php
                    if (!empty($dados['bio'])) {
                        $bio = preg_replace('/^[\s\x{00A0}\x{200B}\x{200E}\x{200F}\x{FEFF}]+/u', '', $dados['bio']);
                        echo '<div class="bio-text">' . htmlspecialchars($bio) . '</div>';
                    } else {
                        echo '<span class="bio-placeholder">🕸🕸🕷🕸🕸 (Empty)</span>';
                    }
                    ?>
                </div>
            </div>
            <div class="genres-container">
                <div class="genres-title-container">
                    <span class="bio-title">Gêneros Musicais</span>
                    <button class="btn btn-primary" id="add-genre-btn"><i class="fa-solid fa-plus"></i></button>
                </div>
                <div class="genres-content" id="genres-content">
                    <?php
                    if (!empty($genres)) {
                        foreach ($genres as $genre) {
                            echo '<div class="genre-item" data-id="' . $genre['id'] . '">' .
                                htmlspecialchars($genre['generos']) .
                                '<i class="fas fa-times remove-genre" data-id="' . $genre['id'] . '"></i></div>';
                        }
                    } else {
                        echo '<span class="genres-placeholder">Nenhum gênero musical adicionado</span>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="modal-overlay" id="genre-modal">
            <div class="modal-content">
                <h3>Adicionar Gênero Musical</h3>
                <input type="text" id="genre-input" placeholder="Digite o gênero musical" maxlength="50">
                <div class="modal-buttons">
                    <button class="btn-save" id="save-genre-btn">Adicionar</button>
                    <button class="btn-cancel" id="cancel-genre-btn">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <br><br><br>
    <?php
    $ano = date("Y");
    ?>
    <footer>
        © <?php echo $ano ?> Melody Chat. Todos os direitos reservados.
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Biografia
            const editBioBtn = $('#edit-bio-btn');
            const bioContent = $('#bio-content');
            let originalBio = bioContent.find('.bio-text').length ? bioContent.find('.bio-text').text() : '';
            const maxWords = 300;

            function countWords(text) {
                return text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
            }

            editBioBtn.on('click', function() {
                bioContent.html(`
                    <div class="bio-edit-container">
                        <textarea class="bio-textarea" id="bio-textarea">${originalBio}</textarea>
                        <div class="word-count" id="word-count">${countWords(originalBio)}/${maxWords} palavras</div>
                        <div class="bio-buttons">
                            <button class="btn-save" id="save-bio-btn">Guardar</button>
                            <button class="btn-cancel" id="cancel-bio-btn">Cancelar</button>
                        </div>
                    </div>
                `);

                const textarea = $('#bio-textarea');
                textarea.focus();

                textarea.on('input', function() {
                    const text = $(this).val();
                    const wordCount = countWords(text);
                    const wordCountElement = $('#word-count');
                    wordCountElement.text(`${wordCount}/${maxWords} palavras`);
                    if (wordCount > maxWords) {
                        wordCountElement.addClass('exceeded');
                    } else {
                        wordCountElement.removeClass('exceeded');
                    }
                });

                $('#save-bio-btn').on('click', function() {
                    const newBio = textarea.val();
                    const wordCount = countWords(newBio);

                    if (wordCount > maxWords) {
                        alert(`A biografia não pode exceder ${maxWords} palavras. Atualmente: ${wordCount} palavras.`);
                        return;
                    }

                    $.ajax({
                        url: 'ajax/update_bio.php',
                        type: 'POST',
                        data: {
                            bio: newBio,
                            user_id: <?php echo $_SESSION['visu_perfil']; ?>
                        },
                        success: function(response) {
                            if (response === 'success') {
                                originalBio = newBio;
                                bioContent.html(newBio.trim() !== '' ?
                                    `<div class="bio-text">${$('<div>').text(newBio).html()}</div>` :
                                    '<span class="bio-placeholder">🕸🕸🕷🕸🕸 (Empty)</span>');
                            } else {
                                alert('Erro ao atualizar a biografia: ' + response);
                            }
                        },
                        error: function() {
                            alert('Erro ao conectar com o servidor.');
                        }
                    });
                });

                $('#cancel-bio-btn').on('click', function() {
                    bioContent.html(originalBio.trim() !== '' ?
                        `<div class="bio-text">${$('<div>').text(originalBio).html()}</div>` :
                        '<span class="bio-placeholder">🕸🕸🕷🕸🕸 (Empty)</span>');
                });
            });

            // Gêneros Musicais
            const addGenreBtn = $('#add-genre-btn');
            const genreModal = $('#genre-modal');
            const genresContent = $('#genres-content');
            const maxGenres = 10;

            addGenreBtn.on('click', function() {
                if (genresContent.children('.genre-item').length >= maxGenres) {
                    alert(`Você já atingiu o limite de ${maxGenres} gêneros musicais.`);
                    return;
                }
                genreModal.css('display', 'flex');
                $('#genre-input').focus();
            });

            $('#save-genre-btn').on('click', function() {
                const genre = $('#genre-input').val().trim();
                if (genre === '') {
                    alert('Por favor, insira um gênero musical.');
                    return;
                }

                $.ajax({
                    url: 'ajax/manage_genres.php',
                    type: 'POST',
                    data: {
                        action: 'add',
                        genre: genre,
                        user_id: <?php echo $_SESSION['visu_perfil']; ?>
                    },
                    success: function(response) {
                        if (response.startsWith('success')) {
                            const id = parseInt(response.split(':')[1]);
                            genresContent.find('.genres-placeholder').remove();
                            genresContent.append(`
                                <div class="genre-item" data-id="${id}">
                                    ${$('<div>').text(genre).html()}
                                    <i class="fas fa-times remove-genre" data-id="${id}"></i>
                                </div>
                            `);
                            $('#genre-input').val('');
                            genreModal.css('display', 'none');
                        } else {
                            alert('Erro ao adicionar gênero: ' + response);
                        }
                    },
                    error: function() {
                        alert('Erro ao conectar com o servidor.');
                    }
                });
            });

            $('#cancel-genre-btn').on('click', function() {
                $('#genre-input').val('');
                genreModal.css('display', 'none');
            });

            genresContent.on('click', '.remove-genre', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: 'ajax/manage_genres.php',
                    type: 'POST',
                    data: {
                        action: 'remove',
                        id: id,
                        user_id: <?php echo $_SESSION['visu_perfil']; ?>
                    },
                    success: function(response) {
                        if (response === 'success') {
                            $(`div[data-id="${id}"]`).remove();
                            if (genresContent.children('.genre-item').length === 0) {
                                genresContent.html('<span class="genres-placeholder">Nenhum gênero musical adicionado</span>');
                            }
                        } else {
                            alert('Erro ao remover gênero: ' + response);
                        }
                    },
                    error: function() {
                        alert('Erro ao conectar com o servidor.');
                    }
                });
            });
        });
    </script>
</body>

</html>