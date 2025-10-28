<?php

namespace App\Jobs;

use App\Models\Webhook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWebhookEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $event;
    public array $payload;

    public function __construct(string $event, array $payload)
    {
        $this->event = $event;
        $this->payload = $payload;
    }

    public function handle(): void
    {
        $hooks = Webhook::where('active', true)->where('event', $this->event)->get();
        foreach ($hooks as $hook) {
            try {
                $body = json_encode(['event' => $this->event, 'data' => $this->payload, 'timestamp' => now()->toIso8601String()]);
                $request = Http::timeout(10);
                if ($hook->secret) {
                    $sig = hash_hmac('sha256', $body, $hook->secret);
                    $request = $request->withHeaders(['X-Webhook-Signature' => $sig]);
                }
                $response = $request->withHeaders(['Content-Type' => 'application/json'])->post($hook->target_url, json_decode($body, true));
                if (!$response->successful()) {
                    Log::warning('Webhook delivery failed', ['url' => $hook->target_url, 'status' => $response->status()]);
                }
            } catch (\Throwable $e) {
                Log::error('Webhook delivery exception', ['url' => $hook->target_url, 'error' => $e->getMessage()]);
            }
        }
    }
}

