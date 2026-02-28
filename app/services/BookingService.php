<?php
/**
 * Orchestrates booking creation, seat availability checks, and ticket generation.
 */
class BookingService
{
    private Booking $bookingModel;
    private Ticket $ticketModel;
    private Screening $screeningModel;
    private Seat $seatModel;

    public function __construct()
    {
        $this->bookingModel   = new Booking();
        $this->ticketModel    = new Ticket();
        $this->screeningModel = new Screening();
        $this->seatModel      = new Seat();
    }

    public function createBooking(int $customerId, int $screeningId, array $seatIds): ?int
    {
        $screening = $this->screeningModel->getDetail($screeningId);
        if (!$screening) {
            throw new Exception("Screening not found.");
        }

        // Validate seat availability
        foreach ($seatIds as $seatId) {
            if ($this->ticketModel->isSeatTaken($screeningId, (int) $seatId)) {
                throw new Exception("One or more seats selected are no longer available.");
            }
        }

        $ticketPrice = DEFAULT_TICKET_PRICE;
        $totalAmount = $ticketPrice * count($seatIds);

        // Transaction simulation (Ideally handled within DB model or Database class)
        // 1. Create Booking
        $bookingId = $this->bookingModel->create($customerId, $screeningId, $totalAmount);

        // 2. Create Tickets
        foreach ($seatIds as $seatId) {
            $this->ticketModel->create($bookingId, $screeningId, (int) $seatId, $ticketPrice);
        }

        return $bookingId;
    }

    public function cancelBooking(int $bookingId, int $customerId = null): bool
    {
        $booking = $this->bookingModel->getDetail($bookingId);
        
        if (!$booking) {
            return false;
        }

        if ($customerId !== null && (int)$booking['customer_id'] !== $customerId) {
            return false;
        }

        if ($booking['status'] === 'CANCELLED') {
            return true;
        }

        $this->bookingModel->updateStatus($bookingId, 'CANCELLED');
        // If payment was made, payment service would initiate refund here.
        return true;
    }
}
