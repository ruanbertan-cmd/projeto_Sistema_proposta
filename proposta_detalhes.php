<?php
include_once('conexao.php');

// Verifica se foi passado um ID na URL
if (!isset($_GET['id'])) {
    die("ID não informado.");
}

$id = intval($_GET['id']); // Sanitiza

// Consulta apenas a linha com o ID informado
$stmt = $conexao -> query("SELECT id, volume, unidade_medida, formato, tipologia, borda, cor, local_uso, data_previsao, preco, cliente, obra, nome_produto, marca, embalagem, observacao, status 
        FROM formulario 
        WHERE id = $id");

$result = $stmt -> FetchAll(PDO::FETCH_ASSOC);

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
    <h1>Consulta Completa dos Dados da Propostas</h1>
 
    <table class="tabela_propostas_detalhes" style="border-radius:4px; width: 90%; margin-left:auto; margin-right:auto; border-collapse: collapse;">
        <tr><th>Volume</th><td><?php echo htmlspecialchars($row['volume'] . ' ' . $row['unidade_medida']); ?></td></tr>
        <tr><th>Formato</th><td><?php echo htmlspecialchars($row['formato']); ?></td></tr>
        <tr><th>Tipologia</th><td><?php echo htmlspecialchars($row['tipologia']); ?></td></tr>
        <tr><th>Borda</th><td><?php echo htmlspecialchars($row['borda']); ?></td></tr>
        <tr><th>Cor</th><td><?php echo htmlspecialchars($row['cor']); ?></td></tr>
        <tr><th>Local de Uso</th><td><?php echo htmlspecialchars($row['local_uso']); ?></td></tr>
        <tr><th>Data Previsão</th><td><?php echo htmlspecialchars(date('d/m/y', strtotime($row['data_previsao']))); ?></td></tr>
        <tr><th>Preço</th><td><?php echo htmlspecialchars($row['preco']); ?></td></tr>
        <tr><th>Cliente</th><td><?php echo htmlspecialchars($row['cliente']); ?></td></tr>
        <tr><th>Obra</th><td><?php echo htmlspecialchars($row['obra']); ?></td></tr>
        <tr><th>Nome Produto</th><td><?php echo htmlspecialchars($row['nome_produto']); ?></td></tr>
        <tr><th>Marca</th><td><?php echo htmlspecialchars($row['marca']); ?></td></tr>
        <tr><th>Embalagem</th><td><?php echo htmlspecialchars($row['embalagem']); ?></td></tr>
        <tr><th>Observação</th><td><?php echo nl2br(htmlspecialchars($row['observacao'])); ?></td></tr>
    </table>

    <table>
    <!-- Botões de Aprovar / Rejeitar -->
    <div style="display:flex; justify-content:center; gap:10px; margin-top:20px;">
        <?php if($row['status'] === 'Em analise'): ?>
            <a href="aprovar_proposta.php?id=<?= htmlspecialchars($row['id']) ?>"
            style="padding:10px 20px; background-color:#28a745; color:white; text-decoration:none; border-radius:4px;"
            onclick="return confirm('Tem certeza que deseja aprovar esta proposta?');">
                Aprovar
            </a>
            <a href="rejeitar_proposta.php?id=<?= htmlspecialchars($row['id']) ?>"
            style="padding:10px 20px; background-color:#dc3545; color:white; text-decoration:none; border-radius:4px;"
            onclick="return confirm('Tem certeza que deseja rejeitar esta proposta?');">
                Rejeitar
            </a>
        <?php else: ?>
            <strong style="color: <?= $row['status'] === 'Aprovado' ? 'green' : 'red' ?>;">
                <?= htmlspecialchars($row['status']) ?>
            </strong>
        <?php endif; ?>
    </div>

    <a class="voltar" href="proposta_fases.php">← Voltar</a>
    </table>
    </main>
</body>
</html>
