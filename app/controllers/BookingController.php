<?php
/**
 * BookingController – seat selection, booking creation, confirmation, user history.
 */
class BookingController extends Controller
{
    private Booking   $bookingModel;
    private Ticket    $ticketModel;
    private Screening $screeningModel;
    private Seat      $seatModel;
    private Payment   $paymentModel;

    public function __construct()
    {
        $this->bookingModel   = new Booking();
        $this->ticketModel    = new Ticket();
        $this->screeningModel = new Screening();
        $this->seatModel      = new Seat();
        $this->paymentModel   = new Payment();
    }

    // GET /bookings  – logged-in user's booking history
    public function index(): void
    {
        $this->requireAuth();
        $bookings = $this->bookingModel->getByCustomer(Auth::id());

        $this->render('booking/history', [
            'bookings'  => $bookings,
            'pageTitle' => 'My Bookings',
        ]);
    }

    // GET /screenings/{id}/book  – seat selection page
    public function create(string $screeningId): void
    {
        $this->requireAuth();

        $screening = $this->screeningModel->getDetail((int) $screeningId);
        if (!$screening) {
            http_response_code(404);
            $this->render('errors/404');
            return;
        }

        $seats = $this->seatModel->getAvailability($screening['hall_id'], (int) $screeningId);

        $this->render('booking/select_seats', [
            'screening'  => $screening,
            'seats'      => $seats,
            'pageTitle'  => 'Select Your Seats',
        ]);
    }

    // POST /bookings  – create booking + tickets
    public function store(): void
    {
        $this->requireAuth();
        Csrf::verify();

        $screeningId = (int) $this->input('screening_id');
        $seatIds     = $_POST['seat_ids'] ?? [];   // array of selected seat IDs

        if (empty($seatIds) || !is_array($seatIds)) {
            $this->flash('error', 'Please select at least one seat.');
            $this->redirect(APP_URL . '/screenings/' . $screeningId . '/book');
            return;
        }

        $screening = $this->screeningModel->getDetail($screeningId);
        if (!$screening) {
            $this->redirect(APP_URL . '/movies');
            return;
        }

        // Validate seat availability.
        foreach ($seatIds as $seatId) {
            if ($this->ticketModel->isSeatTaken($screeningId, (int) $seatId)) {
                $this->flash('error', 'One or more seats you selected are no longer available. Please try again.');
                $this->redirect(APP_URL . '/screenings/' . $screeningId . '/book');
                return;
            }
        }

        $ticketPrice = DEFAULT_TICKET_PRICE;
        $totalAmount = $ticketPrice * count($seatIds);

        // Create booking.
        $bookingId = $this->bookingModel->create(Auth::id(), $screeningId, $totalAmount);

        // Create individual tickets.
        foreach ($seatIds as $seatId) {
            $this->ticketModel->create($bookingId, $screeningId, (int) $seatId, $ticketPrice);
        }

        $this->flash('success', 'Booking confirmed! Enjoy the show 🎬');
        $this->redirect(APP_URL . '/bookings/' . $bookingId . '/confirmation');
    }

    // GET /bookings/{id}/confirmation
    public function confirmation(string $id): void
    {
        $this->requireAuth();

        $booking = $this->bookingModel->getDetail((int) $id);
        if (!$booking || (int) $booking['customer_id'] !== Auth::id()) {
            $this->redirect(APP_URL . '/bookings');
            return;
        }

        $tickets = $this->ticketModel->getByBooking((int) $id);
        $payment = $this->paymentModel->getByBooking((int) $id);

        $this->render('booking/confirmation', [
            'booking'   => $booking,
            'tickets'   => $tickets,
            'payment'   => $payment,
            'pageTitle' => 'Booking Confirmed',
        ]);
    }

    // GET /bookings/{id}  – detail view
    public function show(string $id): void
    {
        $this->requireAuth();

        $booking = $this->bookingModel->getDetail((int) $id);
        if (!$booking || (int) $booking['customer_id'] !== Auth::id()) {
            $this->redirect(APP_URL . '/bookings');
            return;
        }

        $tickets = $this->ticketModel->getByBooking((int) $id);

        $this->render('booking/show', [
            'booking'   => $booking,
            'tickets'   => $tickets,
            'pageTitle' => 'Booking #' . $id,
        ]);
    }

    // POST /bookings/{id}/cancel
    public function cancel(string $id): void
    {
        $this->requireAuth();
        Csrf::verify();

        $booking = $this->bookingModel->getDetail((int) $id);
        if (!$booking || (int) $booking['customer_id'] !== Auth::id()) {
            $this->redirect(APP_URL . '/bookings');
            return;
        }

        if ($booking['status'] === 'CANCELLED') {
            $this->flash('info', 'This booking is already cancelled.');
            $this->redirect(APP_URL . '/bookings');
            return;
        }

        $this->bookingModel->updateStatus((int) $id, 'CANCELLED');
        $this->flash('success', 'Your booking has been cancelled.');
        $this->redirect(APP_URL . '/bookings');
    }
}
