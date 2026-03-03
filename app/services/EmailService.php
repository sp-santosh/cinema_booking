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

        $body = "<h1>Your CineBook Tickets!</h1>";
        $body .= "<p>Hi {$user['first_name']}, your booking for <strong>{$booking['movie_title']}</strong> is confirmed.</p>";
        $body .= "<img src='{$qrCodeUrl}' alt='QR Code' />";

        $this->send($user['email'], 'Your Cinema Tickets', $body);
    }
}
