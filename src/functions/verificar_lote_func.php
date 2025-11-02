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

    $regraDeLoteDeEntrada = false; // Inicializa

    // Variável para rastrear se encontramos a regra de lote mínimo para a tríade
    while (($dados = fgetcsv($handle, 1000, ';')) !== false) {
        $emp = trim($dados[0] ?? '');
        $uni = trim($dados[1] ?? '');
        $tipologiaBase = strtoupper(trim($dados[5] ?? ''));
        $unBase = strtoupper(trim($dados[6] ?? ''));
        $loteMinimo = (float) str_replace(',', '.', trim($dados[9] ?? 0));

        // Checagem apenas das informações Polo, Tipologia e Unidade. Se não tiver no CSV não cadastra
        if (
            identificarUnidade($emp, $uni) === strtoupper($polo) &&
            strcasecmp($tipologiaBase, $tipologia) === 0 &&
            ($unidade_medida === null || strcasecmp($unBase, $unidade_medida) === 0)
        ) {
            fclose($handle);

            // Se encontrou a regra, testa o volume
            if ($volume < $loteMinimo) {
                return [
                    'status' => false,
                    'mensagem' => "⚠️ O volume solicitado ($volume $unBase) é inferior ao lote mínimo ($loteMinimo $unBase) para $formato - $tipologia ($polo)."
                ];
            }

            // Volume OK, retorna SUCESSO e ENCERRA
            return [
                'status' => true,
                'mensagem' => "✅ Volume atende o lote mínimo ($loteMinimo $unBase)."
            ];
        }
    }

    // Se chegou aqui nenhuma função foi encontrada após percorrer todo o CSV
    fclose($handle);

    // Bloqueia se a regra não for encontrada (Polo + Tipologia + Unidade)
    if ($regraDeLoteDeEntrada === false) {
        return [
            'status' => false,
            'mensagem' => "Produção inviável, não conseguimos realizar a produção no Polo ($polo), Tipologia ($tipologia) e Unidade ($unidade_medida). Cadastro bloqueado."
        ];
    }

    return [
        'status' => true,
        'mensagem' => "✅ Validação de lote completa."
    ];
}
