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

    default:
        echo "Ação inválida.";
        break;
}
?>
