<?php
class Ticket extends Model
{
    protected string $table = 'tickets';
    protected string $pk    = 'ticket_id';

    public function create(int $bookingId, int $screeningId, int $seatId, float $price): int
    {
        return $this->insert([
            'booking_id'   => $bookingId,
            'screening_id' => $screeningId,
            'seat_id'      => $seatId,
            'price'        => $price,
        ]);
    }

    /** All tickets for a booking, with seat info. */
    public function getByBooking(int $bookingId): array
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, s.row_label, s.seat_number
             FROM   tickets t
             JOIN   seats   s ON s.seat_id = t.seat_id
             WHERE  t.booking_id = ?
             ORDER  BY s.row_label, s.seat_number'
        );
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll();
    }

    /** Check if a seat is already booked for a screening. */
    public function isSeatTaken(int $screeningId, int $seatId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM tickets WHERE screening_id = ? AND seat_id = ?'
        );
        $stmt->execute([$screeningId, $seatId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
