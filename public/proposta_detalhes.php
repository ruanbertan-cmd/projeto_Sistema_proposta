<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

if (!isset($_GET['id'])) {
    die("ID não informado.");
}

$id = intval($_GET['id']); // Sanitiza

$stmt = $conexao->query("SELECT id, volume, unidade_medida, formato, tipologia, borda, cor, local_uso, data_previsao, preco, cliente, obra, nome_produto, marca, embalagem, observacao, status 
        FROM formulario 
        WHERE id = $id");

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($result) == 0) {
    die("Nenhuma proposta encontrada para o ID informado.");
}
$row = $result[0];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes de Propostas</title>
    <style>
        /* ===== Reset ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: linear-gradient(135deg, #7d7d7d, #d3d3d3);
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

        main.main_proposta_detalhes {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px;
        }

        h1 {
            color: #3a3a3a;
            margin-bottom: 2rem;
            text-align: center;
        }

        table {
            width: 90%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            margin-bottom: 15px;
            font-size: 0.8rem;
            border: 1px solid #999;
            border-radius: 6px;
            overflow: hidden;
        }

        th, td {
            padding: 9px 15px;
            text-align: left;
            border-bottom: 1px solid #ccc;
            color: #333;
        }

        th {
            background-color: #e0e0e0;
            font-weight: 10rem;

        }

        tr:last-child td {
            border-bottom: none;
        }

        .botoes_acoes {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            background-color: #555555ff;
            padding: 10px;
            border-radius: 6px;
        }

        .botoes_acoes a {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: 0.2s;
        }

        .aprovar {
            background-color: #9a9a9a;
            color: white;
        }

        .aprovar:hover {
            background-color: #626364;
        }

        .rejeitar {
            background-color: #d3d3d3;
            color: #333;
        }

        .rejeitar:hover {
            background-color: #b0b0b0;
        }

        .status_text {
            font-weight: bold;
        }

        .voltar {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #444;
            font-weight: 500;
            transition: 0.2s;
        }

        .voltar:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            table {
                width: 100%;
            }
            .botoes_acoes {
                flex-direction: column;
                gap: 10px;
            }
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

<main class="main_proposta_detalhes">
    <h1>Detalhes da Proposta</h1>

    <table>
        <tr><th>Volume</th><td><?= htmlspecialchars($row['volume'] . ' ' . $row['unidade_medida']); ?></td></tr>
        <tr><th>Formato</th><td><?= htmlspecialchars($row['formato']); ?></td></tr>
        <tr><th>Tipologia</th><td><?= htmlspecialchars($row['tipologia']); ?></td></tr>
        <tr><th>Borda</th><td><?= htmlspecialchars($row['borda']); ?></td></tr>
        <tr><th>Cor</th><td><?= htmlspecialchars($row['cor']); ?></td></tr>
        <tr><th>Local de Uso</th><td><?= htmlspecialchars($row['local_uso']); ?></td></tr>
        <tr><th>Data Previsão</th><td><?= htmlspecialchars(date('d/m/Y', strtotime($row['data_previsao']))); ?></td></tr>
        <tr><th>Preço</th><td><?= htmlspecialchars($row['preco']); ?></td></tr>
        <tr><th>Cliente</th><td><?= htmlspecialchars($row['cliente']); ?></td></tr>
        <tr><th>Obra</th><td><?= htmlspecialchars($row['obra']); ?></td></tr>
        <tr><th>Nome Produto</th><td><?= htmlspecialchars($row['nome_produto']); ?></td></tr>
        <tr><th>Marca</th><td><?= htmlspecialchars($row['marca']); ?></td></tr>
        <tr><th>Embalagem</th><td><?= htmlspecialchars($row['embalagem']); ?></td></tr>
        <tr><th>Observação</th><td><?= nl2br(htmlspecialchars($row['observacao'])); ?></td></tr>
    </table>

    <div class="botoes_acoes">
        <?php if($row['status'] === 'Em analise'): ?>
            <a class="aprovar" href="aprovar_proposta.php?id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Tem certeza que deseja aprovar esta proposta?');">Aprovar</a>
            <a class="rejeitar" href="rejeitar_proposta.php?id=<?= htmlspecialchars($row['id']) ?>" onclick="return confirm('Tem certeza que deseja rejeitar esta proposta?');">Rejeitar</a>
        <?php else: ?>
            <?php 
                $status = $row['status'];
                $color = '#333';
                if(str_starts_with($status,'Aprovado')) $color = '#62f68cff';
                elseif(str_starts_with($status,'Rejeitado')) $color = '#f95a5aff';
            ?>
            <p class="status_text" style="color: <?= $color ?>;">Status: <?= htmlspecialchars($status) ?></p>
        <?php endif; ?>
    </div>
    

    <a class="voltar" href="proposta_fases.php">Voltar</a>
</main>

</body>
</html>