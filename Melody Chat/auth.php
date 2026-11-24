<?php
session_start();
require('ligacao_melody.php');



// Configurações do Spotify
$client_id = '339071cd398c4a8fbb3fc58bdfb62d91';
$client_secret = 'b6bce70a6ebc4afa973af67eb6990201';
$redirect_uri = 'https://aluno18404.damiaodegoes.pt/auth.php';
$scopes = 'user-read-currently-playing user-read-recently-played user-read-playback-state user-library-read user-top-read';

// Verificar se temos um código de autorização do Spotify
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Trocar o código por tokens
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://accounts.spotify.com/api/token");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirect_uri,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['access_token'])) {
        $access_token = $data['access_token'];
        $refresh_token = $data['refresh_token'];
        $expires_at = date('Y-m-d H:i:s', time() + $data['expires_in']); // Expira em 1 hora
        $email = $_SESSION['email'];

        // Verificar se já existe um token armazenado para o e-mail do usuário
        $query = "SELECT access_token, token_expires_at FROM faixas WHERE email = '".$_SESSION['email']."'";
        $result = mysqli_query($con, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                // Se o token já existe, vamos atualizar o token apenas se estiver expirado
                if (new DateTime() >= new DateTime($row['token_expires_at'])) {
                    $update_query = "UPDATE faixas SET access_token = '$access_token', refresh_token = '$refresh_token', token_expires_at = '$expires_at' WHERE email = '".$_SESSION['email']."'";
                    $update_result = mysqli_query($con, $update_query);

                    if ($update_result) {
                        echo "Token expirado, atualizado com sucesso!";
                        $_SESSION['flag'] = 1;
                        header('location:profile.php');
                        exit();
                    } else {
                        echo "Erro ao atualizar token: " . mysqli_error($con);
                    }
                } else {
                    echo "Token ainda válido!";
                    $_SESSION['flag'] = 1;
                    header('location:profile.php');
                    exit();
                }
            } else {
                // Caso o usuário não tenha tokens no banco, inserir novos tokens
                $insert_query = "INSERT INTO faixas (email, access_token, refresh_token, token_expires_at) VALUES ('".$_SESSION['email']."', '$access_token', '$refresh_token', '$expires_at')";
                $insert_result = mysqli_query($con, $insert_query);

                if ($insert_result) {
                    echo "Tokens armazenados com sucesso!";
                    $_SESSION['flag'] = 1;
                    header('location:profile.php');
                    exit();
                } else {
                    echo "Erro ao inserir tokens: " . mysqli_error($con);
                }
            }
            mysqli_free_result($result); // Liberar o resultado
        } else {
            echo "Erro ao buscar os tokens no banco de dados: " . mysqli_error($con);
        }

        // Definir flag para sucesso de autenticação
    } else {
        // Caso ocorra um erro na autenticação
        echo "Erro ao autenticar: " . $data['error'];
    }
    exit;
}

// Redirecionar para o Spotify caso não haja código de autorização
$auth_url = "https://accounts.spotify.com/authorize?response_type=code&client_id=$client_id&scope=$scopes&redirect_uri=" . urlencode($redirect_uri);
header("Location: $auth_url");
exit;

?>
