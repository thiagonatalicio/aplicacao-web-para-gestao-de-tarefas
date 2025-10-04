<?php if (!empty($dbtarefas)) : ?>


    <h1>Gestor de tarefas</h1>
    <a href="<?php echo site_url('tarefas/create'); ?>" class="btn-criar">+ Criar Nova Tarefa</a>

    <div class="tarefas-container" style="display:flex; flex-wrap: wrap; gap: 20px; margin-top:20px;">
        <?php foreach ($dbtarefas as $tarefa) : ?>
            <div class="tarefa-card" style="border:1px solid #ccc; border-radius:8px; padding:15px; width:45%; box-shadow:2px 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-top:0;"><?php echo htmlspecialchars($tarefa->titulo); ?></h3>
                <p><strong>Descrição:</strong> <?php echo htmlspecialchars($tarefa->descricao); ?></p>
                <p><strong>Prazo:</strong> <?php echo htmlspecialchars($tarefa->prazo); ?></p>
                <p><strong>Prioridade:</strong> <?php echo htmlspecialchars($tarefa->prioridade); ?></p>

                <div class="acoes" style="margin-top:10px;">
                    <a href="<?php echo site_url('tarefas/edit/'.$tarefa->id); ?>" 
                       style="margin-right:10px; color:blue; text-decoration:none;">Editar</a>

                    <a href="<?php echo site_url('tarefas/delete/'.$tarefa->id); ?>" 
                       style="color:red; text-decoration:none;" 
                       onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">Excluir</a>
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