<?php

namespace App\Jobs;

use App\Models\Form;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendZapMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct(
        public string $number,
        public string $message,
    )
    {
    }

    public function handle()
    {
        try {
            $response = Http::post('https://servicodisparo.com', [
                'phone' => $this->number,
                'message' => $this->message,
            ]);

            if (!$response->ok()) {
                Log::error("Request to zap service failed with status {$response->status()} and body: {$response->body()}");
            }
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());

            Log::error("Request to zap service failed:" . $e->getMessage());
        }
    }
}
