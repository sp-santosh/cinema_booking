<?php
/**
 * Handles complex authentication logic and user management workflows.
 */
class AuthService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function registerUser(array $data): ?array
    {
        // Check if email already exists
        if ($this->userModel->findByEmail($data['email'])) {
            return null; // Email taken
        }

        $userId = $this->userModel->create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => $data['password'],
            'phone'      => $data['phone'] ?? null,
            'role_id'    => ROLE_CUSTOMER,
        ]);

        return $this->userModel->findById($userId);
    }

    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return null;
        }

        if (!$this->userModel->verifyPassword($password, $user['password_hash'])) {
            return null;
        }

        if (!(bool) $user['is_active']) {
            return null; // Deactivated account
        }

        return $user;
    }
}
