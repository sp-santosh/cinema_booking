<?php
class Role extends Model
{
    protected string $table = 'roles';
    protected string $pk    = 'role_id';

    public function findByName(string $name): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM roles WHERE role_name = ? LIMIT 1');
        $stmt->execute([$name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $roleName): int
    {
        return $this->insert(['role_name' => $roleName]);
    }
}
