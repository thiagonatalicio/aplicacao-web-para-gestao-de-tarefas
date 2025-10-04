<?php if (!empty($dbtarefas)) : ?>

<h1>Gestor de tarefas</h1>
<a href="<?php echo site_url('tarefas/create'); ?>" class="btn-criar">+ Criar Nova Tarefa</a>

<div class="tarefas-container" style="display:flex; flex-wrap: wrap; gap: 20px; margin-top:20px;">
    <?php foreach ($dbtarefas as $tarefa) : ?>
        <div class="tarefa-card" style="border:1px solid #ccc; border-radius:8px; padding:15px; width:45%; box-shadow:2px 2px 8px rgba(0,0,0,0.1); position:relative;">
            <h3 style="margin-top:0;"><?php echo htmlspecialchars($tarefa->titulo); ?></h3>
            <p><strong>Descrição:</strong> <?php echo htmlspecialchars($tarefa->descricao); ?></p>
            <p><strong>Prazo:</strong> <?php echo htmlspecialchars($tarefa->prazo); ?></p>
            <p><strong>Prioridade:</strong> <?php echo htmlspecialchars($tarefa->prioridade); ?></p>

            <!-- Tags -->
            <p><strong>Tags:</strong> 
                <?php if (!empty($tarefa->tags)): ?>
                    <?php foreach ($tarefa->tags as $tag): ?>
                        <span style="background:#eee; padding:3px 6px; border-radius:3px; margin-right:5px; display:inline-block;">
                            <?php echo htmlspecialchars($tag); ?>
                        </span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <em>Nenhuma tag</em>
                <?php endif; ?>
            </p>

            <!-- Comentários -->
            <div style="margin-top:10px;">
                <h4>Comentários:</h4>
                <?php if(!empty($tarefa->comentarios)): ?>
                    <?php foreach($tarefa->comentarios as $c): ?>
                        <p style="border-bottom:1px solid #eee; padding:2px 0;">
                            <?php echo htmlspecialchars($c->texto); ?>
                            <a href="<?php echo site_url('Comentario/delete/'.$c->id); ?>" 
                               style="color:red; text-decoration:none; border: 1px solid black; padding:2px 5px; margin-left:10px;" 
                               onclick="return confirm('Excluir comentário?')">Excuir</a>
                        </p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <em>Nenhum comentário</em>
                <?php endif; ?>
                
                <!-- Formulário para adicionar comentário -->
                <form action="<?php echo site_url('Comentario/store/'.$tarefa->id); ?>" method="post" style="margin-top:5px;">
                    <input type="text" name="texto" placeholder="Adicionar comentário..." style="width:80%; padding:5px;">
                    <button type="submit" style="padding:5px 10px;">Enviar</button>
                </form>
            </div>

            <div class="acoes" style="margin-top:10px;">
                <a href="<?php echo site_url('tarefas/edit/'.$tarefa->id); ?>" 
                   style="margin-right:10px; color:blue; text-decoration:none;">Editar</a>

                <a href="<?php echo site_url('tarefas/delete/'.$tarefa->id); ?>" 
                   style="color:red; text-decoration:none;" 
                   onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">Excluir</a>

                <!-- Botão de tags -->
                <button onclick="openTagModal(<?php echo $tarefa->id; ?>)" 
                        style="margin-left:10px; padding:5px 10px;">Gerenciar Tags</button>
            </div>

            <!-- Modal de tags -->
            <div id="tagModal-<?php echo $tarefa->id; ?>" class="tag-modal" style="display:none; position:absolute; top:10px; left:10px; background:white; border:1px solid #ccc; padding:15px; z-index:10; width:90%; max-width:300px; box-shadow:2px 2px 10px rgba(0,0,0,0.2);">
                <h4>Gerenciar Tags</h4>
                <form action="<?php echo site_url('Tags/update_tags/'.$tarefa->id); ?>" method="post">
                    <input type="text" name="tags" 
                           value="<?php echo !empty($tarefa->tags) ? implode(', ', $tarefa->tags) : ''; ?>" 
                           style="width:100%; padding:5px;" 
                           placeholder="Separe por vírgula">
                    <div style="margin-top:10px;">
                        <button type="submit" style="padding:5px 10px;">Salvar</button>
                        <button type="button" onclick="closeTagModal(<?php echo $tarefa->id; ?>)" style="padding:5px 10px; margin-left:5px;">Fechar</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php else: ?>
<p>Nenhuma tarefa encontrada.</p>
<?php endif; ?>

<?php if (validation_errors()): ?>
<div style="color:red;"><?php echo validation_errors(); ?></div>
<?php endif; ?>

<script>
// Abre modal
function openTagModal(id) {
    document.getElementById('tagModal-' + id).style.display = 'block';
}
// Fecha modal
function closeTagModal(id) {
    document.getElementById('tagModal-' + id).style.display = 'none';
}
</script>
