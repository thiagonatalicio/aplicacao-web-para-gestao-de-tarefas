<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tarefas extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('Tarefa_model');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
    }

    // Lista todas as tarefas
    public function index() {
        $this->load->model('Tag_model');         // garante que o model de tags está carregado
        $this->load->model('Comentario_model');  // carrega o model de comentários

        $dados = new stdClass();
        $dados->titulo = 'Lista de Tarefas';

        // Busca todas as tarefas
        $tarefas = $this->Tarefa_model->getAll();

        // Para cada tarefa, adiciona as tags e comentários relacionados
        foreach ($tarefas as $tarefa) {
            // Tags
            $tags = $this->db
                        ->select('t.nome')
                        ->from('tarefas_tags tt')
                        ->join('tags t', 'tt.tag_id = t.id')
                        ->where('tt.tarefa_id', $tarefa->id)
                        ->get()
                        ->result();
            $tarefa->tags = array_map(fn($t) => $t->nome, $tags);

            // Comentários
            $comentarios = $this->Comentario_model->getByTarefa($tarefa->id);
            $tarefa->comentarios = $comentarios;
        }

        $dados->dbtarefas = $tarefas;

        $this->load->view('tarefas/index', $dados);
    }



    // Formulário para criar nova tarefa
    public function create(){
        $data = new stdClass();
        $data->titulo = 'Criar Nova Tarefa';
        $this->load->view('tarefas/create', $data);
    }

    // Salvar nova tarefa
    public function store(){
        $this->form_validation->set_rules('titulo', 'Título', 'required|max_length[255]');
        $this->form_validation->set_rules('descricao', 'Descrição', 'max_length[65535]');
        $this->form_validation->set_rules('prazo', 'Prazo', 'regex_match[/^\d{4}-\d{2}-\d{2}$/]');

        $this->form_validation->set_rules('prioridade', 'Prioridade', 'in_list[Baixa,Media,Alta]');

        if ($this->form_validation->run() === FALSE) {
            return $this->load->view('tarefas/create');
        }

        $tarefa = new stdClass();
        $tarefa->titulo = $this->input->post('titulo', true);
        $tarefa->descricao = $this->input->post('descricao', true);
        $tarefa->prazo = $this->input->post('prazo') ?: null;
        $tarefa->prioridade = $this->input->post('prioridade') ?: 'Media';
        $tarefa->status = 'Em andamento';

        $tags_raw = $this->input->post('tags', true);

        $this->Tarefa_model->insert_tarefa($tarefa, $tags_raw);

        redirect('tarefas');
    }

    // Formulário de edição
    public function edit($id){
        // Busca a tarefa pelo ID
        $tarefa = $this->db->where('id', $id)->get('tarefas')->row();

        // Se não encontrar, mostra erro 404
        if (!$tarefa) {
            show_404();
            return;
        }

        // Se for um POST (formulário enviado)
        if ($this->input->method() === 'post') {
            // Pega os dados do formulário como objeto
            $tarefaAtualizada = (object) [
                'titulo' => $this->input->post('titulo'),
                'descricao' => $this->input->post('descricao'),
                'prazo' => $this->input->post('prazo') ?: null,
                'prioridade' => $this->input->post('prioridade') ?: 'Media',
            ];

            // Atualiza no banco
            $this->Tarefa_model->updateTarefa($id, $tarefaAtualizada);

            // Redireciona de volta para a listagem
            redirect('tarefas');
        }

        // Se for GET, carrega o formulário de edição
        $data = new stdClass();
        $data->titulo = 'Editar Tarefa';
        $data->tarefa = $tarefa;
        

        $this->load->view('tarefas/edit', $data);
    }

    // Atualizar tarefa.
    public function update($id){
        $this->form_validation->set_rules('titulo', 'Título', 'required|max_length[255]');
        $this->form_validation->set_rules('descricao', 'Descrição', 'max_length[65535]');
        $this->form_validation->set_rules('prazo', 'Prazo', 'valid_date');
        $this->form_validation->set_rules('prioridade', 'Prioridade', 'in_list[Baixa,Media,Alta]');

        if ($this->form_validation->run() === FALSE) {
            return $this->edit($id);
        }

        $tarefa = new stdClass();
        $tarefa->titulo = $this->input->post('titulo', true);
        $tarefa->descricao = $this->input->post('descricao', true);
        $tarefa->prazo = $this->input->post('prazo') ?: null;
        $tarefa->prioridade = $this->input->post('prioridade') ?: 'Media';

        $this->Tarefa_model->update($id, $tarefa);

        redirect('tarefas');
    }
    // Deletar tarefa(junto com comentários e vínculos de tags).
    public function delete($id){
        $tarefa = $this->Tarefa_model->get_by_id($id);
        if (!$tarefa) {
            show_404();
        }
        // 1️ Deleta comentários relacionados à tarefa.
        $this->db->where('tarefa_id', $id)->delete('comentarios');

        // 2️ Deleta vínculos de tags relacionados à tarefa.
        $this->db->where('tarefa_id', $id)->delete('tarefas_tags');

        // 3️ Por último, deleta a tarefa em si.
        $this->Tarefa_model->delete($id);

        redirect('tarefas');
    }

}