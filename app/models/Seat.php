<?php
class Seat extends Model
{
    protected string $table = 'seats';
    protected string $pk    = 'seat_id';

    public function create(int $hallId, string $rowLabel, int $seatNumber): int
    {
        return $this->insert([
            'hall_id'     => $hallId,
            'row_label'   => strtoupper($rowLabel),
            'seat_number' => $seatNumber,
        ]);
    }

    public function getByHall(int $hallId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM seats WHERE hall_id = ? ORDER BY row_label, seat_number'
        );
        $stmt->execute([$hallId]);
        return $stmt->fetchAll();
    }

    /**
     * Return all seats for a hall with availability flag for a specific screening.
     * A seat is unavailable if a ticket already exists for (screening_id, seat_id).
     */
    public function getAvailability(int $hallId, int $screeningId): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*,
                    IF(t.ticket_id IS NULL, 1, 0) AS is_available
             FROM   seats s
             LEFT JOIN tickets t
                    ON  t.seat_id      = s.seat_id
                    AND t.screening_id = ?
             WHERE  s.hall_id = ?
             ORDER  BY s.row_label, s.seat_number'
        );
        $stmt->execute([$screeningId, $hallId]);
        return $stmt->fetchAll();
    }

    public function delete(int $id): int
    {
        return $this->deleteById($id);
    }
}
