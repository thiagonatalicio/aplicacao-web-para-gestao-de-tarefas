<h1>Gestor de Tarefas</h1>



<a href="<?php echo site_url('tarefas/create'); ?>" class="btn-criar">+ Criar Nova Tarefa</a>

<div style="margin:20px;">
    <!-- Botão para estatísticas -->
    <a href="<?php echo site_url('tarefas/statistics'); ?>" class="btn-estatisticas"
        style="background:#4CAF50; color:white; padding:5px 10px; border-radius:5px; text-decoration:none;">
        Estatísticas
    </a>
</div>

<!-- Formulário de busca unificada -->
<form method="get" action="<?php echo site_url('tarefas/searchUnified'); ?>" style="margin-bottom:20px;">
    <input type="text" name="busca" placeholder="Buscar por título, descrição ou tags"
        value="<?php echo isset($filtro_busca) ? htmlspecialchars($filtro_busca) : ''; ?>"
        style="padding:5px;">
    <button type="submit" style="padding:5px 10px;">Buscar</button>
</form>

<!-- Formulário apenas para filtro de tags -->
<form method="get" action="<?php echo site_url('tarefas'); ?>" style="margin:20px 0;">
    <input type="text" name="tags" placeholder="Filtrar por tags (separadas por vírgula)"
        value="<?php echo isset($filtro_tags) ? htmlspecialchars($filtro_tags) : ''; ?>"
        style="padding:5px; width:250px;">
    <button type="submit" style="padding:5px 10px;">Buscar</button>

    <?php if (!empty($filtro_tags) || !empty($filtro_busca)): ?>
        <a href="<?php echo site_url('tarefas'); ?>" style="margin-left:10px;">Limpar filtros</a>
    <?php endif; ?>
</form>


<?php if (!empty($dbtarefas)) : ?>
    <div class="tarefas-container" style="display:flex; flex-wrap: wrap; gap: 20px;">
        <?php foreach ($dbtarefas as $tarefa) : ?>
            <div class="tarefa-card" style="border:1px solid #ccc; border-radius:8px; padding:15px; width:45%; box-shadow:2px 2px 8px rgba(0,0,0,0.1); position:relative;">
                <h3><?php echo htmlspecialchars($tarefa->titulo); ?></h3>
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
                <div class="status-acoes" style="margin-top:10px;">
                    <strong>Status:</strong>
                    <?php
                    $status = htmlspecialchars($tarefa->status_calculado);
                    $cor = $status === 'Concluida' ? 'green' : ($status === 'Atrasada' ? 'red' : 'orange');
                    ?>
                    <span style="color: <?php echo $cor; ?>; font-weight:bold;"><?php echo $status; ?></span>

                    <form action="<?php echo site_url('tarefas/update_status/' . $tarefa->id); ?>" method="post" style="display:inline;">
                        <select name="status" onchange="this.form.submit()" style="margin-left:5px; padding:2px;">
                            <option value="Em andamento" <?php echo $tarefa->status == 'Em andamento' ? 'selected' : ''; ?>>Em andamento</option>
                            <option value="Concluida" <?php echo $tarefa->status == 'Concluida' ? 'selected' : ''; ?>>Concluída</option>
                        </select>
                    </form>
                </div>

                <!-- Comentários -->
                <div style="margin-top:10px;">
                    <h4>Comentários:</h4>
                    <?php if (!empty($tarefa->comentarios)): ?>
                        <?php foreach ($tarefa->comentarios as $c): ?>
                            <div style="border-bottom:1px solid #eee; padding:5px 0;">
                                <p style="margin:0;">
                                    <?php echo htmlspecialchars($c->texto); ?>
                                </p>
                                <div style="margin-top:5px;">
                                    <!-- Botão Editar -->
                                    <button type="button"
                                        onclick="toggleEditComentario(<?php echo $c->id; ?>)"
                                        style="color:blue; text-decoration:none; border:1px solid black; padding:2px 5px;">
                                        Editar
                                    </button>

                                    <!-- Botão Excluir -->
                                    <a href="<?php echo site_url('Comentario/delete/' . $c->id); ?>"
                                        style="color:red; text-decoration:none; border:1px solid black; padding:2px 5px; margin-left:10px;"
                                        onclick="return confirm('Excluir comentário?')">
                                        Excluir
                                    </a>
                                </div>

                                <!-- Card de edição de comentário (escondido por padrão) -->
                                <div id="editComentario-<?php echo $c->id; ?>" style="display:none; margin-top:10px; background:#f9f9f9; padding:10px; border:1px solid #ccc; border-radius:5px;">
                                    <form action="<?php echo site_url('Comentario/update/' . $c->id); ?>" method="post">
                                        <input type="text" name="texto" value="<?php echo htmlspecialchars($c->texto); ?>" style="width:80%; padding:5px;">
                                        <button type="submit" style="padding:5px 10px;">Salvar</button>
                                        <button type="button" onclick="toggleEditComentario(<?php echo $c->id; ?>)" style="padding:5px 10px;">Cancelar</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <em>Nenhum comentário</em>
                    <?php endif; ?>

                    <!-- Adicionar novo comentário -->
                    <form action="<?php echo site_url('Comentario/store/' . $tarefa->id); ?>" method="post" style="margin-top:5px;">
                        <input type="text" name="texto" placeholder="Adicionar comentário..." style="width:80%; padding:5px;">
                        <button type="submit" style="padding:5px 10px;">Enviar</button>
                    </form>
                </div>

                <script>
                    function toggleEditComentario(id) {
                        const card = document.getElementById('editComentario-' + id);
                        card.style.display = (card.style.display === 'none' || card.style.display === '') ? 'block' : 'none';
                    }
                </script>

                <div class="acoes" style="margin-top:10px;">
                    <a href="<?php echo site_url('tarefas/edit/' . $tarefa->id); ?>"
                        style="margin-right:10px; color:blue; text-decoration:none;">Editar</a>

                    <a href="<?php echo site_url('tarefas/delete/' . $tarefa->id); ?>"
                        style="color:red; text-decoration:none;"
                        onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">Excluir</a>

                    <button onclick="openTagModal(<?php echo $tarefa->id; ?>)"
                        style="margin-left:10px; padding:5px 10px;">Gerenciar Tags</button>
                </div>

                <!-- Modal de tags -->
                <div id="tagModal-<?php echo $tarefa->id; ?>" class="tag-modal" style="display:none; position:absolute; top:10px; left:10px; background:white; border:1px solid #ccc; padding:15px; z-index:10; width:90%; max-width:300px; box-shadow:2px 2px 10px rgba(0,0,0,0.2);">
                    <h4>Gerenciar Tags</h4>
                    <form action="<?php echo site_url('Tags/update_tags/' . $tarefa->id); ?>" method="post">
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
    function openTagModal(id) {
        document.getElementById('tagModal-' + id).style.display = 'block';
    }

    function closeTagModal(id) {
        document.getElementById('tagModal-' + id).style.display = 'none';
    }
</script>