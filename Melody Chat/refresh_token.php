<?php
session_start();
require('ligacao_melody.php');

// Verificar se o email do usuário está na sessão
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];

// Buscar o refresh_token do banco de dados
$query = "SELECT refresh_token FROM faixas WHERE email = '".$mysqli->real_escape_string($email)."'";
$result = $mysqli->query($query);

if ($result && $row = $result->fetch_assoc()) {
    $refresh_token = $row['refresh_token'];

    // Solicitar um novo access_token usando o refresh_token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://accounts.spotify.com/api/token");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'refresh_token',
        'refresh_token' => $refresh_token,
        'client_id' => '339071cd398c4a8fbb3fc58bdfb62d91',
        'client_secret' => 'b6bce70a6ebc4afa973af67eb6990201',
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['access_token'])) {
        $new_access_token = $data['access_token'];
        $new_expires_at = date('Y-m-d H:i:s', time() + $data['expires_in']); // Expira em 1 hora

        // Atualizar o novo access_token no banco de dados
        $update_query = "UPDATE faixas SET access_token = ?, token_expires_at = ? WHERE email = ?";
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param("sss", $new_access_token, $new_expires_at, $email);
        $stmt->execute();
        $stmt->close();

        echo "Token atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar o token: " . $data['error'];
    }
} else {
    echo "Nenhum refresh_token encontrado no banco de dados.";
}
?>
