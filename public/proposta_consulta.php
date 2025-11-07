<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

// 🕒 Corrige definitivamente o fuso horário (Santa Catarina)
date_default_timezone_set('America/Sao_Paulo');
$conexao->exec("SET time_zone = '-03:00'");

// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Consulta propostas com flag de novo comentário
$stmt = $conexao->prepare("
    SELECT 
        f.id,
        f.nome_produto, 
        f.data_previsao, 
        f.status,
        EXISTS (
            SELECT 1 
            FROM comentarios c
            LEFT JOIN comentarios_visualizacao cv 
                ON cv.comentario_id = c.id AND cv.usuario_id = ?
            WHERE c.formulario_id = f.id 
              AND cv.id IS NULL 
              AND c.usuario_id != ?
        ) AS novo_comentario
    FROM formulario f
    WHERE f.usuario_id = ?
    ORDER BY f.id DESC
");
$stmt->execute([$usuario_id, $usuario_id, $usuario_id]);
$propostas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Minhas Propostas</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }

    body {
        background: linear-gradient(135deg, #7d7d7dff, #d3d3d3ff);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        color: #333;
    }

    header.barra_navegacao {
        background-color: #9a9a9a;
        padding: 15px 0;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        width: 100%;
        position: sticky;
        top: 0;
        z-index: 1000;
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

    .navbar_container a:hover { text-decoration: underline; }

    main.main_proposta_consulta {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 40px 20px;
    }

    .tabela_container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 1200px;
        padding: 25px;
        animation: fadeIn 0.8s ease-in-out;
        overflow-x: auto;
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

    table.tabela_propostas {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #999;
        border-radius: 6px;
        overflow: hidden;
    }

    table.tabela_propostas th, table.tabela_propostas td {
        padding: 12px 10px;
        text-align: center;
        border-bottom: 1px solid #ccc;
        font-size: 0.85rem;
    }

    table.tabela_propostas th {
        background-color: #666;
        color: white;
        font-weight: 600;
    }

    a.detalhes {
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        transition: 0.2s;
        background-color: #aeadadff;
    }
    a.detalhes:hover { background-color: #a1a1a1ff; }

    table.tabela_propostas tr:nth-child(even) { background-color: #f5f5f5; }
    table.tabela_propostas tr:hover { background-color: #e0e0e0; }

    .status-aprovado { color: #4caf50; font-weight: 600; }
    .status-rejeitado { color: #f44336; font-weight: 600; }
    .status-analise { color: #757575; font-weight: 600; }
</style>
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

<main class="main_proposta_consulta">
    <div class="tabela_container">
        <h1>Consulta Propostas</h1>

        <?php if (count($propostas) > 0): ?>
        <table class="tabela_propostas">
            <tr>
                <th>Produto</th>
                <th>Data de Previsão</th>
                <th>Status</th>
                <th>Dados Completos</th>
            </tr>
            <?php foreach ($propostas as $p): ?>
                <?php
                    $status = trim($p['status']);
                    $statusClass =
                        str_starts_with($status, 'Aprovado') ? 'status-aprovado' :
                        (str_starts_with($status, 'Rejeitado') ? 'status-rejeitado' : 'status-analise');
                ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome_produto']) ?></td>
                    <td><?= !empty($p['data_previsao']) ? date('d/m/Y H:i', strtotime($p['data_previsao'])) : '-' ?></td>
                    <td class="<?= $statusClass ?>"><?= htmlspecialchars($status ?: 'Em análise') ?></td>
                    <td>
                        <a href="proposta_detalhes.php?id=<?= $p['id'] ?>&origem=consulta" class="detalhes">
                            Ver Detalhes
                            <?php if ($p['novo_comentario']): ?>
                                <span style="color:#ffeb3b; margin-left:5px;">🔔</span>
                            <?php endif; ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <script>
                Swal.fire({
                    icon: 'info',
                    title: 'Nenhuma proposta encontrada',
                    text: 'Você ainda não cadastrou nenhuma proposta.',
                    confirmButtonColor: '#9a9a9a',
                    confirmButtonText: 'Entendi'
                });
            </script>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
