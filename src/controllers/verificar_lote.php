<?php
include(__DIR__ . '/../config/conexao.php');
include(__DIR__ . '/../functions/verificar_lote_func.php');

//  Corrige definitivamente o fuso horário
date_default_timezone_set('America/Sao_Paulo');
$conexao->exec("SET time_zone = '-03:00'");

$polo = $_POST['polo'] ?? '';
$tipologia = $_POST['tipologia'] ?? '';
$formato = $_POST['formato'] ?? '';
$volume = floatval($_POST['volume'] ?? 0);
$unidade_medida = $_POST['unidade_medida'] ?? null;

$result = verificarLoteMinimoCSV($polo, $tipologia, $formato, $volume, $unidade_medida);
echo json_encode($result);
