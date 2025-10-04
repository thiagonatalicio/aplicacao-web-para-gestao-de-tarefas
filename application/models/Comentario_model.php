<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comentario_model extends CI_Model {

    private $table = 'comentarios';

    public function getByTarefa(int $tarefa_id): array {
        return $this->db
                    ->where('tarefa_id', $tarefa_id)
                    ->order_by('criado_em', 'ASC')
                    ->get($this->table)
                    ->result();
    }

    public function insert(int $tarefa_id, string $texto): int {
        $this->db->insert($this->table, [
            'tarefa_id' => $tarefa_id,
            'texto' => $texto,
            'criado_em' => date('Y-m-d H:i:s')
        ]);
        return $this->db->insert_id();
    }

    public function delete(int $id): bool {
        return $this->db->where('id', $id)->delete($this->table);
    }
}
