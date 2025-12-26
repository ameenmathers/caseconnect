<?php

namespace App\Console\Commands;

use App\Models\Call;
use App\Services\CallAnalysisService;
use Illuminate\Console\Command;

class ProcessPendingCalls extends Command
{
    protected $signature = 'calls:process-pending {--id= : Process specific call ID}';
    protected $description = 'Process pending call recordings (useful for debugging)';

    public function handle(CallAnalysisService $analysisService): int
    {
        $callId = $this->option('id');

        if ($callId) {
            $call = Call::find($callId);
            
            if (!$call) {
                $this->error("Call #{$callId} not found");
                return Command::FAILURE;
            }

            $this->info("Processing call #{$call->id}: {$call->original_filename}");
            
            try {
                $analysisService->process($call);
                $this->info("✅ Call processed successfully! Score: {$call->lead_score}");
                return Command::SUCCESS;
            } catch (\Exception $e) {
                $this->error("❌ Error: {$e->getMessage()}");
                $this->line("File: {$e->getFile()}:{$e->getLine()}");
                return Command::FAILURE;
            }
        }

        $pendingCalls = Call::whereIn('status', ['pending', 'processing'])->get();

        if ($pendingCalls->isEmpty()) {
            $this->info('No pending calls found');
            return Command::SUCCESS;
        }

        $this->info("Found {$pendingCalls->count()} pending call(s)");

        foreach ($pendingCalls as $call) {
            $this->line("Processing call #{$call->id}: {$call->original_filename}");
            
            try {
                $analysisService->process($call);
                $this->info("  ✅ Completed - Score: {$call->lead_score}");
            } catch (\Exception $e) {
                $this->error("  ❌ Failed: {$e->getMessage()}");
            }
        }

        return Command::SUCCESS;
    }
}

