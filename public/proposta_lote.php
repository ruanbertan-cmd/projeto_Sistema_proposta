<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

// Corrige definitivamente o fuso horário
date_default_timezone_set('America/Sao_Paulo');
$conexao->exec("SET time_zone = '-03:00'");

// Verifica se o usuário já está logado
if (isset($_SESSION['usuario']) && !empty($_SESSION['usuario']['login_usuario'])):
    header('Location: proposta_cadastro.php');
    exit;

$stmt = $conexao->query("SELECT * FROM pr_lote_minimo ORDER BY id ASC");
$lotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lote Mínimo</title>

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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header.barra_navegacao {
            background-color: #9a9a9a;
            padding: 15px 0;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            top: 0;
            width: 100%;
            z-index: 1000;
            position: sticky;
        }

        .navbar_container ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 40px;
        }

        .navbar_container a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .navbar_container a:hover {
            text-decoration: underline;
        }

        main.main_proposta_fases {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
        }

        .tabela_container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            padding: 40px 50px;
            width: 100%;
            max-width: 1200px;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h1 {
            text-align: center;
            color: #3a3a3a;
            margin-bottom: 25px;
        }

        form {
            text-align: center;
            margin-bottom: 25px;
        }

        input[type="file"] {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background-color: #fafafa;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        input[type="file"]:hover {
            background-color: #f0f0f0;
        }

        input[type="file"]::-webkit-file-upload-button {
            background: #9a9a9a;
            border: none;
            color: white;
            padding: 8px 14px;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.2s;
        }

        input[type="file"]::-webkit-file-upload-button:hover {
            background: #626364;
        }

        button {
            padding: 10px 20px;
            background-color: #9a9a9a;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            margin-left: 10px;
        }

        button:hover {
            background-color: #626364;
        }

        button:active {
            transform: scale(0.98);
        }

        .tabela_propostas {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 0.75rem;
            border: 1px solid #ddd;
        }

        .tabela_propostas th {
            background-color: #9a9a9a;
            color: white;
            padding: 10px;
            border-bottom: 2px solid #ccc;
        }

        .tabela_propostas td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .tabela_propostas tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .tabela_propostas tr:hover {
            background-color: #f1f1f1;
        }

        /* ===== POPUP SUCESSO ===== */
        .swal2-popup {
            font-family: "Poppins", sans-serif;
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .tabela_container {
                padding: 25px 20px;
            }

            .tabela_propostas th, .tabela_propostas td {
                font-size: 0.75rem;
                padding: 6px;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
<header class="barra_navegacao">
    <nav class="navbar">
        <div class="navbar_container">
            <ul>
                <li><a href="proposta_cadastro.php">Cadastro</a></li>
                <li><a href="proposta_consulta.php">Consulta</a></li>
                <li><a href="proposta_aprovacao.php">Aprovação</a></li>
                <li><a href="proposta_lote.php">Lote</a></li>
            </ul>
        </div>
    </nav>
</header>

<main class="main_proposta_fases">
    <div class="tabela_container">
        <h1>Lista de Lote Mínimo Sendo Considerada</h1>

        <form action="upload_lote.php" method="POST" enctype="multipart/form-data">
            <input type="file" name='arquivo' id='arquivo' accept=".csv" required>
            <button type="submit">Enviar Arquivo</button>
        </form>

        <table class="tabela_propostas">
            <tr>
                <th>Emp</th>
                <th>Uni</th>
                <th>Polo</th>
                <th>Uni_fabril</th>
                <th>Bitola</th>
                <th>Formato</th>
                <th>Tipologia</th>
                <th>Un</th>
                <th>Acabamento</th>
                <th>Descrição</th>
                <th>Situação</th>
                <th>Lote</th>
                <th>Lote Alternativo 1</th>
                <th>Lote Alternativo 2</th>
            </tr>

            <?php if (!empty($lotes)): ?>
                <?php foreach ($lotes as $linha): ?>
                    <tr>
                        <td><?= htmlspecialchars($linha['emp']) ?></td>
                        <td><?= htmlspecialchars($linha['uni']) ?></td>
                        <td><?= htmlspecialchars($linha['polo']) ?></td>
                        <td><?= htmlspecialchars($linha['uni_fabril']) ?></td>
                        <td><?= htmlspecialchars($linha['bitola']) ?></td>
                        <td><?= htmlspecialchars($linha['formato']) ?></td>
                        <td><?= htmlspecialchars($linha['tipologia']) ?></td>
                        <td><?= htmlspecialchars($linha['un']) ?></td>
                        <td><?= htmlspecialchars($linha['acabamento']) ?></td>
                        <td><?= htmlspecialchars($linha['descricao']) ?></td>
                        <td><?= htmlspecialchars($linha['situacao']) ?></td>
                        <td><?= htmlspecialchars($linha['lote']) ?></td>
                        <td><?= htmlspecialchars($linha['lote_alternativo1']) ?></td>
                        <td><?= htmlspecialchars($linha['lote_alternativo2']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="14" style="text-align:center;">Nenhum lote carregado ainda.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</main>

<?php if (isset($_GET['upload']) && $_GET['upload'] === 'ok'): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Arquivo carregado com sucesso!',
        text: '<?= isset($_GET['count']) ? htmlspecialchars($_GET['count']) . ' registros inseridos.' : '' ?>',
        showConfirmButton: false,
        timer: 3000
    }).then(() => {
        // Remove os parâmetros da URL sem recarregar a página
        if (window.history.replaceState) {
            const url = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({}, document.title, url);
        }
    });
</script>
<?php endif; ?>
</body>
</html>
<?php
// Se não estiver logado, redireciona para a página de validação
else:
    $link = 'http://localhost:8080/proposta_cadastro.php';
    $link = base64_encode($link);
    #header('Location: https://ww1.eliane.com/valida/?link=' . $link);
    header('Location: https://ww1.eliane.com/valida/?link=' . $link);
    exit;
endif;
?>
