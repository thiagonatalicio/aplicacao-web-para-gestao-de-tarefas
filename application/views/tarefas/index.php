<h1><?php echo $titulo ?></h1>



<a href="<?php echo site_url('tarefas/create'); ?>">+ Criar nova tarefa</a>


<?php if(!empty($dbtarefas)) : ?>
    <div class="tarefas-container" style="display:flex; flex-wrap: wrap; gap: 20px;">
        <?php foreach($dbtarefas as $tarefa) : ?>
            <div class="tarefa-card" style="border:1px solid #ccc; border-radius:8px; padding:15px; width:50%; box-shadow:2px 2px 8px rgba(0,0,0,0.1);">
                <h3 style="margin-top:0;"><?php echo htmlspecialchars($tarefa['titulo']); ?></h3>
                <p><strong>Descrição:</strong> <?php echo htmlspecialchars($tarefa['descricao']); ?></p>
                <p><strong>Prazo:</strong> <?php echo htmlspecialchars($tarefa['prazo']); ?></p>
                <p><strong>Prioridade:</strong> <?php echo htmlspecialchars($tarefa['prioridade']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Nenhuma tarefa encontrada.</p>
<?php endif; ?>