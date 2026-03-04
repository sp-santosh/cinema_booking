<?php
/**
 * Asynchronous job to send an email verification link.
 */
class SendVerificationEmailJob
{
    public function handle(array $args): void
    {
        if (!isset($args[0])) {
            echo "Error: User ID required.\n";
            return;
        }

        $userId = (int) $args[0];
        $userModel = new User();
        $user = $userModel->findById($userId);

        if (!$user) {
            echo "Error: User {$userId} not found.\n";
            return;
        }

        // Generate and store verification token
        $token = bin2hex(random_bytes(32));
        $userModel->setVerificationToken($userId, $token);

        $emailService = new EmailService();
        $emailService->sendVerificationEmail($user, $token);
        
        echo "Success: Verification email sent to User ID: {$userId}\n";
    }
}
