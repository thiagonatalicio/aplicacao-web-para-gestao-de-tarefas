# aplicacao-web-para-gestao-de-tarefas


Aplicação web para **gestão de tarefas**, desenvolvida com **CodeIgniter 3** e **PHP 7.4**, utilizando **MySQL** como banco de dados e **Docker** para simplificar a configuração do ambiente.

O sistema permite que usuários cadastrem, editem, excluam e filtrem tarefas, além de adicionar tags, comentários e visualizar estatísticas do progresso.

---

##  Funcionalidades

-  Cadastro, edição e exclusão de tarefas  
-  Cada tarefa possui: título, descrição, prazo e prioridade  
-  Sistema de tags para classificação e filtragem  
-  Busca por título, descrição ou tags  
-  Estatísticas: quantidade de tarefas concluídas, em andamento e atrasadas  
-  Sistema de comentários nas tarefas  


---

##  Tecnologias Utilizadas

- **Back-end:** PHP 7.4, CodeIgniter 3  
- **Banco de dados:** MySQL  
- **Front-end:** HTML, CSS, JavaScript (template integrado)  
- **Ambiente:** Docker (PHP + Apache + MySQL)

---

##  Como executar

A aplicação pode ser executada de duas formas:  
-  Utilizando **Docker** (recomendado para facilitar a configuração)  
-  De forma **local**, com PHP e MySQL instalados manualmente

###  Executando com Docker (recomendado)

1. **Clone o repositório**:
   ```bash
   git clone https://github.com/seu-usuario/gestor-de-tarefas.git


2. **Acesse a pasta do projeto**:

   ```bash
   cd gestor-de-tarefas
   ```

3. **Suba os containers**:

   ```bash
   docker-compose up -d
   ```

4. **Acesse no navegador**:

   ```
   http://localhost:8080
   ```

5. **(Opcional)** Para parar os containers:

   ```bash
   docker-compose down
   ```

>  Ao usar Docker, todo o ambiente (PHP + Apache + MySQL) é configurado automaticamente.

###  Executando localmente (sem Docker)

1. Coloque os arquivos do projeto na pasta do servidor local (ex: `htdocs` no XAMPP ou `www` no Wamp).
2. Configure `application/config/config.php` para ajustar a base URL:

   ```php
   $config['base_url'] = 'http://localhost/gestor-de-tarefas/';
   ```
3. Configure `application/config/database.php` com as credenciais do seu MySQL local:

   ```php
   $db['default'] = array(
       'hostname' => 'localhost',
       'username' => 'root',
       'password' => '',
       'database' => 'gestor_tarefas',
       'dbdriver' => 'mysqli',
   );
   ```
4. Inicie o servidor PHP 7.4 e MySQL.
5. Acesse:

   ```
   http://localhost/gestor-de-tarefas
   ```

---

###  Configuração do Banco de Dados

* Crie um banco de dados MySQL chamado `gestor_tarefas` (ou outro nome de sua preferência).
* Importe o arquivo `database.sql` incluso no projeto para criar as tabelas necessárias.
* Se estiver usando Docker, esse processo pode ser automatizado pelo `docker-compose.yml`.

---

##  Licença

Este projeto está licenciado sob a licença MIT.
Sinta-se à vontade para usar, modificar e distribuir.

---

##  Autor

Desenvolvido por **[Thiago Natalicio](https://github.com/thiagonatalicio)** 

##
