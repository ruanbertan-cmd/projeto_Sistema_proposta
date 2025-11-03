<?php
// Consulta os comentários da proposta
$stmtComentarios = $conexao->prepare("
    SELECT c.comentario, c.data_hora, u.nome AS usuario
    FROM comentarios c
    LEFT JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.formulario_id = ?
    ORDER BY c.data_hora DESC
");
$stmtComentarios->execute([$row['id']]);
$comentarios = $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Formulário para adicionar comentário -->
<form method="POST" action="actions.php?action=comentario&id=<?= htmlspecialchars($row['id']) ?>&origem=<?= htmlspecialchars($_GET['origem'] ?? 'consulta') ?>">
    <textarea name="comentario_Lib_Produto" placeholder="Escreva um comentário..." required></textarea>
    <button type="submit">Salvar comentário</button>
</form>

<!-- Exibição do histórico de comentários -->
<div class="comentario-container">
    <strong>Comentários:</strong>
    <?php if (count($comentarios) > 0): ?>
        <?php foreach ($comentarios as $c): ?>
            <div style="margin-top:10px; border-top:1px solid #ddd; padding-top:8px;">
                <small><strong><?= htmlspecialchars($c['usuario'] ?? 'Usuário') ?></strong> - <?= date('d/m/Y H:i', strtotime($c['data_hora'])) ?></small>
                <p class="comentario-text"><?= nl2br(htmlspecialchars($c['comentario'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="comentario-text">Nenhum comentário ainda.</p>
    <?php endif; ?>
</div>
