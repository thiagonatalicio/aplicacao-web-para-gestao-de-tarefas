<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comentario extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Comentario_model');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
    }

    // Adicionar um comentário a uma tarefa
    public function store(int $tarefa_id) {
        $this->form_validation->set_rules('texto', 'Comentário', 'required|max_length[1000]');

        if ($this->form_validation->run() === FALSE) {
            redirect('tarefas'); // ou retorna erro
        }

        $texto = $this->input->post('texto', true);
        $this->Comentario_model->insert($tarefa_id, $texto);

        redirect('tarefas');
    }

    // Deletar comentário
    public function delete(int $id) {
        $this->Comentario_model->delete($id);
        redirect('tarefas');
    }
}
