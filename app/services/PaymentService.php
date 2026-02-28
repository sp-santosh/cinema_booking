<?php
/**
 * Integrates Stripe payment flow with our database Payment models.
 */
class PaymentService
{
    private Payment $paymentModel;
    private StripeGatewayService $stripeService;
    private Booking $bookingModel;

    public function __construct()
    {
        $this->paymentModel  = new Payment();
        $this->stripeService = new StripeGatewayService();
        $this->bookingModel  = new Booking();
    }

    public function processSuccessfulPayment(int $bookingId, float $amount, string $method = 'CARD'): void
    {
        $this->paymentModel->create($bookingId, $amount, $method);
        // Ensure booking is marked as CONFIRMED.
        $this->bookingModel->updateStatus($bookingId, 'CONFIRMED');
        
        // Dispatch email job.
        // Example: exec("php cli/run-job.php SendTicketEmailJob $bookingId > /dev/null &");
    }
}
