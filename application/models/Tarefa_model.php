<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tarefa_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $this->db->select('t.*');
        $this->db->from('tarefas t');
        $this->db->order_by('criado_em', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function insert_tarefa(array $dados, $tags_raw = null)
    {
        // insere tarefa
        $this->db->insert('tarefas', $dados);
        $tarefa_id = $this->db->insert_id();

        // tags: aceitar "tag1,tag2"
        if (!empty($tags_raw)) {
            $tags = array_filter(array_map('trim', explode(',', $tags_raw)));
            foreach ($tags as $tag) {
                // insere se não existir
                $this->db->where('nome', $tag);
                $q = $this->db->get('tags');
                if ($q->num_rows() === 0) {
                    $this->db->insert('tags', ['nome' => $tag]);
                    $tag_id = $this->db->insert_id();
                } else {
                    $tag_id = $q->row()->id;
                }

                // relacao tarefas_tags
                $this->db->insert('tarefas_tags', [
                    'tarefa_id' => $tarefa_id,
                    'tag_id' => $tag_id
                ]);
            }
        }

        return $tarefa_id;
    }
}
