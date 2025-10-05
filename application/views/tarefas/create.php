<h2><?php echo $titulo; ?></h2>

<?php if (validation_errors()): ?>
    <div style="color:red;"><?php echo validation_errors(); ?></div>
<?php endif; ?>

<?php echo form_open('tarefas/create', ['style' => 'max-width:400px;']); ?>

<!-- Título -->
<div style="margin-bottom:10px;">
    <label for="titulo">Título:</label><br>
    <input type="text" id="titulo" name="titulo" 
           value="<?php echo set_value('titulo'); ?>" 
           required style="width:100%; padding:5px;">
</div>

<!-- Descrição -->
<div style="margin-bottom:10px;">
    <label for="descricao">Descrição:</label><br>
    <textarea id="descricao" name="descricao" rows="4" 
              style="width:100%; padding:5px;"><?php echo set_value('descricao'); ?></textarea>
</div>

<!-- Prazo -->
<div style="margin-bottom:10px;">
    <label for="prazo">Prazo:</label><br>
    <input type="date" id="prazo" name="prazo" 
           value="<?php echo set_value('prazo'); ?>" 
           style="width:100%; padding:5px;">
</div>

<!-- Prioridade -->
<div style="margin-bottom:10px;">
    <label for="prioridade">Prioridade:</label><br>
    <select id="prioridade" name="prioridade" style="width:100%; padding:5px;">
        <option value="">Selecione</option>
        <option value="Baixa" <?php echo set_select('prioridade', 'Baixa'); ?>>Baixa</option>
        <option value="Media" <?php echo set_select('prioridade', 'Media'); ?>>Média</option>
        <option value="Alta" <?php echo set_select('prioridade', 'Alta'); ?>>Alta</option>
    </select>
</div>

<!-- Botão Salvar -->
<button type="submit" style="padding:10px 15px; background-color:#28a745; color:#fff; border:none; border-radius:5px;">
    Salvar Tarefa
</button>

<?php echo form_close(); ?>
