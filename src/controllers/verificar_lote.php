<?php
function verificarLoteMinimoCSV($empresa, $unidade, $formato, $volume)
{
    // Caminho do CSV (exemplo)
    $caminho_csv = __DIR__ . '/../../dados/lotes.csv'; // coloque onde realmente está seu CSV

    if (!file_exists($caminho_csv)) {
        return [
            'status' => false,
            'mensagem' => 'Arquivo CSV de lotes não encontrado.'
        ];
    }

    if (($handle = fopen($caminho_csv, 'r')) !== false) {
        $cabecalho = fgetcsv($handle, 1000, ';'); // lê a primeira linha (cabeçalho)
        while (($linha = fgetcsv($handle, 1000, ';')) !== false) {
            $dados = array_combine($cabecalho, $linha);

            // Normaliza para evitar problemas com maiúsculas e espaços
            $empresaCSV = trim($dados['empresa']);
            $unidadeCSV = trim($dados['unidade']);
            $formatoCSV = strtoupper(trim($dados['formato']));
            $loteMinimo = floatval(str_replace(',', '.', $dados['lote_minimo']));

            // Verifica se corresponde à empresa, unidade e formato
            if ($empresaCSV == $empresa && $unidadeCSV == $unidade && $formatoCSV == strtoupper($formato)) {
                fclose($handle);
                if ($volume < $loteMinimo) {
                    return [
                        'status' => false,
                        'mensagem' => "O volume informado ($volume) é menor que o lote mínimo ($loteMinimo) para o formato {$formato}."
                    ];
                } else {
                    return ['status' => true];
                }
            }
        }
        fclose($handle);
    }

    return [
        'status' => true // se não encontrar registro, consideramos ok
    ];
}
