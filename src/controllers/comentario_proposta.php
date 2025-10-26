<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include(__DIR__ . '/../config/conexao.php');

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
