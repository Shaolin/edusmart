<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $instanceId;
    protected $token;
    protected $baseUrl;
    protected $mode;

    public function __construct()
    {
        $this->instanceId = config('services.wapschat.instance_id');
        $this->token = config('services.wapschat.token');
        $this->baseUrl = config('services.wapschat.base_url');
        $this->mode = config('services.wapschat.mode', 'mock');
    }

    /**
     * Send a WhatsApp message (mock or live).
     */
    public function sendMessage($to, $message)
    {
        if ($this->mode === 'mock' || $this->instanceId === 'dummy_instance') {
            // Log mock message instead of sending
            Log::info("MOCK WhatsApp message to {$to}: {$message}");

            return [
                'status' => 'mock',
                'to' => $to,
                'message' => $message,
                'sent' => true,
                'info' => 'Mock mode enabled. Message not sent to WhatsApp.',
            ];
        }

        // Live mode: send to WapsChat API
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->post("{$this->baseUrl}/send-message", [
            'instance_id' => $this->instanceId,
            'to' => $to,
            'message' => $message,
        ]);

        if ($response->successful()) {
            Log::info("WhatsApp message sent to {$to}");
        } else {
            Log::error("WhatsApp send failed: " . $response->body());
        }

        return $response->json();
    }
}
