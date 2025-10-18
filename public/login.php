<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php'); // caminho corrigido

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['username']);
    $senha = trim($_POST['password']);

    try {
        // Busca o usuário pelo nome
        $stmt = $conexao->prepare("SELECT id, usuario, senha FROM usuario WHERE usuario = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o usuário existe e senha está correta
        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['usuario'] = $user['usuario'];
            header('Location: proposta_cadastro.php');
            exit;
        } else {
            $_SESSION['login_erro'] = true;
            header('Location: login.php');
            exit;
        }
    } catch(PDOException $e) {
        die("Erro no login: " . $e->getMessage());
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
</body>
</html>
