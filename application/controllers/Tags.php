<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tags extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Tag_model');
        $this->load->model('Tarefa_model'); // Para acessar tarefas_tags
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
    }

    // Atualiza as tags de uma tarefa
    public function update_tags(int $tarefa_id) {
        // Pega as tags do formulário (separadas por vírgula)
        $tags_raw = $this->input->post('tags', true);
        $tags = array_filter(array_map('trim', explode(',', $tags_raw)));

        // 1️⃣ Remove todas as tags antigas da tarefa
        $this->db->where('tarefa_id', $tarefa_id)->delete('tarefas_tags');

        // 2️⃣ Adiciona as novas tags
        foreach ($tags as $tag_nome) {
            $tag_id = $this->Tag_model->getOrCreate($tag_nome); // retorna ID existente ou cria nova
            $this->db->insert('tarefas_tags', [
                'tarefa_id' => $tarefa_id,
                'tag_id' => $tag_id
            ]);
        }

        // Redireciona de volta para a lista de tarefas
        redirect('tarefas');
    }
}
