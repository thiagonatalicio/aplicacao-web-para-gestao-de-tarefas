<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tarefas extends CI_Controller {
    public function __construct(){
        parent::__construct();
        //torna o model disponível em toda a controller(isso n é IA)
        $this->load->model('Tarefa_model');
        $this->load->helper(array('url','form'));
    }

    public function index(){
        $dados = array(
            'titulo'=>'Lista de Tarefas',
            'dbtarefas' => $this->Tarefa_model->get_all(),
            'pagina' => 'tarefas/index.php'
        );
        $this->load->view('index', $dados);
    }

    public function create(){
        $dados = array(
            'titulo'=>'Criar nova tarefa',
            'pagina' => 'tarefas/create.php'

        );
        
        echo "Passou pelo create";
        $this->load->view('index', $dados);
    }

    public function store(){
        // validações simples
        $this->load->library('form_validation');
        $this->form_validation->set_rules('titulo', 'Título', 'required|max_length[255]');

        if ($this->form_validation->run() === FALSE) {
            // volta para o form com erros
            $this->load->view('tarefas/create');
            return;
        }

        $dados = [
            'titulo' => $this->input->post('titulo', true),
            'descricao' => $this->input->post('descricao', true),
            'prazo' => $this->input->post('prazo') ?: null,
            'prioridade' => $this->input->post('prioridade') ?: 'Media',
            'status' => 'Em andamento'
        ];

        $tags_raw = $this->input->post('tags', true);

        $tarefa_id = $this->Tarefa_model->insert_tarefa($dados, $tags_raw);

        // redireciona para lista
        redirect('tarefas');
    }
}
