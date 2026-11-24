<?php
require('ligacao_melody.php');

if (isset($_POST['chats'])) {
    echo "<script>window.location.href='chats.php';</script>";
}

if (isset($_POST['friends'])) {
    echo "<script>window.location.href='friends.php';</script>";
}

if (isset($_POST['group'])) {
    echo "<script>window.location.href='server.php';</script>";
    exit();
}

if (isset($_POST['logout'])) {
    echo "<script>window.location.href='dsession_melodychat.php';</script>";
}

if (isset($_POST['admin'])) {
    echo "<script>window.location.href='admin.php';</script>";
}

if (isset($_POST['profile'])) {
    echo "<script>window.location.href='profile.php';</script>";
}

if (isset($_POST['contact'])) {
    echo "<script>window.location.href='contact.php';</script>";
}

$update_offline_sql = "UPDATE melodychat SET login = 0 WHERE login_time < DATE_SUB(NOW(), INTERVAL 1 HOUR)";
$update_offline_result = mysqli_query($con, $update_offline_sql);

$est = "SELECT estado FROM manutencao WHERE id = 1;";
$estado = mysqli_query($con, $est);
$row = mysqli_fetch_assoc($estado);

if ($row['estado'] == FALSE and isset($_SESSION['email'])) {
    $sql = "SELECT * from melodychat where email = '" . $_SESSION['email'] . "'";
    $sql_cargo = mysqli_query($con, $sql);
    $cargo_email = mysqli_fetch_assoc($sql_cargo);

    if ($cargo_email['cargo'] == FALSE) {
        header('location:dsession_melodychat.php');
        exit();
    }
}

if (isset($_SESSION['email'])) {
    $sql12 = "select * from melodychat where email = '" . $_SESSION['email'] . "';";
    $result3 = mysqli_query($con, $sql12);
    $rowe = mysqli_fetch_assoc($result3);
}

if (isset($_POST['contact'])) {
    header('location:contact.php');
    exit();
}

if (isset($_POST['start'])) {
    header('location:login.php');
    exit();
}

if (isset($_SESSION['email'])) {
    $termos = "SELECT * from direitos where email = '" . $_SESSION['email'] . "'";
    $direitos = mysqli_query($con, $termos);

    if ($verificar_termos = mysqli_num_rows($direitos) == 0) {
        header('location:termos.php');
        exit();
    }
}
?>

<head>
    <link rel="icon" href="transparente.png" type="image/png">
</head>
<style>
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

    .loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2000;
        transition: opacity 0.5s ease;
    }

    .loader-overlay.hidden {
        opacity: 0;
        pointer-events: none;
    }

    .loader {
        width: 50px;
        height: 50px;
        border: 5px solid #ffffff;
        border-top: 5px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<body>
    <div class="loader-overlay" id="loaderOverlay">
        <div class="loader"></div>
    </div>
    <div class="navbar-top">
        <div class="left-section">
            <img src="https://imageshack.com/i/pmmxUi4hp" alt="Logo" class="logo">
            <?php
            if (isset($_SESSION['email'])) {
                if ($row && $row['estado'] == 0) {
                    if ($rowe['cargo'] == 1) {
            ?>
                        <form method="post" class="navbar-options">
                            <input type="submit" name="chats" value="Chat">
                            <input type="submit" name="group" value="Grupos">
                        </form>
        </div>
        <form method="post">
            <span align="center" class="btn btn-outline-danger">SITE EM MANUTENÇÃO</span>
            <a href="admin.php" class="btn btn-outline-warning"><i class='lni lni-gears-3' style="margin-right: 5px;"></i>Ferramentas de administração</a>
            <input type="submit" name="contact" value="Contacte-nos">
            <input type="submit" name="profile" value="Perfil">
            <?php require('notifica.php'); ?>
            <div class="dropdown">
                <input type="button" name="def" value="☰">
                <div class="dropdown-content">
                    <a href="melodychat.php"><i class="lni lni-home-2" style="margin-right: 5px;"></i>Inicio</a>
                    <a href="dsession_melodychat.php" style="color: red;"><i class="lni lni-exit" style="margin-right: 5px;"></i>Terminar</a>
                </div>
            </div>
        </form>
    <?php
                    }
                } else {
                    if ($rowe['cargo'] == 1) {
    ?>
                        <form method="post" class="navbar-options">
                            <input type="submit" name="chats" value="Chat">
                            <input type="submit" name="group" value="Grupos">
                        </form>
        </div>
        <form method="post">
            <a href="admin.php" class="btn btn-outline-warning"><i class='lni lni-gears-3' style="margin-right: 5px;"></i>Ferramentas de administração</a>
            <input type="submit" name="contact" value="Contacte-nos">
            <input type="submit" name="profile" value="Perfil">
            <?php require('notifica.php'); ?>
            <div class="dropdown">
                <input type="button" name="def" value="☰">
                <div class="dropdown-content">
                    <a href="melodychat.php"><i class="lni lni-home-2" style="margin-right: 5px;"></i>Inicio</a>
                    <a href="dsession_melodychat.php" style="color: red;"><i class="lni lni-exit" style="margin-right: 5px;"></i>Terminar</a>
                </div>
            </div>
        </form>
    <?php
                    } else {
    ?>
                        <form method="post" class="navbar-options">
                            <input type="submit" name="chats" value="Chat">
                            <input type="submit" name="group" value="Grupos">
                        </form>
        </div>
        <form method="post">
            <input type="submit" name="contact" value="Contacte-nos">
            <input type="submit" name="profile" value="Perfil">
            <?php require('notifica.php'); ?>
            <div class="dropdown">
                <input type="button" name="def" value="☰">
                <div class="dropdown-content">
                    <a href="melodychat.php"><i class="lni lni-home-2" style="margin-right: 5px;"></i>Inicio</a>
                    <a href="dsession_melodychat.php" style="color: red;"><i class="lni lni-exit" style="margin-right: 5px;"></i>Terminar</a>
                </div>
            </div>
        </form>
    <?php
                    }
                }
            }
    ?>
    </div>

    <script>
        // Hide loader when page is fully loaded
        window.addEventListener('load', () => {
            const loaderOverlay = document.getElementById('loaderOverlay');
            loaderOverlay.classList.add('hidden');
            // Remove loader from DOM after transition
            setTimeout(() => {
                loaderOverlay.style.display = 'none';
            }, 500); // Match transition duration
        });
    </script>
</body>