<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php'); // caminho corrigido

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['usuario']);
    $senha = trim($_POST['senha']);

    try {
        // Verifica se o usuário já existe
        $stmt = $conexao->prepare("SELECT COUNT(*) AS total FROM usuario WHERE usuario = :usuario");
        $stmt->bindParam(':usuario', $username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
            $_SESSION['usuario_existe'] = true;
            header('Location: cadastro.php');
            exit;
        }

        // Cria hash seguro da senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Insere no banco
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

    <style>
        /* ===== Reset ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: linear-gradient(135deg, #7d7d7dff, #d3d3d3ff);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        main.main_login {
            background: white;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 380px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            color: #3a3a3a;
            margin-bottom: 10px;
        }

        p {
            color: #555;
            font-size: 14px;
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
            transition: border 0.2s;
        }

        input:focus {
            border-color: #a9a9a9;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            background-color: #9a9a9a;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }

        button:hover {
            background-color: #626364;
        }

        button:active {
            transform: scale(0.98);
        }

        .alerta_erro {
            color: #b71c1c;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            margin-top: 15px;
        }

        .alerta_sucesso {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            margin-top: 15px;
        }

        a {
            color: #6e6f6f;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        .link_cadastro {
            margin-top: 20px;
            display: block;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>

<main class="main_login">
    <form action="cadastro.php" method="POST">
        <div>
            <h1>Cadastro</h1>
            <p>Crie sua conta para acessar o sistema.</p>
        </div>

        <div>
            <label for="usuario"></label>
            <input type="text" name="usuario" placeholder="Usuário" required>
        </div>

        <div>
            <label for="senha"></label>
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
                echo '<p class="alerta_sucesso">Cadastro efetuado com sucesso! <a href="login.php">Faça login</a>.</p>';
                unset($_SESSION['status_cadastro']);
            endif;
        ?>
    </form>
    Ir para <a href="login.php">Login</a>
</main>

</body>
</html>