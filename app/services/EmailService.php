<?php
/**
 * Helper to dispatch emails via SMTP/Mail API.
 */
class EmailService
{
    private array $config;

    public function __construct()
    {
        $this->config = require APP_PATH . '/config/mail.php';
    }

    private function getLayoutWrapper(string $title, string $content): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7f9; margin: 0; padding: 0; color: #333; }
        .wrapper { width: 100%; padding: 40px 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .header { background: #0a0b10; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 2px; }
        .body { padding: 40px; line-height: 1.6; }
        .body h2 { margin-top: 0; color: #0a0b10; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #999; }
        .btn { display: inline-block; padding: 14px 30px; background: #e50914; color: #ffffff !important; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
        .ticket-info { background: #f8f9fa; border-left: 4px solid #e50914; padding: 20px; margin: 20px 0; border-radius: 0 4px 4px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>🎬 CINEBOOK</h1>
            </div>
            <div class="body">
                $content
            </div>
            <div class="footer">
                &copy; {{year}} CineBook Cinema. All rights reserved.<br>
                London, UK
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function generateButton(string $text, string $url): string
    {
        return "<a href='$url' class='btn'>$text</a>";
    }

    public function send(string $to, string $subject, string $htmlBody): bool
    {
        if (empty($this->config['username']) || empty($this->config['password'])) {
            error_log("[MOCK EMAIL] To: $to | Subject: $subject (Set MAIL_USERNAME/PASSWORD in config/mail.php to send real mail)");
            return true;
        }

        $mailer = new SMTPSender($this->config);
        return $mailer->send($to, $subject, $htmlBody);
    }

    public function sendTicketEmail(array $user, array $booking, array $tickets): void
    {
        $qrService = new QRService();
        $qrCodeUrl = $qrService->generateForTicket("BOOKING-" . $booking['booking_id']);

        $seats = implode(', ', array_map(fn($t) => $t['row_label'] . $t['seat_number'], $tickets));

        $content = "<h2>Your Tickets are Ready!</h2>";
        $content .= "<p>Hi {$user['first_name']}, get ready for the show. Your booking for <strong>{$booking['movie_title']}</strong> is confirmed.</p>";
        
        $content .= "<div class='ticket-info'>";
        $content .= "<strong>📍 Cinema:</strong> {$booking['cinema_name']}<br>";
        $content .= "<strong>🎭 Hall:</strong> {$booking['hall_name']}<br>";
        $content .= "<strong>🗓 Date:</strong> " . date('D j M Y, H:i', strtotime($booking['start_time'])) . "<br>";
        $content .= "<strong>🎟 Seats:</strong> $seats";
        $content .= "</div>";

        $content .= "<div style='text-align: center; margin: 30px 0;'>";
        $content .= "<p>Scan this QR code at the entrance:</p>";
        $content .= "<img src='{$qrCodeUrl}' alt='Ticket QR Code' style='width: 200px; height: 200px;' />";
        $content .= "</div>";

        $content .= "<p>Visit the cinema link below for directions and more info:</p>";
        $content .= $this->generateButton('View Online Ticket', APP_URL . "/bookings/{$booking['booking_id']}/confirmation");

        $html = str_replace('{{year}}', date('Y'), $this->getLayoutWrapper('Your CineBook Tickets', $content));
        
        $this->send($user['email'], 'Your Cinema Tickets - ' . $booking['movie_title'], $html);
    }

    public function sendVerificationEmail(array $user, string $token): void
    {
        $verifyUrl = APP_URL . "/verify?token={$token}";

        $content = "<h2>Welcome to CineBook, {$user['first_name']}!</h2>";
        $content .= "<p>Thank you for joining our premiere cinema community. To start booking tickets, we need you to confirm your email address.</p>";
        $content .= "<div style='text-align: center;'>";
        $content .= $this->generateButton('Confirm Email Address', $verifyUrl);
        $content .= "</div>";
        $content .= "<p>If the button above doesn't work, you can copy and paste this link into your browser:</p>";
        $content .= "<p style='font-size: 12px; color: #666;'>$verifyUrl</p>";

        $html = str_replace('{{year}}', date('Y'), $this->getLayoutWrapper('Confirm your Email', $content));
        
        $this->send($user['email'], 'Confirm your CineBook Account', $html);
    }

    public function sendPasswordResetEmail(array $user, string $token): void
    {
        $resetUrl = APP_URL . "/reset-password?token={$token}";

        $content = "<h2>Password Reset Request</h2>";
        $content .= "<p>Hi {$user['first_name']}, we received a request to reset your CineBook account password.</p>";
        $content .= "<p>Click the button below to choose a new password. This link will expire in 1 hour.</p>";
        $content .= "<div style='text-align: center;'>";
        $content .= $this->generateButton('Reset Password', $resetUrl);
        $content .= "</div>";
        $content .= "<p>If you didn't request a password reset, you can safely ignore this email.</p>";
        $content .= "<p style='font-size: 12px; color: #666;'>If the button doesn't work, visit:<br>$resetUrl</p>";

        $html = str_replace('{{year}}', date('Y'), $this->getLayoutWrapper('Reset your Password', $content));
        
        $this->send($user['email'], 'Reset your CineBook Password', $html);
    }
}
