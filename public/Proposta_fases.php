<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

// 🕒 Corrige definitivamente o fuso horário
date_default_timezone_set('America/Sao_Paulo');
$conexao->exec("SET time_zone = '-03:00'");

// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Consulta com flag de novo comentário
$stmt = $conexao->prepare("
    SELECT 
        f.id,
        f.volume,
        f.unidade_medida,
        f.formato,
        f.tipologia,
        f.borda,
        f.local_uso,
        f.data_previsao,
        f.nome_produto,
        f.marca,
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
    ORDER BY f.id DESC
");
$stmt->execute([$usuario_id, $usuario_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fases das Propostas</title>
<!-- SweetAlert2 CSS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* ===== Reset ===== */
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

    .navbar_container a:hover { text-decoration: underline; }

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
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 1400px;
        overflow-x: auto;
        padding: 20px;
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
        font-size: 0.7rem;
    }

    table.tabela_propostas th {
        background-color: #666;
        color: white;
        font-weight: 600;
    }

    table.tabela_propostas tr:nth-child(even) { background-color: #f5f5f5; }
    table.tabela_propostas tr:hover { background-color: #e0e0e0; }

    table.tabela_propostas a {
        text-decoration: none;
        color: white;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 0.85rem;
        transition: 0.2s;
    }

    a.aprovar { background-color: #848484ff; }
    a.aprovar:hover { background-color: #656565ff; }

    a.rejeitar { background-color: #aeadadff; }
    a.rejeitar:hover { background-color: #a1a1a1ff; }

    a.detalhes { background-color: #aeadadff; }
    a.detalhes:hover { background-color: #a1a1a1ff; }

    strong { font-weight: bold; }

    @media (max-width: 768px) {
        .tabela_container { padding: 15px; }
        table.tabela_propostas th, table.tabela_propostas td { padding: 8px; font-size: 0.8rem; }
    }
</style>
</head>
<body>

<header class="barra_navegacao">
    <nav class="navbar">
        <div class="navbar_container">
            <ul>
                <li><a href="proposta_cadastro.php">Cadastro</a></li>
                <li><a href="proposta_consulta.php">Consulta</a></li>
                <li><a href="proposta_fases.php">Fases</a></li>
            </ul>
        </div>
    </nav>
</header>

<main class="main_proposta_fases">
    <div class="tabela_container">
        <h1>Fases das Propostas</h1>
        <table class="tabela_propostas">
            <tr>
                <th>Volume</th>
                <th>Formato</th>
                <th>Tipologia</th>
                <th>Borda</th>
                <th>Local de Uso</th>
                <th>Data Previsão</th>
                <th>Nome do Produto</th>
                <th>Marca</th>
                <th>Dados Completos</th>
                <th>Status</th>
            </tr>
            <?php if (count($result) > 0): ?>
                <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['volume'] . ' ' . $row['unidade_medida']) ?></td>
                    <td><?= htmlspecialchars($row['formato']) ?></td>
                    <td><?= htmlspecialchars($row['tipologia']) ?></td>
                    <td><?= htmlspecialchars($row['borda']) ?></td>
                    <td><?= htmlspecialchars($row['local_uso']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($row['data_previsao']))) ?></td>
                    <td><?= htmlspecialchars($row['nome_produto']) ?></td>
                    <td><?= htmlspecialchars($row['marca']) ?></td>
                    <td>
                        <a href="proposta_detalhes.php?id=<?= $row['id'] ?>&origem=fases" class="detalhes">
                            Ver Detalhes
                            <?php if ($row['novo_comentario']): ?>
                                <span style="color:#ffeb3b; margin-left:5px;">🔔</span>
                            <?php endif; ?>
                        </a>
                    </td>
                    <td>
                        <?php if ($row['status'] === 'Em analise'): ?>
                            <a href="#" class="aprovar" onclick="confirmAction('aprovar', <?= $row['id'] ?>)">Aprovar</a>
                            <a href="#" class="rejeitar" onclick="confirmAction('rejeitar', <?= $row['id'] ?>)">Rejeitar</a>
                        <?php else: ?>
                        <?php
                            $status = $row['status'];
                            if (str_starts_with($status, 'Aprovado')) {
                                $color = 'green';
                            } elseif (str_starts_with($status, 'Rejeitado')) {
                                $color = 'red';
                            } else {
                                $color = 'black';
                            }
                        ?>
                        <strong style="color: <?= $color ?>;">
                            <?= htmlspecialchars($status) ?>
                        </strong>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="10">Nenhuma proposta encontrada.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</main>

<script>
function confirmAction(action, id) {
    let actionText = action === 'aprovar' ? 'aprovar' : 'rejeitar';
    let actionColor = action === 'aprovar' ? '#828282ff' : '#d1d1d1ff';

    Swal.fire({
        title: `Tem certeza que deseja ${actionText} esta proposta?`,
        icon: action === 'aprovar' ? 'success' : 'error',
        showCancelButton: true,
        confirmButtonColor: actionColor,
        cancelButtonColor: '#777',
        confirmButtonText: `Sim, ${actionText}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `actions.php?action=${action}&id=${id}`;
        }
    });
}
</script>

</body>
</html>
