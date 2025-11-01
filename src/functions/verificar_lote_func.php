<?php
function identificarUnidade($emp, $uni) {
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

function verificarLoteMinimoCSV($polo, $tipologia, $formato, $volume, $unidade_medida = null) {
    $caminhoCsv = realpath(__DIR__ . '/../data/produtos_lote_minimo.csv');
    if ($caminhoCsv === false || !file_exists($caminhoCsv)) {
        return [
            'status' => false,
            'mensagem' => "❌ Arquivo CSV não encontrado."
        ];
    }

    if (($handle = fopen($caminhoCsv, 'r')) === false) {
        return [
            'status' => false,
            'mensagem' => "❌ Erro ao abrir o arquivo CSV."
        ];
    }

    fgetcsv($handle, 1000, ';'); // Ignora cabeçalho

    while (($dados = fgetcsv($handle, 1000, ';')) !== false) {
        $emp = trim($dados[0] ?? '');
        $uni = trim($dados[1] ?? '');
        $formatoBase = strtoupper(trim($dados[4] ?? ''));
        $tipologiaBase = strtoupper(trim($dados[5] ?? ''));
        $unBase = strtoupper(trim($dados[6] ?? ''));
        $loteMinimo = (float) str_replace(',', '.', trim($dados[9] ?? 0));

        if (
            identificarUnidade($emp, $uni) === strtoupper($polo) &&
            strcasecmp($formatoBase, $formato) === 0 &&
            strcasecmp($tipologiaBase, $tipologia) === 0 &&
            ($unidade_medida === null || strcasecmp($unBase, $unidade_medida) === 0)
        ) {
            fclose($handle);
            if ($volume < $loteMinimo) {
                return [
                    'status' => false,
                    'mensagem' => "⚠️ O volume solicitado ($volume $unBase) é inferior ao lote mínimo ($loteMinimo $unBase) para $formato - $tipologia ($polo)."
                ];
            }
            return [
                'status' => true,
                'mensagem' => "✔ Volume atende o lote mínimo ($loteMinimo $unBase)."
            ];
        }
    }

    fclose($handle);
    return [
        'status' => true,
        'mensagem' => "✔ Nenhum lote mínimo específico encontrado, volume aceito."
    ];
}
