<?php
session_start();
include(__DIR__ . '/../src/config/conexao.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Busca somente as propostas do usuário logado
$stmt = $conexao->prepare("SELECT * FROM formulario WHERE usuario_id = ? ORDER BY id DESC");
$stmt->execute([$usuario_id]);
$propostas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Minhas Propostas</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body {
    font-family: Poppins, sans-serif;
    background: #f2f2f2;
    padding: 40px;
}
.container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 25px;
    max-width: 1000px;
    margin: auto;
}
h1 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 10px;
    border-bottom: 1px solid #ccc;
    text-align: center;
}
th {
    background: #666;
    color: white;
}
tr:nth-child(even) {
    background: #f9f9f9;
}
</style>
</head>
<body>

<div class="container">
    <h1>Minhas Propostas</h1>
    <?php if (count($propostas) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Formato</th>
                <th>Data</th>
                <th>Status</th>
            </tr>
            <?php foreach ($propostas as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['id']) ?></td>
                <td><?= htmlspecialchars($p['nome_produto']) ?></td>
                <td><?= htmlspecialchars($p['formato']) ?></td>
                <td><?= date('d/m/Y', strtotime($p['data_previsao'])) ?></td>
                <td><?= htmlspecialchars($p['status']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Nenhuma proposta encontrada',
                text: 'Você ainda não cadastrou nenhuma proposta.',
                confirmButtonText: 'Entendi'
            });
        </script>
    <?php endif; ?>
</div>

</body>
</html>
