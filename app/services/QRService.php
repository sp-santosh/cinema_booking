<?php
/**
 * Generates QR codes for tickets.
 */
class QRService
{
    /**
     * Genereate a mock QR code image URL or data URI.
     * In a real implementation, you might use an external API like `chrt.apis.google.com`
     * or a library like `endroid/qr-code`.
     */
    public function generateForTicket(string $ticketToken): string
    {
        // Using a free public API to generate an image URL 
        return "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($ticketToken);
    }
}
