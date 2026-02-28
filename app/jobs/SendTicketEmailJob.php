<?php
/**
 * Asynchronous job to send ticket emails with QR codes after successful booking.
 */
class SendTicketEmailJob
{
    public function handle(array $args): void
    {
        if (!isset($args[0])) {
            echo "Error: Booking ID required.\n";
            return;
        }

        $bookingId = (int) $args[0];

        $bookingModel = new Booking();
        $ticketModel  = new Ticket();
        
        $booking = $bookingModel->getDetail($bookingId);
        if (!$booking) {
            echo "Error: Booking {$bookingId} not found.\n";
            return;
        }

        $tickets = $ticketModel->getByBooking($bookingId);

        // Send Email
        $emailService = new EmailService();
        $emailService->sendTicketEmail(
            ['first_name' => $booking['first_name'], 'email' => $booking['email']], 
            $booking, 
            $tickets
        );

        echo "Success: Ticket email sent for Booking ID: {$bookingId}\n";
    }
}
