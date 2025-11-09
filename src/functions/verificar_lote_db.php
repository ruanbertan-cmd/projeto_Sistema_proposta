<?php
function verificarLoteMinimoDB($conexao, $polo, $tipologia, $formato, $acabamento, $volume, $un)
{   
    // Normaliza campos
    $polo = mb_strtoupper(trim($polo));
    $tipologia = mb_strtoupper(trim($tipologia));
    $formato = mb_strtoupper(trim($formato));
    $acabamento = mb_strtoupper(trim($acabamento));
    $un = mb_strtoupper(trim($un));
    $volume = floatval($volume);

    // Ajustes específicos para acabamento (Divisão de AC e PO)
    $acabamentoAjustado = match ($acabamento) {
        'POLIDO' => 'PO',
        'ACETINADO', 'BRILHANTE', 'NATURAL', 'MATE', 'RESISTENTE AO ESCORREGAMENTO', 'METALIZADO', 'FLAMEADO', 'RESISTENTE AOS ACIDOS', 'DUO' => 'AC',
        default => $acabamento,
    };

    // 1️⃣ Busca por formato (para exceção de corte)
    $stmtFormato = $conexao->prepare("SELECT * FROM lote_minimo WHERE formato = ?");
    $stmtFormato->execute([$formato]);
    $dadosFormato = $stmtFormato->fetch(PDO::FETCH_ASSOC);

    // 2️⃣ Busca pelo conjunto polo + tipologia + un + acabamento
    $stmtConjunto = $conexao->prepare("
        SELECT * FROM lote_minimo
        WHERE polo = ? AND tipologia = ? AND un = ? AND acabamento = ?
    ");
    $stmtConjunto->execute([$polo, $tipologia, $un, $acabamentoAjustado]);
    $dadosConjunto = $stmtConjunto->fetch(PDO::FETCH_ASSOC);

    // 3️⃣ Busca completa (todos os campos)
    $stmtCompleto = $conexao->prepare("
        SELECT * FROM lote_minimo
        WHERE polo = ? AND tipologia = ? AND formato = ? AND acabamento = ? AND un = ?
    ");
    $stmtCompleto->execute([$polo, $tipologia, $formato, $acabamentoAjustado, $un]);
    $dadosCompleto = $stmtCompleto->fetch(PDO::FETCH_ASSOC);

    // Situação 1 - Formato não encontrado (exceção de corte)
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
                'mensagem' => "Bloqueado, volume inferior ao lote mínimo de {$loteMinimo} {$un} para {$formato} - {$tipologia} ({$polo}) acabamento {$acabamento}."
            ];
        }
    }

    // Situação 4 - Tipologia errada
    $stmtTipologia = $conexao->prepare("
        SELECT * FROM lote_minimo WHERE formato = ? AND un = ? AND polo = ? AND acabamento = ?
    ");
    $stmtTipologia->execute([$formato, $un, $polo, $acabamentoAjustado]);
    if (!$stmtTipologia->fetch(PDO::FETCH_ASSOC)) {
        return [
            'status' => false,
            'mensagem' => "Bloqueado, tipologia {$tipologia} não está presente para o formato {$formato}, unidade {$un}, acabamento {$acabamento} no polo {$polo}."
        ];
    }

    // Situação 5 - Unidade errada
    $stmtUn = $conexao->prepare("
        SELECT * FROM lote_minimo WHERE formato = ? AND tipologia = ? AND polo = ? AND acabamento = ?
    ");
    $stmtUn->execute([$formato, $tipologia, $polo, $acabamentoAjustado]);
    if (!$stmtUn->fetch(PDO::FETCH_ASSOC)) {
        return [
            'status' => false,
            'mensagem' => "Bloqueado, não temos a unidade de medida {$un} no conjunto Formato {$formato}, Tipologia {$tipologia}, Polo {$polo}, Acabamento {$acabamento}."
        ];
    }

    // Situação 6 - Acabamento errado
    $stmtAcab = $conexao->prepare("
        SELECT * FROM lote_minimo WHERE formato = ? AND tipologia = ? AND polo = ? AND un = ?
    ");
    $stmtAcab->execute([$formato, $tipologia, $polo, $un]);
    if (!$stmtAcab->fetch(PDO::FETCH_ASSOC)) {
        return [
            'status' => false,
            'mensagem' => "Bloqueado, o acabamento {$acabamento} informado não possui combinação válida com Tipologia {$tipologia}, Formato {$formato}, Unidade {$un} e Polo {$polo}."
        ];
    }

    // Situação 7 - Tipologia e Unidade erradas
    $stmtTipUn = $conexao->prepare("
        SELECT * FROM lote_minimo WHERE formato = ? AND polo = ? AND acabamento = ?
    ");
    $stmtTipUn->execute([$formato, $polo, $acabamentoAjustado]);
    if (!$stmtTipUn->fetch(PDO::FETCH_ASSOC)) {
        return [
            'status' => false,
            'mensagem' => "Bloqueado, Tipologia {$tipologia} e unidade de medida {$un} informadas não existem dentro do polo {$polo}, formato {$formato}, acabamento {$acabamento}."
        ];
    }

    // Situação 8 - Polo e Unidade erradas
    $stmtPoloUn = $conexao->prepare("
        SELECT * FROM lote_minimo WHERE formato = ? AND tipologia = ? AND acabamento = ?
    ");
    $stmtPoloUn->execute([$formato, $tipologia, $acabamentoAjustado]);
    if (!$stmtPoloUn->fetch(PDO::FETCH_ASSOC)) {
        return [
            'status' => false,
            'mensagem' => "Bloqueado, Polo {$polo} e unidade de medida {$un} não estão presentes no conjunto Formato {$formato}, Tipologia {$tipologia}, Acabamento {$acabamento}."
        ];
    }

    // Situação 9 - Polo e Tipologia errados
    $stmtPoloTip = $conexao->prepare("
        SELECT * FROM lote_minimo WHERE formato = ? AND un = ? AND acabamento = ?
    ");
    $stmtPoloTip->execute([$formato, $un, $acabamentoAjustado]);
    if (!$stmtPoloTip->fetch(PDO::FETCH_ASSOC)) {
        return [
            'status' => false,
            'mensagem' => "Bloqueado, Polo {$polo} e tipologia {$tipologia} não estão presentes no conjunto Formato {$formato}, Unidade {$un}, Acabamento {$acabamento}."
        ];
    }

    // Situação 10+ - Genérica
    return [
        'status' => false,
        'mensagem' => "Bloqueado, combinação de dados não encontrada (incluindo acabamento {$acabamento})."
    ];
}
?>