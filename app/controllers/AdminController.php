<?php
/**
 * AdminController – dashboard and user management.
 */
class AdminController extends Controller
{
    private User    $userModel;
    private Booking $bookingModel;
    private Payment $paymentModel;
    private Movie   $movieModel;

    public function __construct()
    {
        $this->userModel    = new User();
        $this->bookingModel = new Booking();
        $this->paymentModel = new Payment();
        $this->movieModel   = new Movie();
    }

    // GET /admin
    public function dashboard(): void
    {
        Middleware::admin();

        $stats = [
            'total_users'    => $this->userModel->count(),
            'total_movies'   => $this->movieModel->countActive(),
            'total_bookings' => $this->bookingModel->count(),
            'total_revenue'  => $this->paymentModel->totalRevenue(),
        ];

        $recentBookings = $this->bookingModel->getAll(1, 5);
        $revenueByMonth = $this->paymentModel->revenueByMonth();

        $this->render('admin/dashboard', [
            'stats'          => $stats,
            'recentBookings' => $recentBookings,
            'revenueByMonth' => $revenueByMonth,
            'pageTitle'      => 'Admin Dashboard',
        ]);
    }

    // GET /admin/users
    public function users(): void
    {
        Middleware::admin();

        $page      = max(1, (int) $this->query('page', 1));
        $customers = $this->userModel->getCustomers($page);

        $this->render('admin/users/index', [
            'customers' => $customers,
            'page'      => $page,
            'pageTitle' => 'Manage Users',
        ]);
    }

    // POST /admin/users/{id}/toggle
    public function toggleUser(string $id): void
    {
        Middleware::admin();
        Csrf::verify();

        $user = $this->userModel->findById((int) $id);
        if ($user) {
            $this->userModel->setActive((int) $id, !(bool) $user['is_active']);
        }

        $this->flash('success', 'User status updated.');
        $this->redirect(APP_URL . '/admin/users');
    }

    // GET /admin/bookings
    public function bookings(): void
    {
        Middleware::admin();

        $page     = max(1, (int) $this->query('page', 1));
        $bookings = $this->bookingModel->getAll($page);

        $this->render('admin/bookings/index', [
            'bookings'  => $bookings,
            'page'      => $page,
            'pageTitle' => 'All Bookings',
        ]);
    }

    // POST /admin/bookings/{id}/cancel
    public function cancelBooking(string $id): void
    {
        Middleware::admin();
        Csrf::verify();

        $this->bookingModel->updateStatus((int) $id, 'CANCELLED');
        $this->flash('success', 'Booking cancelled.');
        $this->redirect(APP_URL . '/admin/bookings');
    }
}
