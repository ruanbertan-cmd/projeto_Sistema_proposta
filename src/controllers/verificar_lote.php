<?php
header('Content-Type: application/json; charset=utf-8');

// Caminho do CSV
$arquivoCSV = __DIR__ . '/../data/produtos.csv';

// Recebe os dados enviados pelo formulário
$codigoEmpresa = $_POST['codigo_empresa'] ?? null;
$codigoUnidade = $_POST['codigo_unidade'] ?? null;
$formato       = $_POST['formato'] ?? null;
$loteInformado = (int) ($_POST['lote'] ?? 0);

if (!$codigoEmpresa || !$codigoUnidade || !$formato || !$loteInformado) {
    echo json_encode(['erro' => 'Dados insuficientes para análise.']);
    exit;
}

if (!file_exists($arquivoCSV)) {
    echo json_encode(['erro' => 'Arquivo de base (produtos.csv) não encontrado.']);
    exit;
}

$loteMinimoEncontrado = null;

// Abre e lê o CSV
if (($handle = fopen($arquivoCSV, 'r')) !== false) {
    $cabecalho = fgetcsv($handle, 1000, ';'); // Lê a primeira linha (cabeçalho)

    while (($linha = fgetcsv($handle, 1000, ';')) !== false) {
        // Mapeia a linha de acordo com o cabeçalho
        $dados = array_combine($cabecalho, $linha);

        // Normaliza os campos (removendo espaços e letras maiúsculas/minúsculas)
        $emp = trim($dados['Emp']);
        $uni = trim($dados['Uni']);
        $form = trim($dados['Formato']);
        $loteMin = (int) preg_replace('/\D/', '', $dados['Lote']); // garante que seja numérico

        if ($emp == $codigoEmpresa && $uni == $codigoUnidade && strcasecmp($form, $formato) == 0) {
            $loteMinimoEncontrado = $loteMin;
            break;
        }
    }

    fclose($handle);
}

// Resposta de acordo com o resultado
if ($loteMinimoEncontrado !== null) {
    if ($loteInformado < $loteMinimoEncontrado) {
        echo json_encode([
            'status' => 'alerta',
            'mensagem' => "O lote informado ({$loteInformado}) é menor que o mínimo ({$loteMinimoEncontrado}) para este formato e unidade."
        ]);
    } else {
        echo json_encode([
            'status' => 'ok',
            'mensagem' => "O lote informado ({$loteInformado}) atende ao mínimo ({$loteMinimoEncontrado})."
        ]);
    }
} else {
    echo json_encode([
        'status' => 'nao_encontrado',
        'mensagem' => "Não foi encontrado um registro correspondente (Empresa {$codigoEmpresa}, Unidade {$codigoUnidade}, Formato {$formato})."
    ]);
}
?>