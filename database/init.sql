-- Criar banco
CREATE DATABASE IF NOT EXISTS gestor_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestor_db;

-- Tabela tarefas
CREATE TABLE IF NOT EXISTS tarefas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  descricao TEXT,
  prazo DATE,
  prioridade ENUM('Baixa','Media','Alta') DEFAULT 'Media',
  status ENUM('Em andamento','Concluida') DEFAULT 'Em andamento',
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela tags
CREATE TABLE IF NOT EXISTS tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL UNIQUE
);

-- Tabela tarefas_tags (muitos-para-muitos)
CREATE TABLE IF NOT EXISTS tarefas_tags (
  tarefa_id INT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY (tarefa_id, tag_id),
  FOREIGN KEY (tarefa_id) REFERENCES tarefas(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Tabela comentarios
CREATE TABLE IF NOT EXISTS comentarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tarefa_id INT NOT NULL,
  conteudo TEXT NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (tarefa_id) REFERENCES tarefas(id) ON DELETE CASCADE
);
