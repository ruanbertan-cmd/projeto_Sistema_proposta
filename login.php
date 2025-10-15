<?php
include('conexao.php');

$usuario = mysqli_real_escape_string($conexao, $_POST['username']);
$senha = mysqli_real_escape_string($conexao, $_POST['password']);

$query = "SELECT usuario_id, usuario * FROM usuario WHERE usuario = ('{$senha}') AND senha = md5('{$senha}')";

$result = mysqli_query($conexao, $query);

$row = mysqli_num_rows($result);

if($row == 1) {
    header('location: proposta_cadastro.php');
    exit;
} else {
    header('Location: login.php');
    exit;
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