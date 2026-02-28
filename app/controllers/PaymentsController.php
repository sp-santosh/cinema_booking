<?php
/**
 * PaymentsController – Stripe checkout & webhook handler.
 */
class PaymentsController extends Controller
{
    private Payment $paymentModel;
    private Booking $bookingModel;

    public function __construct()
    {
        $this->paymentModel = new Payment();
        $this->bookingModel = new Booking();
    }

    // POST /payments/checkout  – initiate Stripe session (placeholder)
    public function checkout(): void
    {
        $this->requireAuth();
        Csrf::verify();

        $bookingId = (int) $this->input('booking_id');
        $booking   = $this->bookingModel->getDetail($bookingId);

        if (!$booking || (int) $booking['customer_id'] !== Auth::id()) {
            $this->redirect(APP_URL . '/bookings');
            return;
        }

        // TODO: Integrate Stripe here via StripeGatewayService.
        // For now, record as a direct CARD payment.
        $this->paymentModel->create($bookingId, (float) $booking['total_amount'], 'CARD');

        $this->flash('success', 'Payment recorded successfully.');
        $this->redirect(APP_URL . '/bookings/' . $bookingId . '/confirmation');
    }

    // POST /payments/webhook  – Stripe webhook endpoint (no auth / no CSRF)
    public function webhook(): void
    {
        // TODO: verify Stripe-Signature header & process event types.
        http_response_code(200);
        echo json_encode(['received' => true]);
        exit;
    }
}
