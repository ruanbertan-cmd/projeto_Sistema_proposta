<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

// Verifica se o usuário já está logado
if (isset($_SESSION['usuario']) && !empty($_SESSION['usuario']['login_usuario'])):
    header('Location: proposta_cadastro.php');
    exit;

// Fuso horário e timezone do MySQL
date_default_timezone_set('America/Sao_Paulo');
$conexao->exec("SET time_zone = '-03:00'");

try {
    // === Verifica upload ===
    if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Erro no upload do arquivo.");
    }

    $caminhoTemp = $_FILES['arquivo']['tmp_name'];
    $extensao = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));

    if ($extensao !== 'csv') {
        throw new Exception("Apenas arquivos CSV são aceitos.");
    }

    // === Apaga registros antigos ===
    $conexao->exec("DELETE FROM pr_lote_minimo");

    // === Abre arquivo e detecta delimitador ===
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

    // === Prepara INSERT ===
    $sql = "INSERT INTO pr_lote_minimo 
        (emp, uni, polo, uni_fabril, bitola, formato, tipologia, un, acabamento, descricao, situacao, Lote, lote_alternativo1, lote_alternativo2)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);

    $contador = 0;

    // === Lê cada linha ===
    while (($linha = fgetcsv($handle, 0, $delimitador)) !== false) {
        if (empty(array_filter($linha))) continue;
        $linha = array_pad($linha, 13, null);
        $stmt->execute($linha);
        $contador++;
    }

    fclose($handle);

    // === Salva histórico ===
    $stmtHist = $conexao->prepare("
        INSERT INTO pr_lote_minimo_historico (usuario_id, nome_arquivo, quantidade_registros, observacao)
        VALUES (?, ?, ?, ?)
    ");
    $stmtHist->execute([
        $_SESSION['usuario_id'] ?? 0,
        $_FILES['arquivo']['name'],
        $contador,
        'Carga substituiu os dados anteriores.'
    ]);

    header("Location: proposta_lote.php?upload=ok&count={$contador}");
    exit;


} catch (Exception $e) {
    // Log de erro para análise
    error_log("Erro ao processar arquivo de lote mínimo: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());

    // Mensagem genérica para o usuário
    $_SESSION['flash_error'] =  "Ocorreu um erro ao processar a operação. Por favor, tente novamente ou contate o administrador.";

    // Redireciona de volta para a página de lote
    header("Location: proposta_lote.php");
    exit;
}

// Se não estiver logado, redireciona para a página de validação
else:
    $link = 'http://localhost:8080/proposta_cadastro.php';
    $link = base64_encode($link);
    #header('Location: https://ww1.eliane.com/valida/?link=' . $link);
    header('Location: https://ww1.eliane.com/valida/?link=' . $link);
    exit;
endif;

