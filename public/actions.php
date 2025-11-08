<?php
session_start();

$action = $_GET['action'] ?? null;

switch ($action) {
    case 'aprovar':
        include(__DIR__ . '/../src/controllers/aprovar_proposta.php');
        break;

    case 'rejeitar':
        include(__DIR__ . '/../src/controllers/rejeitar_proposta.php');
        break;

    case 'comentario':
        include(__DIR__ . '/../src/controllers/comentario_proposta.php');
        break;

    case 'upload_lote':
        include(__DIR__ . '../src/controllers/upload_lote.php');
        break;

    case 'verificar_lote':
        include(__DIR__ . '/../src/controllers/verificar_lote.php');
        break;

    default:
        echo "Ação inválida.";
        break;
}
?>
