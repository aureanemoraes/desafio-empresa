<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendDataViaWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $url,
        public array $data
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = Http::retry(3, 100)->post($this->url, $this->data);

            if (!$response->ok()) {
                if($response->notFound()) { // conforme soliticado na tarefa, tratamento especifico para o 404
                    Log::error("Webhook resource not found.");
                } else {
                    Log::error("Webhook request failed with status {$response->status()} and body: {$response->body()}");
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());
            Log::error("Failed to send data to webhook: " . $e->getMessage());
        }
    }
}
