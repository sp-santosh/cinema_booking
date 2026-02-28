<?php
class Screening extends Model
{
    protected string $table = 'screenings';
    protected string $pk    = 'screening_id';

    public function create(array $data): int
    {
        return $this->insert([
            'movie_id'   => (int) $data['movie_id'],
            'hall_id'    => (int) $data['hall_id'],
            'start_time' => $data['start_time'],
            'end_time'   => $data['end_time'],
            'status'     => $data['status'] ?? 'SCHEDULED',
        ]);
    }

    public function updateScreening(int $id, array $data): int
    {
        return $this->update($data, ['screening_id' => $id]);
    }

    public function delete(int $id): int
    {
        return $this->deleteById($id);
    }

    /** Upcoming screenings for a movie, including hall + cinema info. */
    public function getUpcomingByMovie(int $movieId): array
    {
        $stmt = $this->db->prepare(
            'SELECT sc.*, h.name AS hall_name, c.name AS cinema_name, c.city
             FROM   screenings sc
             JOIN   halls    h ON h.hall_id   = sc.hall_id
             JOIN   cinemas  c ON c.cinema_id = h.cinema_id
             WHERE  sc.movie_id = ?
               AND  sc.start_time > NOW()
               AND  sc.status = \'SCHEDULED\'
             ORDER  BY sc.start_time'
        );
        $stmt->execute([$movieId]);
        return $stmt->fetchAll();
    }

    /** All upcoming screenings (for home page / search). */
    public function getUpcoming(int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            'SELECT sc.*, m.title, m.age_rating, m.movie_rating,
                    h.name AS hall_name, c.name AS cinema_name, c.city
             FROM   screenings sc
             JOIN   movies  m ON m.movie_id = sc.movie_id
             JOIN   halls   h ON h.hall_id  = sc.hall_id
             JOIN   cinemas c ON c.cinema_id = h.cinema_id
             WHERE  sc.start_time > NOW()
               AND  sc.status = \'SCHEDULED\'
               AND  m.is_active = 1
             ORDER  BY sc.start_time
             LIMIT  ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getDetail(int $screeningId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT sc.*, m.title, m.duration_minutes, m.age_rating, m.movie_rating, m.description,
                    h.name AS hall_name, h.hall_id,
                    c.name AS cinema_name, c.city, c.address_line1, c.postcode
             FROM   screenings sc
             JOIN   movies  m ON m.movie_id  = sc.movie_id
             JOIN   halls   h ON h.hall_id   = sc.hall_id
             JOIN   cinemas c ON c.cinema_id = h.cinema_id
             WHERE  sc.screening_id = ?
             LIMIT  1'
        );
        $stmt->execute([$screeningId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function countAvailableSeats(int $screeningId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM seats s
             LEFT JOIN tickets t ON t.seat_id = s.seat_id AND t.screening_id = ?
             JOIN halls h ON h.hall_id = s.hall_id
             JOIN screenings sc ON sc.screening_id = ? AND sc.hall_id = h.hall_id
             WHERE t.ticket_id IS NULL'
        );
        $stmt->execute([$screeningId, $screeningId]);
        return (int) $stmt->fetchColumn();
    }
}
