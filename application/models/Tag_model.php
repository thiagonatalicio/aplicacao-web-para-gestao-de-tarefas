<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tag_model extends CI_Model {

    private $table = 'tags';

    public function getAll(): array {
        return $this->db->order_by('nome', 'ASC')->get($this->table)->result();
    }

    public function getById(int $id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function getOrCreate(string $nome): int {
        $query = $this->db->where('nome', $nome)->get($this->table);
        if ($query->num_rows() === 0) {
            $this->db->insert($this->table, ['nome' => $nome]);
            return $this->db->insert_id();
        } else {
            return $query->row()->id;
        }
    }

    public function delete(int $id) {
        return $this->db->where('id', $id)->delete($this->table);
    }
}
