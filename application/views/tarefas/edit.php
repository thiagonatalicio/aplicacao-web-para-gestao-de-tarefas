<h1><?php echo $titulo; ?></h1>
<?php if (validation_errors()): ?>
    <div style="color:red;"><?php echo validation_errors(); ?></div>
<?php endif; ?>
<form action="<?php echo site_url('tarefas/edit/'.$tarefa->id); ?>" method="post">
    <label for="titulo">Título:</label><br>
    <input type="text" id="titulo" name="titulo" value="<?php echo $tarefa->titulo; ?>" required><br><br>

    <label for="descricao">Descrição:</label><br>
    <textarea id="descricao" name="descricao" rows="4" cols="50"><?php echo $tarefa->descricao; ?></textarea><br><br>

    <button type="submit">Salvar alterações</button>
</form>
