<?php
/**
 * Base Model – thin PDO wrapper shared by all models.
 */

abstract class Model
{
    protected PDO    $db;
    protected string $table  = '';
    protected string $pk     = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ── Generic finders ─────────────────────────────────────────────

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->pk} = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findAll(string $orderBy = ''): array
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        return $this->db->query($sql)->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    // ── Write helpers ────────────────────────────────────────────────

    /**
     * Insert a row. Returns the new last-insert-id.
     * @param array<string,mixed> $data
     */
    protected function insert(array $data): int
    {
        $cols        = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$cols}) VALUES ({$placeholders})");
        $stmt->execute(array_values($data));
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update rows matching $where conditions.
     * @param array<string,mixed> $data
     * @param array<string,mixed> $where  e.g. ['user_id' => 5]
     */
    protected function update(array $data, array $where): int
    {
        $set   = implode(', ', array_map(fn($c) => "{$c} = ?", array_keys($data)));
        $conds = implode(' AND ', array_map(fn($c) => "{$c} = ?", array_keys($where)));
        $stmt  = $this->db->prepare("UPDATE {$this->table} SET {$set} WHERE {$conds}");
        $stmt->execute([...array_values($data), ...array_values($where)]);
        return $stmt->rowCount();
    }

    /** Hard-delete a row by primary key. */
    protected function deleteById(int $id): int
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->pk} = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}
