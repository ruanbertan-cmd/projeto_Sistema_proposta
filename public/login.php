<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['username']);
    $senha = trim($_POST['password']);

    try {
        $stmt = $conexao->prepare("SELECT id, usuario, senha FROM pr_usuario WHERE usuario = :usuario");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario'] = $user['usuario'];

            header('Location: proposta_cadastro.php');
            exit;
        } else {
            $_SESSION['login_erro'] = true;
            header('Location: login.php');
            exit;
        }
    } catch(PDOException $e) {
        // Log de erro para análise
        error_log("Erro no login: " . $e->getMessage());
        // Mensagem genérica para o usuário
        $_SESSION['flash_error'] = 'Erro ao realizar o login. Tente novamente ou entre em contato com o administrador.';
        // Redireciona de volta para o cadastro
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
    <title>Tela de Login</title>
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
            color: #3a3a3aff;
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
            border-color: #a9a9a9ff;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            background-color: #9a9a9aff;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }

        button:hover {
            background-color: #626364ff;
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
            color: #6e6f6fff;
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
    <form action="login.php" method="POST">
        <div>
            <h1>Login</h1>
            <p>Insira seu usuário e senha para acessar o sistema.</p>
        </div>

        <input type="text" name="username" placeholder="Usuário" required>
        <input type="password" name="password" placeholder="Senha" required>

        <button type="submit" name="loginButton">Entrar</button>

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

    <a class="link_cadastro" href="cadastro.php">Não tem conta? Cadastre-se aqui</a>
</main>
</body>
</html>
