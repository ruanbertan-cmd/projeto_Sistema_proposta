<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/conexao.php';

date_default_timezone_set('America/Sao_Paulo');
$conexao->exec("SET time_zone = '-03:00'");

if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    die("Erro no upload do arquivo.");
}

$caminhoTemp = $_FILES['arquivo']['tmp_name'];
$extensao = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));

if ($extensao !== 'csv') {
    die("Apenas arquivos CSV são aceitos.");
}

try {
    $conexao->exec("DELETE FROM lote_minimo");

    $handle = fopen($caminhoTemp, 'r');
    if ($handle === false) {
        throw new Exception("Erro ao abrir o arquivo.");
    }

    $primeiraLinha = fgets($handle);
    $delimitador = substr_count($primeiraLinha, ';') > substr_count($primeiraLinha, ',') ? ';' : ',';
    rewind($handle);

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
        if (empty(array_filter($linha))) continue;
        $linha = array_pad($linha, 13, null);
        $stmt->execute($linha);
        $contador++;
    }

    fclose($handle);

    $stmtHist = $conexao->prepare("
        INSERT INTO lote_minimo_historico (usuario_id, nome_arquivo, quantidade_registros, observacao)
        VALUES (?, ?, ?, ?)
    ");
    $stmtHist->execute([
        $_SESSION['usuario_id'] ?? 0,
        $_FILES['arquivo']['name'],
        $contador,
        'Carga substituiu os dados anteriores.'
    ]);

    // Caminho relativo (funciona tanto local quanto no servidor)
    header("Location: /proposta_lote.php?upload=ok&count={$contador}");
    exit;


} catch (Exception $e) {
    die("Erro ao processar arquivo: " . $e->getMessage());
}
