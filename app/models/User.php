<?php
class User extends Model
{
    protected string $table = 'users';
    protected string $pk    = 'user_id';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        return $this->insert([
            'role_id'       => $data['role_id']   ?? ROLE_CUSTOMER,
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'email'         => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]),
            'phone'         => $data['phone'] ?? null,
            'is_active'     => 1,
        ]);
    }

    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    public function updateProfile(int $userId, array $data): int
    {
        return $this->update($data, ['user_id' => $userId]);
    }

    public function setActive(int $userId, bool $active): int
    {
        return $this->update(['is_active' => (int) $active], ['user_id' => $userId]);
    }

    public function setVerificationToken(int $userId, string $token): int
    {
        return $this->update(['verification_token' => $token], ['user_id' => $userId]);
    }

    public function findByToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE verification_token = ? LIMIT 1");
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function markVerified(int $userId): int
    {
        return $this->update([
            'email_verified_at' => date('Y-m-d H:i:s'),
            'verification_token' => null,
            'is_active' => 1
        ], ['user_id' => $userId]);
    }

    /** Paginate all customers (role = ROLE_CUSTOMER). */
    public function getCustomers(int $page = 1, int $limit = ITEMS_PER_PAGE): array
    {
        $offset = ($page - 1) * $limit;
        $stmt   = $this->db->prepare(
            'SELECT * FROM users WHERE role_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?'
        );
        $stmt->execute([ROLE_CUSTOMER, $limit, $offset]);
        return $stmt->fetchAll();
    }
}
