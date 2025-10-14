<?php
include 'conexao.php';

$usuario = mysqli_real_escape_string($conexao, trim($_POST['username']));
$senha = mysqli_real_escape_string($conexao, trim(md5($_POST['password'])));

$sql = "SELECT COUNT(*) AS total FROM  usuario WHERE usuario = '$usuario'";
$result = mysqli_query($conexao, $sql);
$row = mysqli_fetch_assoc($result);

if($row['total'] == 1) {
    $_SESSION['usuario_existe'] = true;
    header('location: cadastro.php');
    exit;
}

$sql = "INSERT INTO usuario (usuario, senha, data_cadastro) VALUES ('$usuario', '$senha', NOW())";

if($conexao -> query($sql) === TRUE) {
    $_SESSION['status_cadastro'] = true;
}

$conexao->close();

header('Location: cadastro.php');
exit;

?>







<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>



    <main class="main_cadastro">
        <form action="cadastro.php" method="POST">
            <div>
                <h1>Cadastro</h1>
                <p>Insira seu usuário e senha para se cadastrar no sistema.</p>
            </div>
            <div>
                <label for="username"></label>
                <input type="text" name="username" placeholder="Usuário" required>
            </div>
            <div>
                <label for="password"></label>
                <input type="password" name="password" placeholder="Senha" required>
            </div>
            <div>
                <button type="submit" name="loginButton">Entrar</button>

            </div>

    
</body>
</html>