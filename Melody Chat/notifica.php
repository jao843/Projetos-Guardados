<?php
if (isset($_SESSION['email'])) {
    // Consulta SQL para contar apenas as notificações não lidas (para a bola vermelha)
    $contador_nao_lidas_sql = "SELECT COUNT(*) as total_nao_lidas FROM notifications WHERE email_user = '" . mysqli_real_escape_string($con, $_SESSION['email']) . "' AND is_read = 0";
    $contador_nao_lidas_result = mysqli_query($con, $contador_nao_lidas_sql);
    $contador_nao_lidas = mysqli_fetch_assoc($contador_nao_lidas_result)['total_nao_lidas'];

    // Consulta SQL para obter todas as notificações (lidas e não lidas) para o dropdown, ordenadas por data decrescente
    $notificacoes_sql = "SELECT * FROM notifications WHERE email_user = '" . mysqli_real_escape_string($con, $_SESSION['email']) . "' ORDER BY data DESC LIMIT 5";
    $notificacoes_result = mysqli_query($con, $notificacoes_sql);
?>

    <div class="notification-icon" style="position: relative; display: inline-block;">
        <a href="#" style="text-decoration: none;" onclick="toggleNotifications()">
            <i class="fas fa-bell" style="font-size: 24px; color: white;"></i>
            <?php if ($contador_nao_lidas > 0) { ?>
                <span style="position: absolute; top: 10px; right: -5px; background: red; color: white; border-radius: 50%; width: 15px; height: 15px; font-size: 10px; text-align: center; line-height: 15px;"><?php echo $contador_nao_lidas; ?></span>
            <?php } ?>
        </a>
        <div id="notificationDropdown" class="dropdown-content" style="display: none; position: absolute; left: -300px; top: 40px; background: #181818; min-width: 300px; max-height: 400px; overflow-y: auto; box-shadow: 0 8px 16px rgba(0,0,0,0.2); z-index: 1000; border-radius: 8px; padding: 10px;">
            <div style="border-bottom: 1px solid #3a3a3a; padding-bottom: 10px; text-align: center;">
                <h5 style="color: white; margin: 0; padding-bottom: 10px;">Notificações</h5>
                <?php if ($contador_nao_lidas > 0) { ?>
                    <form method="post" style="margin-top: 10px; display: flex; justify-content: center;">
                        <input type="submit" name="marcar_tudo_lido" value="Todas Vistas" style="background: #871e1e; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                    </form>
                <?php } ?>
            </div>
            <?php while ($notificacao = mysqli_fetch_assoc($notificacoes_result)) {
                $link = $notificacao['link'];
                $email_causa = $notificacao['email_user_causa'];
                $email_causa_escaped = mysqli_real_escape_string($con, $email_causa);
                $nome_causa_sql = "SELECT util FROM melodychat WHERE email = '$email_causa_escaped'";
                $nome_causa_result = mysqli_query($con, $nome_causa_sql);
                $nome_causa = mysqli_fetch_assoc($nome_causa_result);

                $texto_formatado = $notificacao['texto'];
            ?>
                <div style="color: white; padding: 10px; border-bottom: 1px solid #3a3a3a; position: relative;" data-notif-id="<?php echo $notificacao['id']; ?>" data-friend-email="<?php echo htmlspecialchars($email_causa); ?>" data-link="<?php echo htmlspecialchars($notificacao['link']); ?>">
                    <form method="post" style="position: absolute; top: 10px; right: 10px;">
                        <input type="hidden" name="id_ntf_delete" value="<?php echo $notificacao['id']; ?>">
                        <button type="submit" name="deletar_notificacao" style="background: transparent; border: none; color: #ff4444; font-size: 16px; cursor: pointer;" title="Apagar notificação">×</button>
                    </form>
                    <p><strong><?php echo htmlspecialchars($notificacao['titulo']); ?></strong></p>
                    <p><?php echo htmlspecialchars($texto_formatado); ?></p>
                    <?php if (isset($notificacao['link']) && !empty($notificacao['link'])) { ?>
                        <form method="post">
                            <input type="submit" name="ver_mais" style="color: #007bff; text-decoration: none;" value="Ver Mais">
                            <input type="hidden" name="id_ntf" value="<?php echo $notificacao['id']; ?>">
                            <input type="hidden" name="link" value="<?php echo htmlspecialchars($link); ?>">
                        </form>
                    <?php } ?>
                    <small style="color: #a0a0a0;"><?php echo date('d/m/Y H:i', strtotime($notificacao['data'])); ?></small>
                    <?php if ($notificacao['is_read'] == 0) { ?>
                        <span style="color: red; font-size: 12px;">(Não lida)</span>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        function toggleNotifications() {
            const dropdown = document.getElementById("notificationDropdown");
            dropdown.style.display = dropdown.style.display === "none" ? "block" : "none";
        }

        document.addEventListener("click", function(event) {
            const dropdown = document.getElementById("notificationDropdown");
            const icon = document.querySelector(".notification-icon");
            if (!icon.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });
    </script>

    <?php
    if (isset($_POST['ver_mais'])) {
        // Mark notification as read
        $id_ntf = (int)$_POST['id_ntf'];
        $update_sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND email_user = ?";
        $stmt = $con->prepare($update_sql);
        $stmt->bind_param("is", $id_ntf, $_SESSION['email']);
        $stmt->execute();
        $stmt->close();

        // Handle link
        $link = $_POST['link'];
        if ($link === 'chats.php') {
            // Direct message: set friend session variables
            $email_causa = mysqli_real_escape_string($con, $notificacao['email_user_causa']);
            if ($email_causa !== $_SESSION['email']) {
                $sql = "SELECT util, path, bio FROM melodychat WHERE email = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("s", $email_causa);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($dados = $result->fetch_assoc()) {
                    $_SESSION['email_friend'] = $email_causa;
                    $_SESSION['foto'] = $dados['path'];
                    $_SESSION['bio_amigo'] = $dados['bio'];
                    $_SESSION['amigo_nome'] = $dados['util'];
                }
                $stmt->close();
            }
        } elseif (preg_match('/^server\.php\?id=(\d+)$/', $link, $matches)) {
            // Group message: set group_id session variable
            $group_id = (int)$matches[1];
            // Verify group_id exists
            $sql = "SELECT id FROM servers WHERE id = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("i", $group_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $_SESSION['group_id'] = $group_id;
            } else {
                // Invalid group_id; redirect to error page
                echo "<script>window.location.href='error.php?msg=Grupo inválido';</script>";
                exit();
            }
            $stmt->close();
        }

        // Redirect to link
        echo "<script>window.location.href='" . htmlspecialchars($link) . "';</script>";
        exit();
    }

    if (isset($_POST['marcar_tudo_lido'])) {
        $update_all_sql = "UPDATE notifications SET is_read = 1 WHERE email_user = ? AND is_read = 0";
        $stmt = $con->prepare($update_all_sql);
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();
        $stmt->close();
        echo "<script>window.location.href='" . htmlspecialchars($_SERVER['PHP_SELF']) . "';</script>";
        exit();
    }

    if (isset($_POST['deletar_notificacao'])) {
        $id_ntf_delete = (int)$_POST['id_ntf_delete'];
        $delete_sql = "DELETE FROM notifications WHERE id = ? AND email_user = ?";
        $stmt = $con->prepare($delete_sql);
        $stmt->bind_param("is", $id_ntf_delete, $_SESSION['email']);
        $stmt->execute();
        $stmt->close();
        echo "<script>window.location.href='" . htmlspecialchars($_SERVER['PHP_SELF']) . "';</script>";
        exit();
    }
} else {
    ?>
    <a href="#" style="text-decoration: none; margin-left: 10px;">
        <i class="fas fa-bell" style="font-size: 24px; color: white;"></i>
    </a>
<?php
}
?>