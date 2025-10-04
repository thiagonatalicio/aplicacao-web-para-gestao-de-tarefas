<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tarefa_model extends CI_Model
{
    private $table = 'tarefas';
    private $tagsTable = 'tags';
    private $tarefasTagsTable = 'tarefas_tags';

    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
        echo 'passou pelo controller';
        // $tarefas será um array de objetos stdClass
        $tarefas = $this->Tarefa_model->getAll();

        // Cria um stdClass para passar dados como objeto
        $data = new stdClass();
        $data->titulo = 'Lista de Tarefas';
        $data->tarefas = $tarefas;

        // Carrega a view com dados em forma de objeto
        $this->load->view('tarefas/index', $data);
    }


    // Retorna todas as tarefas como objetos
    
    public function getAll(){
        $query = $this->db->select('t.*')
                        ->from($this->table . ' t')
                        ->order_by('criado_em', 'DESC')
                        ->get();

        return $query->result(); // array de objetos stdClass
    }


    // Insere uma tarefa e suas tags
    public function insert_tarefa($tarefa, $tagsRaw = null){
        $this->db->insert($this->table, (array)$tarefa);
        $tarefaId = $this->db->insert_id();

        if (!empty($tagsRaw)) {
            $tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
            foreach ($tags as $tag) {
                $tagId = $this->getOrCreateTag($tag);
                $this->db->insert($this->tarefasTagsTable, [
                    'tarefa_id' => $tarefaId,
                    'tag_id' => $tagId
                ]);
            }
        }

        return $tarefaId;
    }

    // Atualiza uma tarefa
    public function updateTarefa(int $id, object $tarefa): bool
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, (array)$tarefa);
    }
    

    // Deleta uma tarefa
    public function delete(int $id){
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
    public function get_by_id($id){
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    // Retorna ou cria uma tag e retorna o ID
    private function getOrCreateTag(string $nome): int
    {
        $query = $this->db->where('nome', $nome)->get($this->tagsTable);
        if ($query->num_rows() === 0) {
            $this->db->insert($this->tagsTable, ['nome' => $nome]);
            return $this->db->insert_id();
        } else {
            return $query->row()->id;
        }
    }
}