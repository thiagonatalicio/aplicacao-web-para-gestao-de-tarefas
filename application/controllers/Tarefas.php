<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tarefas extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tarefa_model');
        $this->load->model('Comentario_model');
        $this->load->model('Tag_model');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
    }

    // Listagem geral com possível filtro por tags
    public function index()
    {
        $tagsRaw = $this->input->get('tags', true);
        $busca = $this->input->get('busca', true);

        if (!empty($tagsRaw) || !empty($busca)) {
            $tarefas = $this->Tarefa_model->search($busca, $tagsRaw);
        } else {
            $tarefas = $this->Tarefa_model->getAll();
        }

        $this->loadTasksWithExtras($tarefas, $busca, $tagsRaw);
    }

    // Funções CRUD (create, store, edit, update, delete) permanecem iguais...
    // Apenas simplifiquei para foco na busca

    // Busca unificada (título, descrição, tags)
    public function searchUnified()
    {
        $termo = $this->input->get('busca', true);
        $tags = $this->input->get('tags', true);

        $tarefas = $this->Tarefa_model->search($termo, $tags);
        $this->loadTasksWithExtras($tarefas, $termo, $tags);
    }

    /**
     * Função helper que adiciona tags e comentários a cada tarefa
     * e carrega a view index
     *
     * @param array $tarefas Array de objetos tarefas
     * @param string|null $busca Termo de busca
     * @param string|null $tagsRaw Tags usadas no filtro
     */
    private function loadTasksWithExtras(array $tarefas, ?string $busca = null, ?string $tagsRaw = null)
    {
        $hoje = date('Y-m-d');

        foreach ($tarefas as $tarefa) {
            // Verifica se está atrasada automaticamente
            if ($tarefa->status !== 'Concluida' && !empty($tarefa->prazo) && $tarefa->prazo < $hoje) {
                $tarefa->status_calculado = 'Atrasada';
            } else {
                $tarefa->status_calculado = $tarefa->status;
            }

            // Carrega tags
            $tags = $this->db->select('t.nome')
                ->from('tarefas_tags tt')
                ->join('tags t', 'tt.tag_id = t.id')
                ->where('tt.tarefa_id', $tarefa->id)
                ->get()
                ->result();
            $tarefa->tags = array_map(fn($t) => $t->nome, $tags);

            // Carrega comentários
            $tarefa->comentarios = $this->Comentario_model->getByTarefa($tarefa->id);
        }

        $dados = new stdClass();
        $dados->titulo = 'Lista de Tarefas';
        $dados->dbtarefas = $tarefas;
        $dados->filtro_busca = $busca;
        $dados->filtro_tags = $tagsRaw;

        $this->load->view('tarefas/index', $dados);
    }


    /**
     * Exibe estatísticas de tarefas
     */
    public function statistics()
    {
        $stats = $this->Tarefa_model->getStatistics();

        $data = new stdClass();
        $data->titulo = 'Estatísticas de Tarefas';
        $data->stats = $stats;

        $this->load->view('tarefas/statistics', $data);
    }

    // Exibe o formulário para criar nova tarefa e processa o envio
    public function create()
    {
        // Instancia o objeto de dados da view
        $data = new stdClass();
        $data->titulo = 'Criar Nova Tarefa';

        // Se houver POST (formulário enviado)
        if ($this->input->post()) {
            // Validação básica
            $this->load->library('form_validation');
            $this->form_validation->set_rules('titulo', 'Título', 'required|max_length[255]');

            if ($this->form_validation->run() === FALSE) {
                // Volta para o form com erros
                $this->load->view('tarefas/create', $data);
                return;
            }

            // Prepara dados para inserir no banco
            $tarefa = (object) [
                'titulo' => $this->input->post('titulo', true),
                'descricao' => $this->input->post('descricao', true),
                'prazo' => $this->input->post('prazo') ?: null,
                'prioridade' => $this->input->post('prioridade') ?: 'Media',
                'status' => 'Em andamento'
            ];

            $tagsRaw = $this->input->post('tags', true);

            // Chama o model orientado a objetos
            $this->Tarefa_model->insert_tarefa($tarefa, $tagsRaw);

            // Redireciona para a listagem
            redirect('tarefas');
        }

        // Se for GET, exibe o formulário
        $this->load->view('tarefas/create', $data);
    }


    // Edita a tarefa no banco de dados.
    public function edit(int $id)
    {
        // Busca a tarefa pelo ID usando o model OO
        $tarefa = $this->Tarefa_model->get_by_id($id);

        if (!$tarefa) {
            show_404();
            return;
        }

        // Se o formulário foi enviado
        if ($this->input->method() === 'post') {
            // Pega os dados do formulário como objeto
            $tarefaAtualizada = (object)[
                'titulo' => $this->input->post('titulo', true),
                'descricao' => $this->input->post('descricao', true),
                'prazo' => $this->input->post('prazo') ?: null,
                'prioridade' => $this->input->post('prioridade') ?: 'Media',
            ];

            // Atualiza no banco usando o model OO
            $this->Tarefa_model->update($id, $tarefaAtualizada);

            // Redireciona de volta para a listagem
            redirect('tarefas');
        }

        // Carrega o formulário de edição
        $data = new stdClass();
        $data->titulo = 'Editar Tarefa';
        $data->tarefa = $tarefa;

        $this->load->view('tarefas/edit', $data);
    }

    /**
     * Exclui uma tarefa pelo ID
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        // Busca a tarefa antes de deletar (opcional, mas evita erros)
        $tarefa = $this->Tarefa_model->get_by_id($id);

        if (!$tarefa) {
            show_404(); // Tarefa não encontrada
            return;
        }

        // Deleta a tarefa
        $this->Tarefa_model->delete($id);

        // Redireciona de volta para a lista com mensagem de sucesso (opcional)
        $this->session->set_flashdata('success', 'Tarefa deletada com sucesso!');
        redirect('tarefas');
    }
    public function update_status(int $id)
    {
        $status = $this->input->post('status', true);

        // Valida para garantir que não seja um valor inválido
        $valores_permitidos = ['Em andamento', 'Concluida', 'Atrasada'];
        if (!in_array($status, $valores_permitidos)) {
            show_error('Status inválido');
            return;
        }

        $this->Tarefa_model->updateStatus($id, ['status' => $status]);
        redirect('tarefas');
    }
}
