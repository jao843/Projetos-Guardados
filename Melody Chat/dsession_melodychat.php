<?php
session_start();
require('ligacao_melody.php');


$login_sql = "UPDATE melodychat set login = 0 where email = '" . $_SESSION['email']."'";
$login_query = mysqli_query($con, $login_sql);


if($login_query){
    session_destroy();
    header('location:melodychat.php');
}
?>