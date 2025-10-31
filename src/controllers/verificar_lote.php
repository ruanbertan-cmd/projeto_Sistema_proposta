<?php
// Caminho do CSV
$caminhoCsv = __DIR__ . '/../data/produtos.csv';

// Dados vindos do formulário
$polo = $_POST['polo'] ?? null;
$tipologia = $_POST['tipologia'] ?? null;
$unidade_medida = $_POST['unidade_medida'] ?? null;
$formato = $_POST['formato'] ?? null;
$volume = (int) ($_POST['volume'] ?? 0);

// Função para identificar a unidade (com base em Emp e Uni)
function identificarUnidade($emp, $uni)
{
    if ($emp == 1 && in_array($uni, [1, 31, 63, 198, 192])) {
        return "SC";
    } elseif ($emp == 42 && $uni == 1) {
        return "PB";
    } elseif ($emp == 13 && $uni == 1) {
        return "BA";
    } else {
        return "OUTRA";
    }
}

// Abre e lê o CSV
if (($handle = fopen($caminhoCsv, 'r')) !== false) {
    // Ignora o cabeçalho
    fgetcsv($handle, 1000, ';');

    $aviso = null;

    while (($dados = fgetcsv($handle, 1000, ';')) !== false) {
        // Estrutura esperada do CSV:
        // Emp;Uni;;Bitola;Formato;Descricao;Sit;Lote;Lote Alternativo 1;Lote Alternativo 2
        // (ajustando conforme sua planilha)
        $emp = trim($dados[0] ?? '');
        $uni = trim($dados[1] ?? '');
        $formatoBase = trim($dados[4] ?? '');
        $tipologiaBase = trim($dados[5] ?? '');
        $loteMinimo = (int) trim($dados[7] ?? 0);

        // Verifica correspondência entre polo, formato e tipologia
        if (
            identificarUnidade($emp, $uni) === $polo &&
            strcasecmp($formatoBase, $formato) === 0 &&
            strcasecmp($tipologiaBase, $tipologia) === 0
        ) {
            if ($volume < $loteMinimo) {
                $aviso = "⚠️ O volume solicitado ($volume) é inferior ao lote mínimo ($loteMinimo) 
                          para o formato $formato / tipologia $tipologia ($polo).";
            }
            break;
        }
    }

    fclose($handle);

    // Exibe resultado
    if ($aviso) {
        echo "<p style='color:red;'>$aviso</p>";
    } else {
        echo "<p style='color:green;'>✔ Volume atende o lote mínimo.</p>";
    }
} else {
    echo "<p style='color:red;'>Erro ao abrir o arquivo CSV.</p>";
}
