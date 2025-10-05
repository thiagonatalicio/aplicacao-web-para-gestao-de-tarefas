<h2><?= $titulo; ?></h2>

<?php echo form_open('tarefas/edit/'.$tarefa->id, ['style' => 'max-width:400px;']); ?>

<!-- Título -->
<div style="margin-bottom:10px;">
    <label for="titulo">Título:</label><br>
    <input type="text" id="titulo" name="titulo" 
           value="<?= set_value('titulo', $tarefa->titulo); ?>" 
           required style="width:100%; padding:5px;">
</div>

<!-- Descrição -->
<div style="margin-bottom:10px;">
    <label for="descricao">Descrição:</label><br>
    <textarea id="descricao" name="descricao" rows="4" 
              style="width:100%; padding:5px;"><?= set_value('descricao', $tarefa->descricao); ?></textarea>
</div>

<!-- Prazo -->
<div style="margin-bottom:10px;">
    <label for="prazo">Prazo:</label><br>
    <input type="date" id="prazo" name="prazo" 
           value="<?= set_value('prazo', $tarefa->prazo); ?>" 
           style="width:100%; padding:5px;">
</div>

<!-- Prioridade -->
<div style="margin-bottom:10px;">
    <label for="prioridade">Prioridade:</label><br>
    <select id="prioridade" name="prioridade" style="width:100%; padding:5px;">
        <option value="Baixa" <?= ($tarefa->prioridade=='Baixa')?'selected':''; ?>>Baixa</option>
        <option value="Media" <?= ($tarefa->prioridade=='Media')?'selected':''; ?>>Média</option>
        <option value="Alta" <?= ($tarefa->prioridade=='Alta')?'selected':''; ?>>Alta</option>
    </select>
</div>

<!-- Botão Salvar -->
<button type="submit" style="padding:10px 15px; background-color:#28a745; color:#fff; border:none; border-radius:5px;">
    Atualizar Tarefa
</button>

<?php echo form_close(); ?>

<!-- Exibe erros de validação -->
<?php if (validation_errors()): ?>
    <div style="color:red; margin-top:10px;"><?= validation_errors(); ?></div>
<?php endif; ?>
