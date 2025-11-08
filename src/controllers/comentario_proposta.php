<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../config/conexao.php');

//  Corrige definitivamente o fuso horário
date_default_timezone_set('America/Sao_Paulo');
$conexao->exec("SET time_zone = '-03:00'");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comentario = trim($_POST['comentario_Lib_Produto'] ?? '');
    $id = intval($_GET['id']); // ID vindo pela URL

    if (!empty($comentario)) {
        $stmt = $conexao->prepare("
            UPDATE formulario 
            SET comentario_Lib_Produto = :comentario 
            WHERE id = :id
        ");
        $stmt->execute([
            ':comentario' => $comentario,
            ':id' => $id
        ]);
    }

    // Redireciona de volta para os detalhes da proposta
    header("Location: proposta_detalhes.php?id=$id");
    exit;
}
?>
