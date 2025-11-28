<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/conexao.php';

//  Corrige definitivamente o fuso horário
date_default_timezone_set('America/Sao_Paulo');
$conexao->exec("SET time_zone = '-03:00'");

// Captura o ID da proposta
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id) {
    $_SESSION['flash_error'] = 'ID da proposta inválido.';
    header('Location: proposta_aprovacao.php');
    exit;
}

try {
    // Atualiza o status no banco
    $stmt = $conexao->prepare("
        UPDATE pr_formulario
        SET status = CONCAT('Aprovado ', DATE_FORMAT(NOW(), '%d/%m/%Y'))
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    $_SESSION['flash_success'] = 'Proposta aprovada com sucesso.';
} catch (PDOException $e) {

    // Log de erro para análise
    error_log('Erro ao aprovar proposta (ID ' . $id . '): ' . $e->getMessage());

    // Mensagem genérica para o usuário
    $_SESSION['flash_error'] = 'Erro ao aprovar a proposta. Tente novamente.';
}

// Redireciona para a tela de aprovação
header('Location: proposta_aprovacao.php');
exit;
?>
