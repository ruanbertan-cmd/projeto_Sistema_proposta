<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

// 🕒 Corrige definitivamente o fuso horário
date_default_timezone_set('America/Sao_Paulo');
$conexao->exec("SET time_zone = '-03:00'");

// 🔒 Garantir que o usuário está logado
$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    header('Location: login.php');
    exit;
}

// === Sanitiza o ID da proposta ===
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID inválido.");
}

// === Busca dados da proposta ===
$stmt = $conexao->prepare("
    SELECT id, volume, unidade_medida, polo, formato, tipologia, borda, cor, local_uso, 
           data_previsao, preco, cliente, obra, nome_produto, marca, embalagem, observacao, 
           imagem, status
    FROM formulario 
    WHERE id = ?
");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("Nenhuma proposta encontrada para o ID informado.");
}

$formulario_id = $row['id'];

// === Inserir novo comentário ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comentario_novo'])) {
    $comentario = trim($_POST['comentario_novo']);

    if ($comentario !== '') {
        $stmtInsert = $conexao->prepare("
            INSERT INTO comentarios (formulario_id, usuario_id, comentario, data_hora)
            VALUES (?, ?, ?, NOW())
        ");
        $stmtInsert->execute([$formulario_id, $usuario_id, $comentario]);

        // Redireciona com flag "novo=1" para scroll automático
        header("Location: proposta_detalhes.php?id={$id}&origem=" . ($_GET['origem'] ?? '') . "&novo=1#ultimo-comentario");
        exit;
    }
}

