<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

// Verifica se o ID foi passado
if(!isset($_GET['id'])) {
    die('ID não informado.');
} 

$id = intval($_GET['id']);

// Atualiza o status no banco de dados

$stmt = $conexao -> prepare("UPDATE formulario SET status = CONCAT('Aprovado ',DATE_FORMAT(NOW(), '%d/%m/%Y')) WHERE id = :id");
$stmt -> execute([':id' => $id]);

// Redirecionando para a listagem
header('location: proposta_fases.php');
exit;
?>