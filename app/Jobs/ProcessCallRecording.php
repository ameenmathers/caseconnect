<?php

namespace App\Jobs;

use App\Models\Call;
use App\Services\CallAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCallRecording implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 600;

    public function __construct(
        public Call $call
    ) {}

    public function handle(CallAnalysisService $analysisService): void
    {
        Log::info('Processing call recording', [
            'call_id' => $this->call->id,
            'filename' => $this->call->filename,
            'status' => $this->call->status,
        ]);

        try {
            $analysisService->process($this->call);
            
            Log::info('Call processing completed', [
                'call_id' => $this->call->id,
                'lead_score' => $this->call->lead_score,
            ]);
        } catch (\Exception $e) {
            Log::error('Call processing failed', [
                'call_id' => $this->call->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Call processing job failed permanently', [
            'call_id' => $this->call->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $this->call->markAsFailed($exception->getMessage());
    }
}

