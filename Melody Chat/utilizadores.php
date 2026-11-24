<?php
session_start(); // PARA PODER USAR VARIÁVEIS DE SESSÃO
require('ligacao_melody.php');

if (!isset($_SESSION['email'])) {
    header('location:melodychat.php');
    exit();
} elseif (isset($_SESSION['email'])) {
    $sql = "SELECT email FROM melodychat WHERE cargo=TRUE AND email='" . $_SESSION['email'] . "';";
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
    <title>Melody Chat - Utilizadores</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            font-family: 'Arial', sans-serif;
        }

        /* Navbar fixo no topo */
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

        /* Ajuste do conteúdo principal */
        #main {
            margin-top: 90px;
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

        th, td {
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

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Barra de Navegação Fixa no Topo -->
    <?php require('header.php'); ?>

    <div id="main">
        <img src="drum.jpg" alt="Imagem de fundo" class="background-img">
        <br><br><br>

        <div class="admin-container">
            <div class="admin-card">
                <div class="title">
                    Lista de Utilizadores
                </div>

                <!-- Campo de busca -->
                <div class="search-container">
                    <form method="post">
                        <input type="text" name="searched_term" placeholder="Procurar..." value="<?php echo isset($_POST['searched_term']) ? htmlspecialchars($_POST['searched_term']) : ''; ?>">
                        <input type="submit" value="Buscar">
                    </form>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <?php
                            // Define a consulta SQL
                            $consulta_sql = "SELECT id,util, email, cargo, estado, login, login_time, path FROM melodychat";

                            if (isset($_POST['searched_term']) && !empty($_POST['searched_term'])) {
                                $search_term = mysqli_real_escape_string($con, $_POST['searched_term']);
                                $consulta_sql .= " WHERE ";
                                $fields = mysqli_fetch_fields(mysqli_query($con, "SELECT id,util, email, cargo, estado, login, login_time, path FROM melodychat"));
                                $first_condition = true;
                                foreach ($fields as $field) {
                                    if (!$first_condition) {
                                        $consulta_sql .= " OR ";
                                    }
                                    $consulta_sql .= "`" . $field->name . "` LIKE '%$search_term%'";
                                    $first_condition = false;
                                }
                            }

                            $consulta_query = mysqli_query($con, $consulta_sql);

                            // Define os novos nomes dos cabeçalhos
                            $new_headers = ["ID","Nome", "Email", "Cargo", "Estado", "Login", "Ultimo Login", "Foto de Perfil"];

                            if (mysqli_num_rows($consulta_query) > 0) {
                                // Exibe os cabeçalhos
                                echo "<tr>";
                                foreach ($new_headers as $header) {
                                    echo "<th>" . htmlspecialchars($header) . "</th>";
                                }
                                echo "</tr></thead><tbody>";

                                // Exibe os dados
                                while ($row = mysqli_fetch_assoc($consulta_query)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['util']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td>" . ($row['cargo'] == 1 ? 'Administrador' : 'Utilizador') . "</td>";
                                    echo "<td>" . ($row['estado'] == 1 ? 'Bloqueado' : 'Ativo') . "</td>";
                                    echo "<td>" . ($row['login'] == 1 ? 'Online' : 'Offline') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['login_time']) . "</td>";
                                    echo "<td>";
                                    if (!empty($row['path'])) {
                                        echo "<img src='" . htmlspecialchars($row['path']) . "' alt='Foto de Perfil' class='profile-img'>";
                                    } else {
                                        echo "Sem foto";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                            } else {
                                // Exibe cabeçalhos mesmo se não houver resultados
                                echo "<tr>";
                                foreach ($new_headers as $header) {
                                    echo "<th>" . htmlspecialchars($header) . "</th>";
                                }
                                echo "</tr></thead><tbody>";
                                echo "<tr><td colspan='" . count($new_headers) . "'>Nenhum utilizador encontrado.</td></tr></tbody>";
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