<?php
if(isset($_POST['nome'])){
    if($_POST['nome']!='' && is_numeric($_POST['nome'])==FALSE){
        echo 'Olá '.$_POST['nome'];
    }else{
        echo '<spanh style="color:red;">ERRO: FORMATO INVÁLIDO</span>';
    }
}

?>
<form method="post">
    <input type="text" name="nome" placeholder="Qual eh o seu Nome?">
    <input type="submit" name="botao" value="submeter">
</form>

<?php
$calculo=0;
echo '<br><hr>';
    if(isset($_POST["idade"])){
        if(is_numeric($_POST["idade"])==true & $_POST["idade"]!=""){
            $calculo= 2024 - $_POST["idade"];
            echo "A sua data de nasciemento é de " .$calculo;
        }
    }
?>

<form method="post">
    <input type="text" name="idade" value="" placeholder="Insira o ano de nascimento">
    <input type="submit" name="botao2" value="submeter">
</form>