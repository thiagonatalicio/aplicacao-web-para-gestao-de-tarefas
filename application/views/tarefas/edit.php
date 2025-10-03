<h1>Editar Tarefa</h1>
<form method="post">
    <label>Título:</label><input type="text" name="titulo" value="<?= $tarefa->titulo ?>" required><br>
    <label>Descrição:</label><textarea name="descricao"><?= $tarefa->descricao ?></textarea><br>
    <label>Prazo:</label><input type="date" name="prazo" value="<?= $tarefa->prazo ?>"><br>
    <label>Prioridade:</label>
    <select name="prioridade">
        <option value="Baixa" <?= $tarefa->prioridade=='Baixa'?'selected':'' ?>>Baixa</option>
        <option value="Media" <?= $tarefa->prioridade=='Media'?'selected':'' ?>>Media</option>
        <option value="Alta" <?= $tarefa->prioridade=='Alta'?'selected':'' ?>>Alta</option>
    </select><br>
    <button type="submit">Salvar</button>
</form>
<a href="<?= site_url('tarefas') ?>">Voltar</a>
