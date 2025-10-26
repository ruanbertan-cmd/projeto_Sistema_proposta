<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comentario = trim($_POST['comentario_Lib_Produto'] ?? '');
    $id = intval($_GET['id']); // o ID do produto vindo pela URL

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
header("location: proposta_detalhes.php?id=" . intval($_GET['id']));
exit;
}
?>