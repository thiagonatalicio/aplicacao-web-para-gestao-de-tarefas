<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tarefas extends CI_Controller
{
    private function setCorsHeaders()
    {
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit; // para preflight
        }
    }

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
        $this->setCorsHeaders();
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
        $this->setCorsHeaders();
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
        $this->setCorsHeaders();
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
        $this->setCorsHeaders();
        $stats = $this->Tarefa_model->getStatistics();

        $data = new stdClass();
        $data->titulo = 'Estatísticas de Tarefas';
        $data->stats = $stats;

        $this->load->view('tarefas/statistics', $data);
    }

    // Exibe o formulário para criar nova tarefa e processa o envio
    public function apiCreate()
    {
        $this->setCorsHeaders();

        // Lê JSON enviado pelo React
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Dados inválidos']));
            return;
        }

        // Certifique-se que os nomes batem com o que o frontend envia
        $tarefa = (object)[
            'titulo' => $input['titulo'] ?? '',
            'descricao' => $input['descricao'] ?? '',
            'prazo' => $input['prazo'] ?? null,
            'prioridade' => $input['prioridade'] ?? 'Media',
            'status' => $input['status'] ?? 'Em andamento'
        ];

        $tagsRaw = $input['tags'] ?? null; // se vier tags, senão null

        try {
            $id = $this->Tarefa_model->insert_tarefa($tarefa, $tagsRaw);
            $tarefa->id = $id;

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($tarefa));
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['error' => $e->getMessage()]));
        }
    }





    // Edita a tarefa no banco de dados.
    public function apiUpdate($id)
    {
        $this->setCorsHeaders();
        // Lê o JSON enviado pelo front-end
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);

        $titulo = $input['titulo'] ?? null;
        $descricao = $input['descricao'] ?? null;
        $prazo = $input['prazo'] ?? null;
        $prioridade = $input['prioridade'] ?? 'Media';

        // Busca a tarefa
        $tarefa = $this->Tarefa_model->get_by_id($id);
        if (!$tarefa) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode(['error' => 'Tarefa não encontrada']));
            return;
        }

        // Atualiza
        $tarefaAtualizada = (object)[
            'titulo' => $titulo,
            'descricao' => $descricao,
            'prazo' => $prazo,
            'prioridade' => $prioridade
        ];

        $this->Tarefa_model->update($id, $tarefaAtualizada);

        // Retorna a tarefa atualizada
        $tarefa = $this->Tarefa_model->get_by_id($id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($tarefa));
    }




    /**
     * Exclui uma tarefa pelo ID
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $this->setCorsHeaders();
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
        $this->setCorsHeaders();
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

    public function apiIndex()
    {
        $this->setCorsHeaders();
        $tarefas = $this->Tarefa_model->getAll();

        // Adiciona tags, comentários e status calculado (mesmo que no index)
        $hoje = date('Y-m-d');
        foreach ($tarefas as $tarefa) {
            if ($tarefa->status !== 'Concluida' && !empty($tarefa->prazo) && $tarefa->prazo < $hoje) {
                $tarefa->status_calculado = 'Atrasada';
            } else {
                $tarefa->status_calculado = $tarefa->status;
            }

            $tags = $this->db->select('t.nome')
                ->from('tarefas_tags tt')
                ->join('tags t', 'tt.tag_id = t.id')
                ->where('tt.tarefa_id', $tarefa->id)
                ->get()
                ->result();
            $tarefa->tags = array_map(fn($t) => $t->nome, $tags);

            $tarefa->comentarios = $this->Comentario_model->getByTarefa($tarefa->id);
        }

        // Retorna JSON
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($tarefas));
    }
}
