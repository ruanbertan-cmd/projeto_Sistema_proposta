<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php'); // corrigi o caminho

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['usuario']);
    $senha = trim($_POST['senha']);

    try {
        // 1️⃣ Verifica se o usuário já existe
        $stmt = $conexao->prepare("SELECT COUNT(*) AS total FROM usuario WHERE usuario = :usuario");
        $stmt->bindParam(':usuario', $username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row['total'] > 0) {
            $_SESSION['usuario_existe'] = true;
            header('Location: cadastro.php');
            exit;
        }

        // 2️⃣ Cria hash seguro da senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // 3️⃣ Insere o usuário no banco
        $stmt = $conexao->prepare("INSERT INTO usuario (usuario, senha, data) VALUES (:usuario, :senha, NOW())");
        $stmt->bindParam(':usuario', $username);
        $stmt->bindParam(':senha', $senha_hash);
        $stmt->execute();

        $_SESSION['status_cadastro'] = true;
        header('Location: cadastro.php');
        exit;

    } catch(PDOException $e) {
        die("Erro no cadastro: " . $e->getMessage());
    }
}
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
            <label for="usuario"></label>
            <input type="text" name="usuario" placeholder="Usuário" required>
        </div>
        <div>
            <label for="password"></label>
            <input type="password" name="senha" placeholder="Senha" required>
        </div>
        <div>
            <button type="submit" name="cadastroButton">Cadastrar</button>
        </div>
        <?php
            if(isset($_SESSION['usuario_existe'])):
                echo '<p class="alerta_erro">Usuário já existe. Escolha outro nome de usuário.</p>';
                unset($_SESSION['usuario_existe']);
            endif;
            if(isset($_SESSION['status_cadastro'])):
                echo '<p class="alerta_sucesso">Cadastro efetuado com sucesso! Faça login para entrar.</p>';
                unset($_SESSION['status_cadastro']);
            endif;
        ?>
    </form>
    Ir para <a href="login.php">Login</a>
</main>
</body>
</html>