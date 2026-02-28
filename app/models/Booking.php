<?php
class Booking extends Model
{
    protected string $table = 'bookings';
    protected string $pk    = 'booking_id';

    public function create(int $customerId, int $screeningId, float $totalAmount): int
    {
        return $this->insert([
            'customer_id'  => $customerId,
            'screening_id' => $screeningId,
            'status'       => 'CONFIRMED',
            'total_amount' => $totalAmount,
        ]);
    }

    public function updateStatus(int $bookingId, string $status): int
    {
        return $this->update(['status' => $status], ['booking_id' => $bookingId]);
    }

    /** Bookings for a customer with screening + movie info. */
    public function getByCustomer(int $customerId): array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*, sc.start_time, sc.end_time, m.title AS movie_title,
                    c.name AS cinema_name, h.name AS hall_name
             FROM   bookings b
             JOIN   screenings sc ON sc.screening_id = b.screening_id
             JOIN   movies     m  ON m.movie_id      = sc.movie_id
             JOIN   halls      h  ON h.hall_id       = sc.hall_id
             JOIN   cinemas    c  ON c.cinema_id     = h.cinema_id
             WHERE  b.customer_id = ?
             ORDER  BY b.created_at DESC'
        );
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }

    /** Full booking detail (for confirmation / admin). */
    public function getDetail(int $bookingId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*,
                    u.first_name, u.last_name, u.email, u.phone,
                    sc.start_time, sc.end_time,
                    m.title AS movie_title, m.age_rating,
                    h.name AS hall_name,
                    c.name AS cinema_name, c.city, c.address_line1
             FROM   bookings   b
             JOIN   users      u  ON u.user_id       = b.customer_id
             JOIN   screenings sc ON sc.screening_id = b.screening_id
             JOIN   movies     m  ON m.movie_id      = sc.movie_id
             JOIN   halls      h  ON h.hall_id       = sc.hall_id
             JOIN   cinemas    c  ON c.cinema_id     = h.cinema_id
             WHERE  b.booking_id = ?
             LIMIT  1'
        );
        $stmt->execute([$bookingId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Paginated list of all bookings for admin. */
    public function getAll(int $page = 1, int $limit = ITEMS_PER_PAGE): array
    {
        $offset = ($page - 1) * $limit;
        $stmt   = $this->db->prepare(
            'SELECT b.*, u.first_name, u.last_name, m.title AS movie_title, sc.start_time
             FROM   bookings   b
             JOIN   users      u  ON u.user_id       = b.customer_id
             JOIN   screenings sc ON sc.screening_id = b.screening_id
             JOIN   movies     m  ON m.movie_id      = sc.movie_id
             ORDER  BY b.created_at DESC
             LIMIT  ? OFFSET ?'
        );
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
}
