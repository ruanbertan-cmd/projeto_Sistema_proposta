<?php
function verificarLoteMinimoDB($conexao, $polo, $tipologia, $formato, $volume, $un)
{
    // Normaliza campos
    $polo = mb_strtoupper(trim($polo));
    $tipologia = mb_strtoupper(trim($tipologia));
    $formato = mb_strtoupper(trim($formato));
    $un = mb_strtoupper(trim($un));
    $volume = floatval($volume);

    // Busca por formato
    $stmtFormato = $conexao->prepare("SELECT * FROM lote_minimo WHERE formato = ?");
    $stmtFormato->execute([$formato]);
    $dadosFormato = $stmtFormato->fetch(PDO::FETCH_ASSOC);

    // Busca pelo conjunto polo + tipologia + un
    $stmtConjunto = $conexao->prepare("
        SELECT * FROM lote_minimo
        WHERE polo = ? AND tipologia = ? AND un = ?
    ");
    $stmtConjunto->execute([$polo, $tipologia, $un]);
    $dadosConjunto = $stmtConjunto->fetch(PDO::FETCH_ASSOC);

    // Busca completa (tudo certo)
    $stmtCompleto = $conexao->prepare("
        SELECT * FROM lote_minimo
        WHERE polo = ? AND tipologia = ? AND formato = ? AND un = ?
    ");
    $stmtCompleto->execute([$polo, $tipologia, $formato, $un]);
    $dadosCompleto = $stmtCompleto->fetch(PDO::FETCH_ASSOC);

    // Situação 1 - Formato não encontrado, mas o resto ok (corte)
    if (!$dadosFormato && $dadosConjunto) {
        return [
            'status' => true,
            'mensagem' => "Ok, proposta cadastrada considerando como formato de corte!"
        ];
    }

    // Situação 2 - Tudo OK
    if ($dadosCompleto) {
        $loteMinimo = floatval($dadosCompleto['lote']);
        if ($volume >= $loteMinimo) {
            return [
                'status' => true,
                'mensagem' => "Ok, volume atende o lote mínimo de {$loteMinimo} {$un}."
            ];
        } else {
            // Situação 3 - Volume inferior
            return [
                'status' => false,
                'mensagem' => "Bloqueado, volume inferior ao lote mínimo de {$loteMinimo} {$un} para {$formato} - {$tipologia} ({$polo})."
            ];
        }
    }

    // Situação 4 - Tipologia errada (não há correspondência no polo/formato)
    $stmtTipologia = $conexao->prepare("
        SELECT * FROM lote_minimo WHERE formato = ? AND un = ? AND polo = ?
    ");
    $stmtTipologia->execute([$formato, $un, $polo]);
    $dadosTipologia = $stmtTipologia->fetch(PDO::FETCH_ASSOC);

    if (!$dadosTipologia) {
        return [
            'status' => false,
            'mensagem' => "Bloqueado, tipologia {$tipologia} não está presente para o formato {$formato} e unidade {$un} no polo {$polo}."
        ];
    }

    // Situação 5 - Unidade errada
    $stmtUn = $conexao->prepare("
        SELECT * FROM lote_minimo WHERE formato = ? AND tipologia = ? AND polo = ?
    ");
    $stmtUn->execute([$formato, $tipologia, $polo]);
    $dadosUn = $stmtUn->fetch(PDO::FETCH_ASSOC);
    if (!$dadosUn) {
        return [
            'status' => false,
            'mensagem' => "Bloqueado, não temos a unidade de medida {$un} ao conjunto de Formato {$formato}, Tipologia {$tipologia} no polo {$polo}."
        ];
    }

    // Situação 6 - Tipologia e Unidade erradas
    if (!$dadosConjunto && $dadosFormato) {
        return [
            'status' => false,
            'mensagem' => "Bloqueado, Tipologia {$tipologia} e unidade de medida {$un} informadas não existem dentro do polo {$polo} e formato {$formato}."
        ];
    }

    // Situação 7 - Polo e Unidade erradas
    $stmtPoloUn = $conexao->prepare("
        SELECT * FROM lote_minimo WHERE formato = ? AND tipologia = ?
    ");
    $stmtPoloUn->execute([$formato, $tipologia]);
    $dadosPoloUn = $stmtPoloUn->fetch(PDO::FETCH_ASSOC);
    if (!$dadosPoloUn) {
        return [
            'status' => false,
            'mensagem' => "Bloqueado, Polo {$polo} e unidade de medida {$un} informados não estão presentes no conjunto Formato {$formato} e Tipologia {$tipologia}."
        ];
    }

    // Situação 8 - Polo e Tipologia errados
    $stmtPoloTip = $conexao->prepare("
        SELECT * FROM lote_minimo WHERE formato = ? AND un = ?
    ");
    $stmtPoloTip->execute([$formato, $un]);
    $dadosPoloTip = $stmtPoloTip->fetch(PDO::FETCH_ASSOC);
    if (!$dadosPoloTip) {
        return [
            'status' => false,
            'mensagem' => "Bloqueado, Polo {$polo} e tipologia {$tipologia} informados não estão presentes no conjunto Formato {$formato} e Unidade de Medida {$un}."
        ];
    }

    // Se nada combinar — genérico
    return [
        'status' => false,
        'mensagem' => "Bloqueado, combinação de dados não encontrada na base de lote mínimo."
    ];
}
