<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

// 🕒 Corrige definitivamente o fuso horário
date_default_timezone_set('America/Sao_Paulo');
$conexao->exec("SET time_zone = '-03:00'");

// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lote Mínimo</title>
<!-- SweetAlert2 CSS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* ===== Reset ===== */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }

    body {
        background: linear-gradient(135deg, #7d7d7dff, #d3d3d3ff);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        color: #333;
    }

    header.barra_navegacao {
        background-color: #9a9a9a;
        padding: 15px 0;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        top: 0;
        width: 100%;
        z-index: 1000;
        position: sticky;
    }

    .navbar_container ul {
        list-style: none;
        display: flex;
        justify-content: center;
        gap: 40px;
    }

    .navbar_container a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: 0.2s;
    }

    .navbar_container a:hover { text-decoration: underline; }

    main.main_proposta_fases {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 40px 20px;
    }

    .tabela_container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 1400px;
        overflow-x: auto;
        padding: 20px;
        animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h1 {
        text-align: center;
        color: #3a3a3a;
        margin-bottom: 25px;
    }

    table.tabela_propostas {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #999;
        border-radius: 6px;
        overflow: hidden;
    }

    table.tabela_propostas th, table.tabela_propostas td {
        padding: 12px 10px;
        text-align: center;
        border-bottom: 1px solid #ccc;
        font-size: 0.7rem;
    }

    table.tabela_propostas th {
        background-color: #666;
        color: white;
        font-weight: 600;
    }

    table.tabela_propostas tr:nth-child(even) { background-color: #f5f5f5; }
    table.tabela_propostas tr:hover { background-color: #e0e0e0; }

    table.tabela_propostas a {
        text-decoration: none;
        color: white;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 0.85rem;
        transition: 0.2s;
    }

    a.aprovar { background-color: #848484ff; }
    a.aprovar:hover { background-color: #656565ff; }

    a.rejeitar { background-color: #aeadadff; }
    a.rejeitar:hover { background-color: #a1a1a1ff; }

    a.detalhes { background-color: #aeadadff; }
    a.detalhes:hover { background-color: #a1a1a1ff; }

    strong { font-weight: bold; }

    @media (max-width: 768px) {
        .tabela_container { padding: 15px; }
        table.tabela_propostas th, table.tabela_propostas td { padding: 8px; font-size: 0.8rem; }
    }
</style>
</head>
<body>

<header class="barra_navegacao">
    <nav class="navbar">
        <div class="navbar_container">
            <ul>
                <li><a href="proposta_cadastro.php">Cadastro</a></li>
                <li><a href="proposta_consulta.php">Consulta</a></li>
                <li><a href="proposta_aprovacao.php">Aprovação</a></li>
                <li><a href="proposta_lote.php">Lote</a></li>
            </ul>
        </div>
    </nav>
</header>

<main class="main_proposta_fases">
    <div class="tabela_container">
        <h1>Lista de Lote Mínimo Sendo Considerada</h1>
        <table class="tabela_propostas">
            <tr>
                <th>Emp</th>
                <th>Uni</th>
                <th>Polo</th>
                <th>Uni_fabril</th>
                <th>bitola</th>
                <th>Formato</th>
                <th>Tipologia</th>
                <th>Un</th>
                <th>Descrição</th>
                <th>Situação</th>
                <th>Lote</th>
                <th>lote_alternativo1</th>
                <th>lote_alternativo2</th>
            </tr>
        </table>
    </div>
</main>
</body>
</html>
