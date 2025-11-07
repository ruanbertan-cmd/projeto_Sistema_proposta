<?php
include(__DIR__ . '/config/conexao.php');

if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    die("Erro no upload do arquivo.");
}

$caminhoTemp = $_FILES['arquivo']['tmp_name'];
$extensao = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));

if ($extensao !== 'csv') {
    die("Apenas arquivos CSV são aceitos.");
}

try {
    // Apaga todos os dados anteriores
    $conexao->exec("DELETE FROM lote_minimo");

    // Detecta automaticamente o delimitador
    $primeiraLinha = fgets(fopen($caminhoTemp, 'r'));
    $delimitador = substr_count($primeiraLinha, ';') > substr_count($primeiraLinha, ',') ? ';' : ',';
    rewind(fopen($caminhoTemp, 'r')); // volta para o início do arquivo

    $handle = fopen($caminhoTemp, 'r');
    if ($handle === false) {
        throw new Exception("Erro ao abrir o arquivo.");
    }

    // Lê cabeçalho (descarta)
    $cabecalho = fgetcsv($handle, 0, $delimitador);
    if (!$cabecalho) {
        throw new Exception("Arquivo CSV vazio ou formato inválido.");
    }

    $sql = "INSERT INTO lote_minimo 
        (Emp, Uni, Polo, Uni_fabril, bitola, Formato, Tipologia, Un, Descricao, Situacao, Lote, lote_alternativo1, lote_alternativo2)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);

    $contador = 0;

    while (($linha = fgetcsv($handle, 0, $delimitador)) !== false) {
        // Ignora linhas vazias
        if (empty(array_filter($linha))) continue;

        // Ajusta número de colunas se tiver menos de 13
        $linha = array_pad($linha, 13, null);

        $stmt->execute($linha);
        $contador++;
    }

    fclose($handle);

    // Registro no histórico
$stmtHist = $conexao->prepare("
    INSERT INTO lote_minimo_historico (usuario_id, nome_arquivo, quantidade_registros, observacao)
    VALUES (?, ?, ?, ?)
");

$stmtHist->execute([
    $_SESSION['usuario_id'] ?? 0,                     // se não tiver login, grava 0
    $_FILES['arquivo']['name'],                       // nome do arquivo enviado
    $contador,                                        // quantidade de registros importados
    'Carga substituiu os dados anteriores.'           // observação opcional
]);


    // Redireciona de volta à página principal com popup de sucesso
    header("Location: ../public/proposta_lote.php?upload=ok&count={$contador}");
    exit;

} catch (Exception $e) {
    die("Erro ao processar arquivo: " . $e->getMessage());
}
