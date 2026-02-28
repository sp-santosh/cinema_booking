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

        // Mock verification token generation
        $token = bin2hex(random_bytes(32));
        $verifyUrl = APP_URL . "/verify?token={$token}";

        $emailService = new EmailService();
        $htmlBody = "<h1>Welcome to CineBook, {$user['first_name']}!</h1>";
        $htmlBody .= "<p>Please verify your email address by clicking the link below:</p>";
        $htmlBody .= "<a href='{$verifyUrl}'>Verify Email</a>";

        $emailService->send($user['email'], "Please verify your email", $htmlBody);
        
        echo "Success: Verification email sent to User ID: {$userId}\n";
    }
}
