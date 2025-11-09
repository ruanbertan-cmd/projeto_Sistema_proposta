<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

// Verifica se o usuário já está logado
if (isset($_SESSION['usuario']) && !empty($_SESSION['usuario']['login_usuario'])):
    header('Location: proposta_cadastro.php');
    exit;

// Se não estiver logado, redireciona para a página de validação
else:
    $link = 'https://ww1.eliane.com/ruan/proposta_cadastro.php';
    $link = base64_encode($link);
    header('Location: https://ww1.eliane.com/valida/?link=' . $link);
    exit;
endif;
?>
