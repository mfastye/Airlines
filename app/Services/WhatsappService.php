<?php

namespace App\Services;

use App\Models\WhatsappGateway;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected ?WhatsappGateway $gateway = null;

    public function __construct()
    {
        $this->gateway = WhatsappGateway::where('status', 'active')->first();
    }

    /**
     * Send a text message via WhatsApp
     */
    public function sendMessage(string $phone, string $message, ?string $recipientName = null, ?string $relatedType = null, ?int $relatedId = null): ?WhatsappMessage
    {
        if (!$this->gateway) {
            Log::warning('No active WhatsApp gateway configured');
            return null;
        }

        $waMessage = WhatsappMessage::create([
            'whatsapp_gateway_id' => $this->gateway->id,
            'recipient_phone' => $phone,
            'recipient_name' => $recipientName,
            'message' => $message,
            'type' => 'text',
            'status' => 'pending',
            'direction' => 'outgoing',
            'related_type' => $relatedType,
            'related_id' => $relatedId,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->gateway->api_key,
                'Content-Type' => 'application/json',
            ])->post($this->gateway->api_url . '/api/sendText', [
                'chatId' => $this->formatPhone($phone) . '@c.us',
                'text' => $message,
                'session' => $this->gateway->session_name ?? 'default',
            ]);

            $waMessage->update([
                'status' => $response->successful() ? 'sent' : 'failed',
                'response_data' => $response->json(),
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());
            $waMessage->update(['status' => 'failed', 'response_data' => ['error' => $e->getMessage()]]);
        }

        return $waMessage;
    }

    /**
     * Send image with caption
     */
    public function sendImage(string $phone, string $imageUrl, string $caption = '', ?string $recipientName = null): ?WhatsappMessage
    {
        if (!$this->gateway) return null;

        $waMessage = WhatsappMessage::create([
            'whatsapp_gateway_id' => $this->gateway->id,
            'recipient_phone' => $phone,
            'recipient_name' => $recipientName,
            'message' => $caption,
            'media_url' => $imageUrl,
            'type' => 'image',
            'status' => 'pending',
            'direction' => 'outgoing',
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->gateway->api_key,
            ])->post($this->gateway->api_url . '/api/sendImage', [
                'chatId' => $this->formatPhone($phone) . '@c.us',
                'file' => ['url' => $imageUrl],
                'caption' => $caption,
                'session' => $this->gateway->session_name ?? 'default',
            ]);

            $waMessage->update([
                'status' => $response->successful() ? 'sent' : 'failed',
                'response_data' => $response->json(),
            ]);
        } catch (\Exception $e) {
            $waMessage->update(['status' => 'failed']);
        }

        return $waMessage;
    }

    /**
     * Send booking confirmation
     */
    public function sendBookingConfirmation(\App\Models\Booking $booking): void
    {
        $customer = $booking->customer;
        $flight = $booking->flight;
        $phone = $customer->whatsapp ?? $customer->phone;

        if (!$phone) return;

        $message = $this->buildBookingMessage($booking);
        $this->sendMessage($phone, $message, $customer->full_name, 'booking', $booking->id);
    }

    /**
     * Send booking status update
     */
    public function sendBookingStatusUpdate(\App\Models\Booking $booking, string $status): void
    {
        $customer = $booking->customer;
        $phone = $customer->whatsapp ?? $customer->phone;

        if (!$phone) return;

        $statusMessages = [
            'confirmed' => "Your booking {$booking->booking_reference} has been confirmed! ✈️",
            'cancelled' => "Your booking {$booking->booking_reference} has been cancelled.",
            'refunded' => "Your payment for booking {$booking->booking_reference} has been refunded.",
        ];

        $message = $statusMessages[$status] ?? "Booking {$booking->booking_reference} status updated to: {$status}";
        $this->sendMessage($phone, $message, $customer->full_name, 'booking', $booking->id);
    }

    /**
     * Handle incoming customer service messages (AI Chatbot)
     */
    public function handleIncomingMessage(string $phone, string $message): string
    {
        $lowered = mb_strtolower($message);

        // Smart keyword-based responses (built-in AI)
        if (str_contains($lowered, 'search') || str_contains($lowered, 'flight') || str_contains($lowered, 'رحل') || str_contains($lowered, 'بحث')) {
            return $this->getFlightSearchResponse($message);
        }

        if (str_contains($lowered, 'booking') || str_contains($lowered, 'حجز') || str_contains($lowered, 'reservation')) {
            return $this->getBookingInfoResponse($phone, $message);
        }

        if (str_contains($lowered, 'help') || str_contains($lowered, 'مساعد') || str_contains($lowered, 'support')) {
            return "Welcome to our support! How can we help you?\n\n"
                . "1. Search for flights\n"
                . "2. Check booking status\n"
                . "3. Contact support agent\n\n"
                . "Please reply with the number of your choice.";
        }

        if ($message === '1') return "Please visit our website to search for flights.";
        if ($message === '2') return "Please provide your booking reference number.";
        if ($message === '3') return "A support agent will contact you shortly.";

        return "Thank you for contacting us. How can we help?\n"
            . "Reply with:\n1. Search flights\n2. Booking status\n3. Contact support";
    }

    protected function getFlightSearchResponse(string $message): string
    {
        return "To search for flights, please visit our website or provide:\n"
            . "- Departure city\n- Arrival city\n- Travel date\n"
            . "And we'll find the best options for you!";
    }

    protected function getBookingInfoResponse(string $phone, string $message): string
    {
        // Try to find booking by reference in message
        preg_match('/BK-[A-Z0-9]+/', strtoupper($message), $matches);

        if (!empty($matches[0])) {
            $booking = \App\Models\Booking::where('booking_reference', $matches[0])->first();
            if ($booking) {
                return "Booking: {$booking->booking_reference}\n"
                    . "Status: {$booking->status}\n"
                    . "Flight: {$booking->flight->flight_number}\n"
                    . "Date: {$booking->flight->departure_time->format('Y-m-d H:i')}";
            }
        }

        return "Please provide your booking reference number (e.g., BK-XXXXXXXX) to check your booking status.";
    }

    protected function buildBookingMessage(\App\Models\Booking $booking): string
    {
        $flight = $booking->flight;
        return "Booking Confirmation\n"
            . "Reference: {$booking->booking_reference}\n"
            . "Flight: {$flight->flight_number}\n"
            . "From: {$flight->departureAirport->name_en}\n"
            . "To: {$flight->arrivalAirport->name_en}\n"
            . "Date: {$flight->departure_time->format('Y-m-d H:i')}\n"
            . "Status: {$booking->status}\n"
            . "Total: {$booking->total_price} {$booking->currency}";
    }

    protected function formatPhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
