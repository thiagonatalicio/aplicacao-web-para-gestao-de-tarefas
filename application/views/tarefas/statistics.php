<h1><?php echo $titulo; ?></h1>

<ul>
    <li>Total de tarefas concluídas: <?php echo $stats['Concluidas']; ?></li>
    <li>Total de tarefas em andamento: <?php echo $stats['Em andamento']; ?></li>
    <li>Total de tarefas atrasadas: <?php echo $stats['Atrasadas']; ?></li>
</ul>

<a href="<?php echo site_url('tarefas'); ?>">Voltar à lista de tarefas</a>
