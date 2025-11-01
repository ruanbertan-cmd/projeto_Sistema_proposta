<?php
// public/actions.php
session_start();

if (!isset($_GET['action']) || !isset($_GET['id'])) {
    die("Parâmetros inválidos.");
}

$action = $_GET['action'];
$id = intval($_GET['id']);

// Inclui os controllers
switch($action) {
    case 'aprovar':
        include(__DIR__ . '/../src/controllers/aprovar_proposta.php');
        break;
    case 'rejeitar':
        include(__DIR__ . '/../src/controllers/rejeitar_proposta.php');
        break;
    case 'comentario':
        include(__DIR__ . '/../src/controllers/comentario_proposta.php');
        break;
    case 'lote':
        include(__DIR__ . '/../src/controllers/verificar_lote.php');
        break;
    default:
        die("Ação inválida.");
}
