<?php
session_start();
include('../src/config/conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$usuario = $_POST['username'];
$senha = $_POST['password'];

$stmt = $conexao -> prepare("SELECT usuario_id, usuario FROM usuario WHERE usuario = :usuario AND senha = md5(:senha)");

$stmt = bindParam(':usuario', $usuario);
$stmt = bindParam(':senha', $senha);
$stmt -> execute();

if($stmt -> rowCount() === 1) {
    $_SESSION['usuario'] = $usuario;
    header('location: proposta_cadastro.php');
    exit;
} else {
    $_SESSION['login_erro'] = true;
    header('Location: login.php');
    exit;
}
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <main class="main_login">
        <form action="login.php" method="POST">
            <div>
                <h1>Login</h1>
                <p>Insira seu usuário e senha para se logar no sistema.</p>
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
                <button type="submit" name="loginButton">Login</button>

            </div>
            <?php
                if(isset($_SESSION['login_erro'])):
                    echo '<p class="alerta_erro">Usuário ou senha inválidos.</p>';
                    unset($_SESSION['login_erro']);
                endif;
                if(isset($_SESSION['logindeslogado'])):
                    echo '<p class="alerta_sucesso">Deslogado com sucesso.</p>';
                    unset($_SESSION['logindeslogado']);
                endif;
            ?>
        </form>
        Ir para <a href="cadastro.php">Cadastro</a>
    </main>