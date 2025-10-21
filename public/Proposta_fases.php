<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

// Consulta os registros da tabela
$stmt = $conexao->query("SELECT id, volume, unidade_medida, formato, tipologia, borda, local_uso, data_previsao, nome_produto, marca, status FROM formulario ORDER BY id DESC");
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fases das Propostas</title>
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
        max-width: 1100px;
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
    }

    table.tabela_propostas th, table.tabela_propostas td {
        padding: 12px 10px;
        text-align: center;
        border-bottom: 1px solid #ccc;
        font-size: 0.3rem;
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

    a.aprovar { background-color: #9a9a9a; }
    a.aprovar:hover { background-color: #626364; }

    a.rejeitar { background-color: #777; }
    a.rejeitar:hover { background-color: #555; }

    a.detalhes { background-color: #666; }
    a.detalhes:hover { background-color: #555; }

    strong { font-weight: bold; }
    .status-aprovado { color: '#62f68cff'; }
    .status-rejeitado { color: '#f95a5aff'; }

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
                    <td><a href="proposta_detalhes.php?id=<?= $row['id'] ?>" class="detalhes">Ver Detalhes</a></td>
                    <td>
                        <?php if ($row['status'] === 'Em analise'): ?>
                            <a href="aprovar_proposta.php?id=<?= htmlspecialchars($row['id']) ?>" class="aprovar"
                               onclick="return confirm('Tem certeza que deseja aprovar esta proposta?');">Aprovar</a>
                            <a href="rejeitar_proposta.php?id=<?= htmlspecialchars($row['id']) ?>" class="rejeitar"
                               onclick="return confirm('Tem certeza que deseja rejeitar esta proposta?');">Rejeitar</a>
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

</body>
</html>