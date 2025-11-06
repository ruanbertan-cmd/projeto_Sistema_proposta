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
    $caminhoCsv = realpath(__DIR__ . '/../data/lote_minimo.xlsx');
    if ($caminhoCsv === false || !file_exists($caminhoCsv)) {
        return [
            'status' => false,
            'mensagem' => "❌ Arquivo de lote mínimo não encontrado no sistema. Contate o suporte."
        ];
    }

    if (($handle = fopen($caminhoCsv, 'r')) === false) {
        return [
            'status' => false,
            'mensagem' => "❌ Não foi possível abrir o arquivo de lote mínimo. Tente novamente."
        ];
    }

    fgetcsv($handle, 1000, ';'); // Ignora cabeçalho

    $formatosNaPlanilha = [];
    $poloEncontrado = false;
    $tipologiaEncontrada = false;
    $unidadeEncontrada = false;

    // Primeiro, vamos mapear todos os formatos disponíveis
    while (($dados = fgetcsv($handle, 1000, ';')) !== false) {
        $formatoBase = strtoupper(trim($dados[4] ?? '')); // coluna do formato
        if ($formatoBase !== '') {
            $formatosNaPlanilha[] = $formatoBase;
        }
    }

    // Volta o ponteiro pro início do arquivo para fazer a leitura normal
    rewind($handle);
    fgetcsv($handle, 1000, ';'); // Ignora cabeçalho novamente

    // Se o formato informado não existir na planilha → aceitar automaticamente
    if (!in_array(strtoupper($formato), $formatosNaPlanilha)) {
        fclose($handle);
        return [
            'status' => true,
            'mensagem' => "Considerando o Formato '$formato' como personalização de corte. Cadastro liberado."
        ];
    }

    // Percorre o CSV normalmente, agora sabendo que o formato existe
    while (($dados = fgetcsv($handle, 1000, ';')) !== false) {
        $emp = trim($dados[0] ?? '');
        $uni = trim($dados[1] ?? '');
        $formatoBase = strtoupper(trim($dados[4] ?? ''));
        $tipologiaBase = strtoupper(trim($dados[6] ?? ''));
        $unBase = strtoupper(trim($dados[7] ?? ''));
        $loteMinimo = (float) str_replace(',', '.', trim($dados[10] ?? 0));
        $poloPlanilha = identificarUnidade($emp, $uni);

        // Marca o que foi encontrado
        if ($poloPlanilha === strtoupper($polo)) $poloEncontrado = true;
        if ($tipologiaBase === strtoupper($tipologia)) $tipologiaEncontrada = true;
        if ($unBase === strtoupper($unidade_medida)) $unidadeEncontrada = true;

        // Valida a combinação completa (agora considerando também o formato)
        if (
            $poloPlanilha === strtoupper($polo) &&
            $formatoBase === strtoupper($formato) &&
            strcasecmp($tipologiaBase, $tipologia) === 0 &&
            ($unidade_medida === null || strcasecmp($unBase, $unidade_medida) === 0)
        ) {
            fclose($handle);

            // Verifica volume
            if ($volume < $loteMinimo) {
                return [
                    'status' => false,
                    'mensagem' => "⚠️ O volume informado ($volume $unBase) é inferior ao lote mínimo exigido ($loteMinimo $unBase) para $formato - $tipologia ($polo)."
                ];
            }

            return [
                'status' => true,
                'mensagem' => "✅ Volume dentro do lote mínimo ($loteMinimo $unBase)."
            ];
        }
    }

    fclose($handle);

    // Monta mensagem específica conforme o que não foi encontrado
    if (!$poloEncontrado) {
        $motivo = "❌ Polo '$polo' não disponível para produção.";
    } elseif (!$tipologiaEncontrada) {
        $motivo = "❌ Tipologia '$tipologia' não disponível no polo '$polo'.";
    } elseif (!$unidadeEncontrada) {
        $motivo = "❌ Unidade de medida '$unidade_medida' não utilizada para '$tipologia' no polo '$polo'.";
    } else {
        $motivo = "❌ Regra de produção não encontrada para a combinação informada.";
    }

    return [
        'status' => false,
        'mensagem' => "$motivo Produção inviável, cadastro bloqueado."
    ];
}
