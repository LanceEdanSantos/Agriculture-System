<?php

namespace App\Actions;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMessage
{
    /**
     * Send SMS through your TextBee-like gateway API.
     *
     * @param  string|array  $recipients
     * @param  string  $message
     * @return array
     *
     * @throws Exception
     */
    public function execute(string|array $recipients, string $message): array
    {
        $recipients = is_array($recipients) ? $recipients : [$recipients];

        $baseUrl  = config('services.textbee.base_url');
        $deviceId = config('services.textbee.device_id');
        $apiKey   = config('services.textbee.api_key');

        $url = "{$baseUrl}/gateway/devices/{$deviceId}/send-sms";

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
        ])->post($url, [
            'recipients' => $recipients,
            'message'    => $message,
        ]);

        if (! $response->successful()) {
            Log::error('Failed to send SMS: ' . $response->body());
            throw new Exception('Failed to send SMS: ' . $response->body());
        }
        Log::info('SMS sent successfully: ' . $response->body());
        return $response->json();
    }
}
