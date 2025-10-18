<?php
session_start();
include('../src/config/conexao.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conexao, trim($_POST['username']));
    $senha = mysqli_real_escape_string($conexao, trim(md5($_POST['password'])));


$sql = "SELECT COUNT(*) AS total FROM  usuario WHERE usuario = '$username'";
$result = mysqli_query($conexao, $sql);
$row = mysqli_fetch_assoc($result);

if($row['total'] == 1) {
    $_SESSION['usuario_existe'] = true;
    header('location: cadastro.php');
    exit;
}

$sql = "INSERT INTO usuario (usuario, senha, data_cadastro) VALUES ('$username', '$senha', NOW())";

if($conexao -> query($sql) === TRUE) {
    $_SESSION['status_cadastro'] = true;
}

$conexao->close();
header('Location: cadastro.php');
exit;
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
                <label for="username"></label>
                <input type="text" name="username" placeholder="Usuário" required>
            </div>
            <div>
                <label for="password"></label>
                <input type="password" name="password" placeholder="Senha" required>
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