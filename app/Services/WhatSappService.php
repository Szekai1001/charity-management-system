<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected Client $client;
    protected string $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $this->from = config('services.twilio.whatsapp_from');
    }

    /**
     * Send a WhatsApp message.
     *
     * @param string $to Phone number in E.164 e.g. 60123456789 (without leading +)
     * @param string $message
     * @return bool true on success
     */
    public function sendMessage(string $to, string $message): bool
    {
        try {
            // Strip non-digits (just numbers)
            $toNumber = preg_replace('/\D+/', '', $to);

            // If the number starts with 0, replace it with your country code (e.g., Malaysia +60)
            if (strpos($toNumber, '0') === 0) {
                $toNumber = '60' . substr($toNumber, 1); // 0123456789 -> 60123456789
            }

            // Send WhatsApp message in E.164 format
            $this->client->messages->create("whatsapp:+{$toNumber}", [
                'from' => $this->from, // already "whatsapp:+14155238886"
                'body' => $message,
            ]);

            Log::info("✅ WhatsApp message sent to +{$toNumber}");

            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage(), [
                'to' => $to,
                'message' => $message
            ]);
            return false;
        }
    }
}
