<?php
/**
 * Calculates analytics and KPIs for the standard dashboard.
 */
class AnalyticsService
{
    private Payment $paymentModel;
    private Booking $bookingModel;
    private User $userModel;

    public function __construct()
    {
        $this->paymentModel = new Payment();
        $this->bookingModel = new Booking();
        $this->userModel    = new User();
    }

    public function getDashboardStats(): array
    {
        return [
            'total_users'    => $this->userModel->count(),
            'total_bookings' => $this->bookingModel->count(),
            'total_revenue'  => $this->paymentModel->totalRevenue(),
            'revenue_chart'  => $this->paymentModel->revenueByMonth()
        ];
    }
}
