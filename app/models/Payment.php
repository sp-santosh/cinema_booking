<?php
class Payment extends Model
{
    protected string $table = 'payments';
    protected string $pk    = 'payment_id';

    public function create(int $bookingId, float $amount, string $method = 'CARD'): int
    {
        return $this->insert([
            'booking_id'     => $bookingId,
            'amount'         => $amount,
            'method'         => $method,
            'payment_status' => 'PAID',
            'paid_at'        => date('Y-m-d H:i:s'),
        ]);
    }

    public function getByBooking(int $bookingId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE booking_id = ? LIMIT 1');
        $stmt->execute([$bookingId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateStatus(int $paymentId, string $status): int
    {
        return $this->update(['payment_status' => $status], ['payment_id' => $paymentId]);
    }

    /** Revenue totals for analytics. */
    public function totalRevenue(): float
    {
        return (float) $this->db->query(
            "SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'PAID'"
        )->fetchColumn();
    }

    public function revenueByMonth(): array
    {
        return $this->db->query(
            "SELECT DATE_FORMAT(paid_at, '%Y-%m') AS month, SUM(amount) AS revenue
             FROM   payments
             WHERE  payment_status = 'PAID'
             GROUP  BY month
             ORDER  BY month DESC
             LIMIT  12"
        )->fetchAll();
    }
}
