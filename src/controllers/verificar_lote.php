<?php
include(__DIR__ . '/../config/conexao.php');
include(__DIR__ . '/../functions/verificar_lote_func.php');

$polo = $_POST['polo'] ?? '';
$tipologia = $_POST['tipologia'] ?? '';
$formato = $_POST['formato'] ?? '';
$volume = floatval($_POST['volume'] ?? 0);
$unidade_medida = $_POST['unidade_medida'] ?? null;

$result = verificarLoteMinimoCSV($polo, $tipologia, $formato, $volume, $unidade_medida);
echo json_encode($result);