// === Marcar comentários como visualizados pelo usuário ===
$stmtMarcar = $conexao->prepare("
    INSERT INTO comentarios_visualizacao (comentario_id, usuario_id)
    SELECT c.id, :usuario_id
    FROM comentarios c
    LEFT JOIN comentarios_visualizacao cv 
        ON cv.comentario_id = c.id AND cv.usuario_id = :usuario_id
    WHERE c.formulario_id = :formulario_id
      AND cv.id IS NULL
");
$stmtMarcar->execute([
    ':usuario_id' => $usuario_id,
    ':formulario_id' => $formulario_id
]);


// === Carregar comentários ===
$stmtComentarios = $conexao->prepare("
    SELECT 
        c.id,
        c.comentario,
        c.data_hora AS data_hora_br,
        u.usuario AS autor,
        CASE 
            WHEN cv.id IS NULL AND c.usuario_id != ? THEN 1
            ELSE 0
        END AS novo
    FROM comentarios c
    LEFT JOIN usuario u ON u.id = c.usuario_id
    LEFT JOIN comentarios_visualizacao cv 
        ON cv.comentario_id = c.id AND cv.usuario_id = ?
    WHERE c.formulario_id = ?
    ORDER BY c.data_hora ASC
");
$stmtComentarios->execute([$usuario_id, $usuario_id, $formulario_id]);
$comentarios = $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);

// === Marcar comentários como lidos (somente os que ainda não foram vistos) ===
$stmtLidos = $conexao->prepare("
    INSERT IGNORE INTO comentarios_visualizacao (comentario_id, usuario_id)
    SELECT c.id, ?
    FROM comentarios c
    LEFT JOIN comentarios_visualizacao cv 
        ON cv.comentario_id = c.id AND cv.usuario_id = ?
    WHERE c.formulario_id = ? 
      AND c.usuario_id != ? 
      AND cv.id IS NULL
");
$stmtLidos->execute([$usuario_id, $usuario_id, $formulario_id, $usuario_id]);
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes de Propostas</title>
    <style>
        /* ===== Reset ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: linear-gradient(135deg, #7d7d7d, #d3d3d3);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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

        .navbar_container a:hover {
            text-decoration: underline;
        }

        main.main_proposta_detalhes {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px;
        }

        h1 {
            color: #3a3a3a;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* ===== Tabela de detalhes ===== */
        table {
            width: 90%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            margin-bottom: 15px;
            font-size: 0.8rem;
            border: 1px solid #999;
            border-radius: 6px;
            overflow: hidden;
        }

        th, td {
            padding: 9px 15px;
            text-align: left;
            border-bottom: 1px solid #ccc;
            color: #333;
        }

        th {
            background-color: #e0e0e0;
            font-weight: 600;
        }

        td img {
            max-width: 250px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        /* ===== Área de botões ===== */
        .botoes_acoes {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            background-color: #555555ff;
            padding: 10px;
            border-radius: 6px;
        }

        .botoes_acoes a {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: 0.2s;
        }

        .aprovar {
            background-color: #9a9a9a;
            color: white;
        }

        .aprovar:hover {
            background-color: #626364;
        }

        .rejeitar {
            background-color: #d3d3d3;
            color: #333;
        }

        .rejeitar:hover {
            background-color: #b0b0b0;
        }

        /* ===== Formulário de comentário ===== */
        form {
            width: 90%;
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        textarea {
            width: 100%;
            min-height: 80px;
            border: 1px solid #aaa;
            border-radius: 6px;
            padding: 10px;
            resize: vertical;
            font-size: 0.9rem;
            color: #333;
            background-color: #fafafa;
            transition: 0.2s;
        }

        textarea:focus {
            outline: none;
            border-color: #666;
            background-color: #fff;
            box-shadow: 0 0 0 2px #9a9a9a60;
        }

        button[type="submit"] {
            margin-top: 12px;
            background-color: #555;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        button[type="submit"]:hover {
            background-color: #6c6c6c;
            transform: scale(1.03);
        }

        /* ===== Status ===== */
        .status_box {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 6px;
            background-color: #444;
            color: white;
            font-weight: bold;
            font-size: 0.85rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            margin-top: 10px;
        }

        /* ===== Container de comentários ===== */
        .comentarios-container {
            width: 90%;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        /* ===== Cada comentário ===== */
        .comentario-bloco {
            background: #e8e8e8;
            border-left: 4px solid #999;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 10px;
            position: relative;
            transition: all 0.3s ease;
        }

        .comentario-bloco:hover {
            background: #dcdcdc;
        }

        /* ===== Destaque: novo comentário ===== */
        .comentario-bloco.novo-comentario {
            background-color: #cfcfcf;
            border-left-color: #666;
            animation: brilhoCinza 1.8s ease-in-out;
        }

        @keyframes brilhoCinza {
            0% { box-shadow: 0 0 0 rgba(100, 100, 100, 0); }
            50% { box-shadow: 0 0 15px rgba(120, 120, 120, 0.4); }
            100% { box-shadow: 0 0 0 rgba(100, 100, 100, 0); }
        }

        /* Cabeçalho do comentário */
        .comentario-cabecalho {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
            font-size: 0.85rem;
            color: #555;
        }

        .comentario-usuario {
            font-weight: 600;
            color: #222;
        }

        /* Texto do comentário */
        .comentario-texto {
            font-size: 0.9rem;
            color: #333;
            line-height: 1.4;
        }

        /* Selo de “Novo” */
        .etiqueta-novo {
            position: absolute;
            top: -8px;
            right: 10px;
            background-color: #777;
            color: white;
            font-size: 0.75rem;
            font-weight: 500;
            padding: 3px 8px;
            border-radius: 8px;
            animation: fadeOut 4s ease forwards;
            opacity: 1;
        }

        @keyframes fadeOut {
            0%, 70% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-5px); }
        }

        /* ===== Voltar ===== */
        .voltar {
            display: inline-block;
            margin-top: 25px;
            padding: 8px 16px;
            background-color: #e1e1e1;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.2s;
        }

        .voltar:hover {
            background-color: #ccc;
            transform: scale(1.03);
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
        <li><a href="proposta_fases.php">Fases</a></li>
    </ul>
</div>
</nav>
</header>

<main class="main_proposta_detalhes">
<h1>Detalhes da Proposta</h1>

<table>
<tr><th>Volume</th><td><?= htmlspecialchars($row['volume'] . ' ' . $row['unidade_medida']); ?></td></tr>
<tr><th>Formato</th><td><?= htmlspecialchars($row['formato']); ?></td></tr>
<tr><th>Polo</th><td><?= htmlspecialchars($row['polo']); ?></td></tr>
<tr><th>Tipologia</th><td><?= htmlspecialchars($row['tipologia']); ?></td></tr>
<tr><th>Borda</th><td><?= htmlspecialchars($row['borda']); ?></td></tr>
<tr><th>Cor</th><td><?= htmlspecialchars($row['cor']); ?></td></tr>
<tr><th>Local de Uso</th><td><?= htmlspecialchars($row['local_uso']); ?></td></tr>
<tr><th>Data Previsão</th><td><?= htmlspecialchars(date('d/m/Y', strtotime($row['data_previsao']))); ?></td></tr>
<tr><th>Preço</th><td><?= htmlspecialchars($row['preco']); ?></td></tr>
<tr><th>Cliente</th><td><?= htmlspecialchars($row['cliente']); ?></td></tr>
<tr><th>Obra</th><td><?= htmlspecialchars($row['obra']); ?></td></tr>
<tr><th>Nome Produto</th><td><?= htmlspecialchars($row['nome_produto']); ?></td></tr>
<tr><th>Marca</th><td><?= htmlspecialchars($row['marca']); ?></td></tr>
<tr><th>Embalagem</th><td><?= htmlspecialchars($row['embalagem']); ?></td></tr>
<?php if (!empty($row['imagem'])): ?>
<tr>
<th>Imagem Referência</th>
<td><img src="uploads/<?= htmlspecialchars($row['imagem']); ?>" alt="Imagem de referência"></td>
</tr>
<?php endif; ?>
</table>

<!-- Formulário para adicionar comentário -->
<form method="POST">
<textarea name="comentario_novo" placeholder="Escreva um comentário..." required></textarea>
<button type="submit">Salvar comentário</button>
</form>

<!-- Comentários existentes -->
<div class="comentarios-container" style="margin-top: 30px;" id="comentarios">
    <h2 style="color: #444; margin-bottom: 10px;">Comentários</h2>
    <?php if (count($comentarios) > 0): ?>
        <?php foreach ($comentarios as $i => $c): 
            $ultimo = $i === array_key_last($comentarios);
        ?>
            <div id="<?= $ultimo ? 'ultimo-comentario' : '' ?>" style="
                background: <?= $c['novo'] ? '#cfcfcf' : '#e8e8e8' ?>;
                border-radius: 8px;
                padding: 10px 15px;
                margin-bottom: 8px;
                color: #333;
                position: relative;
                ">
                <div style="font-weight: 600;"><?= htmlspecialchars($c['autor'] ?? 'Usuário') ?></div>
                <div style="font-size: 0.9rem;"><?= nl2br(htmlspecialchars($c['comentario'])) ?></div>
                <div style="font-size: 0.8rem; color: #666; margin-top: 5px;">
                    <?= date('d/m/Y H:i', strtotime($c['data_hora_br'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: #666;">Nenhum comentário ainda.</p>
    <?php endif; ?>
</div>

<!-- Status -->
<?php 
$status = $row['status'];
$color = '#333';
if(str_starts_with($status,'Aprovado')) $color = 'green';
elseif(str_starts_with($status,'Rejeitado')) $color = 'red';
?>
<p class="status_box" style="background-color: <?= $color ?>;">Status: <?= htmlspecialchars($status) ?></p>

<!-- Botão voltar -->
<?php
$origem = $_GET['origem'] ?? null;
$voltar_para = $origem
    ? ($origem === 'fases' ? 'proposta_fases.php' : 'proposta_consulta.php')
    : ($_SERVER['HTTP_REFERER'] ?? 'proposta_consulta.php');
?>
<a href="<?= htmlspecialchars($voltar_para) ?>" class="voltar">Voltar</a>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const novoComentario = urlParams.get("novo");

    // Só rola se o parâmetro "novo=1" estiver presente
    if (novoComentario === "1") {
        const ultimo = document.querySelector("#ultimo-comentario");
        if (ultimo) {
            ultimo.scrollIntoView({ behavior: "smooth", block: "center" });

            // Efeito de destaque suave
            ultimo.style.transition = "background-color 1s ease";
            const corOriginal = ultimo.style.backgroundColor;
            ultimo.style.backgroundColor = "#fff3b0";
            setTimeout(() => ultimo.style.backgroundColor = corOriginal, 2000);
        }

        // Remove o parâmetro da URL sem recarregar a página
        const novaURL = window.location.href.replace(/(&|\?)novo=1/, "");
        window.history.replaceState({}, document.title, novaURL);
    }
});
</script>
</body>
</html>