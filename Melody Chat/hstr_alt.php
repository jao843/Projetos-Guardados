<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

if (!isset($_SESSION['email'])) {
    header('location:melodychat.php');
    exit();
} elseif (isset($_SESSION['email'])) {
    $sql = "Select email FROM melodychat WHERE cargo=TRUE and email= '" . $_SESSION['email'] . "';";
    $resultado = mysqli_query($con, $sql);
    $num_cargo = mysqli_num_rows($resultado);
    if ($num_cargo != 0) {

        if (isset($_POST['chats'])) {
            header('location:chats.php');
            exit();
        }

        if (isset($_POST['friends'])) {
            header('location:comunidade.php');
            exit();
        }

        if (isset($_POST['group'])) {
            header('location:server.php');
            exit();
        }

        if (isset($_POST['logout'])) {
            header('location:dsession_melodychat.php');
            exit();
        }

        if (isset($_POST['editar'])) {
            header('location:editar.php');
            exit();
        }

        if (isset($_POST['admin'])) {
            header('location:admin.php');
            exit();
        }

        if (isset($_POST['profile'])) {
            header('location:profile.php');
            exit();
        }

        if (isset($_POST['hstr_alt'])) {
            header('location:hstr_alt.php');
            exit();
        }
?>
        <!DOCTYPE html>
        <html lang="pt-br">

        <head>
            <!-- Meta tags Obrigatórias -->
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

            <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
            <link href="https://cdn.lineicons.com/5.0/lineicons.css" rel="stylesheet" />
            <link rel="icon" href="transparente.png" type="image/png">
            <script defer src="plugins/fontawesome/js/all.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <title>Melody Chat</title>

            <style>
                body {
                    margin: 0;
                    padding: 0;
                    height: 100vh;
                    font-family: 'Arial', sans-serif;
                    /* Fonte opcional */
                }

                /* Navbar fixo no topo */
                .navbar-top {
                    width: 100%;
                    height: 70px;
                    background-color: #181818;
                    /* Cor da barra */
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
                    /* Cor mais clara ao passar o mouse */
                }

                /* Ajuste do conteúdo principal */
                #main {
                    margin-top: 90px;
                    /* Para compensar a altura da navbar */
                    padding: 20px;
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
                    /* Move o menu para o lado esquerdo */
                    top: 40px;
                    /* Ajusta para não sobrepor o botão */
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

                /* Exibir dropdown ao passar o mouse no botão */
                .dropdown:hover .dropdown-content {
                    display: block;
                }


                .navbar-top {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 0 20px;
                    background-color: #181818;
                    height: 70px;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
                    z-index: 1000;
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

                .dropdown {
                    margin-left: auto;
                }

                /* Navbar fixo no topo */
                .navbar-top {
                    width: 100%;
                    height: 70px;
                    background-color: #181818;
                    /* Cor da barra */
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
                    /* Cor mais clara ao passar o mouse */
                }

                /* Ajuste do conteúdo principal */
                #main {
                    margin-top: 90px;
                    /* Para compensar a altura da navbar */
                    padding: 20px;
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
                    /* Small margin for balance */
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
                    /* Changed from left: -120px */
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

                /* Exibir dropdown ao passar o mouse no botão */
                .dropdown:hover .dropdown-content {
                    display: block;
                }


                .navbar-top {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 0 20px;
                    background-color: #181818;
                    height: 70px;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
                    z-index: 1000;
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

                .title {
                    position: absolute;
                    top: 50px;
                    left: 50%;
                    transform: translateX(-50%);
                    font-size: 30px;
                    font-weight: bold;
                    color: white;
                    text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
                    margin-top: 20px;
                }

                .background-img {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100vh;
                    object-fit: cover;
                    z-index: -1;
                    filter: blur(8px);
                }

                .subtitle {
                    position: absolute;
                    top: 150px;
                    left: 50%;
                    transform: translateX(-50%);
                    font-size: 30px;
                    font-weight: normal;
                    color: #d3d3d3;
                    text-align: center;
                    margin-top: 50px;
                }

                .text-box {
                    background-color: #1f1f1f;
                    padding: 20px;
                    border-radius: 10px;
                    width: 80%;
                    margin: 20px auto;
                    font-size: 16px;
                    color: white;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                .admin-container {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    padding: 20px;
                    width: 90%;
                    margin: 0 auto;
                }

                .admin-card {
                    background-color: #fff;
                    width: 100%;
                    max-width: none;
                    padding: 30px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                }

                .admin-card h2 {
                    font-size: 20px;
                    color: #2c3e50;
                    margin-bottom: 20px;
                    text-align: center;
                }

                .form-group {
                    margin-bottom: 15px;
                }

                label {
                    font-size: 14px;
                    color: #555;
                    display: block;
                    margin-bottom: 5px;
                }

                input[type="text"] {
                    width: 100%;
                    padding: 12px;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                    font-size: 14px;
                }

                input[type="checkbox"] {
                    margin-right: 8px;
                }

                .submit-button {
                    width: 20%;
                    padding: 10px;
                    font-size: 14px;
                    background-color: #3498db;
                    color: white;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    transition: background-color 0.3s;
                    display: block;
                    margin: 10px auto 0;
                    text-align: center;
                }

                .submit-button:hover {
                    background-color: #2980b9;
                }

                .back-button {
                    background-color: #007bff;
                    color: white;
                    padding: 10px 20px;
                    font-size: 16px;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    transition: background-color 0.3s ease, transform 0.2s ease;
                }

                .back-button:hover {
                    background-color: #0056b3;
                    transform: scale(1.05);
                }

                .back-button:active {
                    background-color: #003d80;
                    transform: scale(0.95);
                }

                .back-button::before {
                    content: '←';
                    margin-right: 8px;
                    font-weight: bold;
                }

                .card {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 10px;
                    background-color: #f5f5f5;
                    border: 1px solid #ccc;
                    border-radius: 8px;
                    max-width: 600px;
                    margin: 10px auto;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                .user-row {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    width: 100%;
                    justify-content: space-between;
                }

                .user-email {
                    font-size: 10px;
                    color: #333;
                    flex: 2;
                }

                .separator {
                    font-size: 14px;
                    color: #888;
                }

                .button {
                    padding: 8px 12px;
                    border: none;
                    border-radius: 4px;
                    font-size: 12px;
                    cursor: pointer;
                    transition: background-color 0.2s ease;
                }

                .button-block {
                    background-color: #dc3545;
                    color: #fff;
                }

                .button-block:hover {
                    background-color: #c82333;
                }

                .button-admin {
                    background-color: #007bff;
                    color: #fff;
                }

                .button-promote {
                    background-color: #28a745;
                    color: #fff;
                }

                .button-promote:hover {
                    background-color: #218838;
                }

                .button[disabled] {
                    background-color: #ddd;
                    color: #666;
                    cursor: default;
                }

                .table-container {
                    margin-top: 20px;
                    overflow-x: auto;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    background-color: #fff;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                th,
                td {
                    padding: 12px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                    color: #333;
                    font-size: 14px;
                }

                th {
                    background-color: #3498db;
                    color: white;
                    font-weight: bold;
                }

                tr:nth-child(even) {
                    background-color: #f9f9f9;
                }

                tr:hover {
                    background-color: #f5f5f5;
                }

                /* Estilo para o campo de busca */
                .search-container {
                    margin-bottom: 20px;
                    display: flex;
                    justify-content: center;
                }

                .search-container input[type="text"] {
                    width: 50%;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                    font-size: 14px;
                }

                .search-container input[type="submit"] {
                    padding: 10px 20px;
                    margin-left: 10px;
                    background-color: #3498db;
                    color: white;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 14px;
                }

                .search-container input[type="submit"]:hover {
                    background-color: #2980b9;
                }
            </style>
        </head>

        <body>

            <!-- Barra de Navegação Fixa no Topo -->
            <?php
            require('header.php');
            ?>

            <div id="main">
                <img src="drum.jpg" alt="Imagem de fundo" class="background-img">
                <br><br><br>

                <div class="admin-container">
                    <div class="admin-card">
                        <div class="title">
                            Histórico de Alterações
                        </div>

                        <!-- Campo de busca -->
                        <div class="search-container">
                            <form method="post">
                                <input type="text" name="search_term" placeholder="Procurar..." value="<?php echo isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : ''; ?>">
                                <input type="submit" value="Buscar">
                            </form>
                        </div>

                        <div class="table-container">
                            <table>
                                <thead>
                                    <?php
                                    // Define a consulta SQL
                                    $consulta_sql = "SELECT * FROM alt_administracao";

                                    if (isset($_POST['search_term']) && !empty($_POST['search_term'])) {
                                        $search_term = mysqli_real_escape_string($con, $_POST['search_term']);
                                        // Ajusta a consulta para buscar nos campos exibidos (índices 2, 4, 5, etc.)
                                        $consulta_sql .= " WHERE ";
                                        $field_index = 0;
                                        $first_condition = true;
                                        $original_keys = mysqli_fetch_fields(mysqli_query($con, "SELECT * FROM alt_administracao"));
                                        foreach ($original_keys as $field) {
                                            if ($field_index != 0 && $field_index != 1 && $field_index != 3) {
                                                if (!$first_condition) {
                                                    $consulta_sql .= " OR ";
                                                }
                                                $consulta_sql .= "`" . $field->name . "` LIKE '%$search_term%'";
                                                $first_condition = false;
                                            }
                                            $field_index++;
                                        }
                                    }

                                    // Adiciona a ordenação por data (da mais recente para a mais antiga)
                                    $consulta_sql .= " ORDER BY data DESC";

                                    $consulta_query = mysqli_query($con, $consulta_sql);

                                    if (mysqli_num_rows($consulta_query) > 0) {
                                        // Pega os nomes das colunas originais
                                        $first_row = mysqli_fetch_assoc($consulta_query);
                                        $original_keys = array_keys($first_row);

                                        // Define os novos nomes dos cabeçalhos
                                        $new_headers = ["Administrador", "Utilizador", "Ação", "Data"];

                                        echo "<tr>";
                                        $header_index = 0;
                                        $field_index = 0;
                                        foreach ($original_keys as $key) {
                                            if ($field_index != 0 && $field_index != 1 && $field_index != 3 && $field_index != 6) {
                                                if ($header_index < count($new_headers)) {
                                                    echo "<th>" . htmlspecialchars($new_headers[$header_index]) . "</th>";
                                                    $header_index++;
                                                }
                                            }
                                            $field_index++;
                                        }
                                        echo "</tr></thead><tbody>";

                                        // Volta o ponteiro para o início
                                        mysqli_data_seek($consulta_query, 0);

                                        // Exibe os dados
                                        while ($row = mysqli_fetch_assoc($consulta_query)) {
                                            echo "<tr>";
                                            $field_index = 0;
                                            foreach ($row as $value) {
                                                if ($field_index != 0 && $field_index != 1 && $field_index != 3 && $field_index != 6) {
                                                    echo "<td>" . htmlspecialchars($value) . "</td>";
                                                }
                                                $field_index++;
                                            }
                                            echo "</tr>";
                                        }
                                        echo "</tbody>";
                                    } else {
                                        echo "<tr><td colspan='5'>Nenhum registro encontrado.</td></tr></tbody>";
                                    }
                                    ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer>
                © 2025 Melody Chat. Todos os direitos reservados.
            </footer>

    <?php
    } else {
        header('location:melodychat.php');
        exit();
    }
} else {
    header('location:melodychat.php');
    exit();
}
    ?>

    <!-- JavaScript (Opcional) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        </body>

        </html>