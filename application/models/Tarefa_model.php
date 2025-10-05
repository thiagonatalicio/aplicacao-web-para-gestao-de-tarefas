<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model para gerenciar tarefas.
 * 
 * Inclui funcionalidades de:
 * - Listagem
 * - Inserção
 * - Atualização
 * - Exclusão
 * - Busca por título, descrição, tags e busca combinada
 */
class Tarefa_model extends CI_Model
{

    private $table = 'tarefas';
    private $tagsTable = 'tags';
    private $tarefasTagsTable = 'tarefas_tags';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retorna todas as tarefas
     *
     * @return array Array de objetos stdClass
     */
    public function getAll(): array
    {
        return $this->db->select('t.*')
            ->from($this->table . ' t')
            ->order_by('criado_em', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Retorna uma tarefa pelo ID
     *
     * @param int $id
     * @return object|null
     */
    public function get_by_id(int $id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    /**
     * Insere uma nova tarefa junto com suas tags
     *
     * @param object $tarefa Objeto com dados da tarefa
     * @param string|null $tagsRaw Tags separadas por vírgula
     * @return int ID da tarefa criada
     */
    public function insert_tarefa(object $tarefa, ?string $tagsRaw = null): int
    {
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

    /**
     * Atualiza uma tarefa
     *
     * @param int $id
     * @param object $tarefa
     * @return bool
     */
    public function update(int $id, object $tarefa): bool
    {
        return $this->db->where('id', $id)
            ->update($this->table, (array)$tarefa);
    }

    /**
     * Deleta uma tarefa
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->db->where('id', $id)
            ->delete($this->table);
    }

    /**
     * Retorna ou cria uma tag e retorna o ID
     *
     * @param string $nome
     * @return int
     */
    private function getOrCreateTag(string $nome): int
    {
        $query = $this->db->where('nome', $nome)->get($this->tagsTable);
        if ($query->num_rows() === 0) {
            $this->db->insert($this->tagsTable, ['nome' => $nome]);
            return $this->db->insert_id();
        }
        return $query->row()->id;
    }

    /**
     * Retorna tarefas que possuem TODAS as tags informadas
     *
     * @param string $tagsRaw Tags separadas por vírgula
     * @return array
     */
    public function getByTags(string $tagsRaw): array
    {
        $tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
        if (empty($tags)) return $this->getAll();

        $this->db->select('t.*')
            ->from($this->table . ' t')
            ->join($this->tarefasTagsTable . ' tt', 't.id = tt.tarefa_id')
            ->join($this->tagsTable . ' tg', 'tt.tag_id = tg.id')
            ->where_in('tg.nome', $tags)
            ->group_by('t.id')
            ->having('COUNT(DISTINCT tg.nome) = ' . count($tags))
            ->order_by('t.criado_em', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Busca tarefas pelo título
     *
     * @param string $termo
     * @return array
     */
    public function searchByTitle(string $termo): array
    {
        return $this->db->like('titulo', $termo)
            ->order_by('criado_em', 'DESC')
            ->get($this->table)
            ->result();
    }

    /**
     * Busca tarefas pela descrição
     *
     * @param string $termo
     * @return array
     */
    public function searchByDescription(string $termo): array
    {
        return $this->db->like('descricao', $termo)
            ->order_by('criado_em', 'DESC')
            ->get($this->table)
            ->result();
    }

    /**
     * Busca combinada por título, descrição e tags
     *
     * @param string|null $termo
     * @param string|null $tagsRaw
     * @return array
     */
    public function search(string $termo = null, string $tagsRaw = null): array
    {
        $this->db->select('t.*')->from($this->table . ' t');

        // Filtra por tags, se houver
        if (!empty($tagsRaw)) {
            $tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
            if (!empty($tags)) {
                $this->db->join('tarefas_tags tt', 't.id = tt.tarefa_id')
                    ->join('tags tg', 'tt.tag_id = tg.id')
                    ->where_in('tg.nome', $tags)
                    ->group_by('t.id')
                    ->having('COUNT(DISTINCT tg.nome) = ' . count($tags));
            }
        }

        // Filtra por termo em título ou descrição
        if (!empty($termo)) {
            $this->db->group_start()
                ->like('titulo', $termo)
                ->or_like('descricao', $termo)
                ->group_end();
        }

        $this->db->order_by('t.criado_em', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Retorna estatísticas de tarefas
     *
     * @return array Associativo com total de tarefas por status
     */
    public function getStatistics(): array
    {
        $this->db->select('status, COUNT(*) as total')
            ->from($this->table)
            ->group_by('status');
        $query = $this->db->get()->result();

        // Inicializa os status para garantir que sempre existam as chaves
        $stats = [
            'Concluidas' => 0,
            'Em andamento' => 0,
            'Atrasadas' => 0
        ];

        foreach ($query as $row) {
            // Adiciona o status retornado pelo banco
            if ($row->status === 'Concluida') {
                $stats['Concluidas'] = (int)$row->total;
            } elseif ($row->status === 'Em andamento') {
                $stats['Em andamento'] = (int)$row->total;
            }
        }

        // Calcula atrasadas (prazo < hoje e status não concluída)
        $today = date('Y-m-d');
        $atrasadas = $this->db->where('status !=', 'Concluida')
            ->where('prazo <', $today)
            ->count_all_results($this->table);
        $stats['Atrasadas'] = (int)$atrasadas;

        return $stats;
    }
    public function updateStatus(int $id, array $data): bool
    {
        return $this->db->where('id', $id)->update('tarefas', $data);
    }
}
