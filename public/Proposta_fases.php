<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

// Consulta os registros da tabela detalhados abaixo:
$stmt = $conexao -> query("SELECT id,volume,unidade_medida,formato,tipologia,borda,local_uso,data_previsao,nome_produto,marca,status FROM formulario ORDER BY id DESC");
$result = $stmt -> fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Propostas</title>
    <link rel="stylesheet" href="style.css">
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
    <h1>Fases Propostas</h1>
 
    <table class="tabela_propostas">
        
        <tr>
            <th>Volume</th>
            <th>Formato</th>
            <th>Tipologia</th>
            <th>Borda</th>
            <th>Local de Uso</th>
            <th>Data Previsão</th>
            <th>Nome do produto</th>
            <th>Marca</th>
            <th>Dados Completo</th>
            <th>Status</th>

        </tr>
        <?php if (count($result) > 0): ?>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['volume']. ' ' . $row['unidade_medida']) ?></td>
                    <td><?= htmlspecialchars($row['formato']) ?></td>
                    <td><?= htmlspecialchars($row['tipologia']) ?></td>
                    <td><?= htmlspecialchars($row['borda']) ?></td>
                    <td><?= htmlspecialchars($row['local_uso']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($row['data_previsao'])))?></td>
                    <td><?= htmlspecialchars($row['nome_produto']) ?></td>
                    <td><?= htmlspecialchars($row['marca']) ?></td>
                    

                    <!-- Link para ver detalhes completos -->
                    <td><a href="proposta_detalhes.php?id=<?= $row['id'] ?>">Ver Detalhes</a></td>



                  <!-- Botões de Aprovar / Rejeitar -->
                    
                    <td style="color: <?= $row['status'] === 'Aprovado' ? 'green' : ($row['status'] === 'Rejeitado' ? 'red' : 'black') ?>;">
                    <?php if($row['status'] === 'Em analise'): ?>
                        <a href="aprovar_proposta.php?id=<?= htmlspecialchars($row['id']) ?>"
                           style="margin-right:5px; padding:5px 10px; background-color:#28a745; color:white; text-decoration:none; border-radius:4px;"
                           onclick="return confirm('Tem certeza que deseja aprovar esta proposta?');">
                           Aprovar
                        </a>
                        <a href="rejeitar_proposta.php?id=<?= htmlspecialchars($row['id']) ?>"
                           style="padding:5px 10px; background-color:#dc3545; color:white; text-decoration:none; border-radius:4px;"
                           onclick="return confirm('Tem certeza que deseja rejeitar esta proposta?');">
                           Rejeitar
                        </a>
                    <?php else: ?>
                        <strong><?=htmlspecialchars($row['status']) ?></strong>
                    <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="14">Nenhuma proposta encontrada.</td></tr>
        <?php endif; ?>
    </table>
    </main>
</body>
</html>
