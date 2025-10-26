<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../config/conexao.php');

// Atualiza o status no banco de dados

$stmt = $conexao -> prepare("UPDATE formulario SET status = CONCAT('Rejeitado ',DATE_FORMAT(NOW(), '%d/%m/%Y')) WHERE id = :id");
$stmt -> execute([':id' => $id]);

// Redirecionando para a listagem
header('location: proposta_fases.php');
exit;
?>