<?php
include_once('conexao.php');

// Verifica se o ID foi passado
if(!isset($_GET['id'])) {
    die('ID não informado.');
}

$id = intval($_GET['id']);

// Atualiza o status no banco de dados

$stmt = $conexao -> prepare("UPDATE formulario SET status = 'Rejeitado' WHERE id = :id");
$stmt -> execute([':id' => $id]);

// Redirecionando para a listagem
header('localization: proposta_fases.php');
exit;
?>