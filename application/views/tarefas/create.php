<h2>Criar Nova Tarefa</h2>
<?php if (validation_errors()): ?>
    <div style="color:red;"><?php echo validation_errors(); ?></div>
<?php endif; ?>
<form action="<?php echo site_url('tarefas/store'); ?>" method="post" style="max-width:400px;">
    
    <div style="margin-bottom:10px;">
        <label for="titulo">Título:</label><br>
        <input type="text" id="titulo" name="titulo" required style="width:100%; padding:5px;">
    </div>

    <div style="margin-bottom:10px;">
        <label for="descricao">Descrição:</label><br>
        <textarea id="descricao" name="descricao" rows="4" required style="width:100%; padding:5px;"></textarea>
    </div>

    <div style="margin-bottom:10px;">
        <label for="prazo">Prazo:</label><br>
        <input type="date" id="prazo" name="prazo" required style="width:100%; padding:5px;">
    </div>

    <div style="margin-bottom:10px;">
        <label for="prioridade">Prioridade:</label><br>
        <select id="prioridade" name="prioridade" required style="width:100%; padding:5px;">
            <option value="">Selecione</option>
            <option value="Baixa">Baixa</option>
            <option value="Media">Média</option>
            <option value="Alta">Alta</option>
        </select>
    </div>

    <button type="submit" style="padding:10px 15px; background-color:#28a745; color:#fff; border:none; border-radius:5px;">Salvar Tarefa</button>

</form>

