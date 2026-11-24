<?php
require('../ligacao_melody.php');

header('Content-Type: application/json');

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $sql = "SELECT email FROM melodychat WHERE email = '$email'";
    $result = mysqli_query($con, $sql);
    $exists = mysqli_num_rows($result) > 0;

    echo json_encode(['exists' => $exists]);
} else {
    echo json_encode(['exists' => false]);
}

mysqli_close($con);
?>