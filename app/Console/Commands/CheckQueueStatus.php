<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class CheckQueueStatus extends Command
{
    protected $signature = 'queue:status';
    protected $description = 'Check queue configuration and status';

    public function handle(): int
    {
        $this->info('📊 Queue Status Check');
        $this->line(str_repeat('=', 50));

        $connection = config('queue.default');
        $this->line("Queue Connection: <fg=cyan>{$connection}</>");

        if ($connection === 'sync') {
            $this->warn('⚠️  Using sync queue - jobs run immediately (no worker needed)');
            $this->line('   If calls are stuck, check application logs');
        } else {
            $this->info('✅ Using async queue - worker must be running');
            
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();
            
            $this->line("Pending Jobs: <fg=yellow>{$pendingJobs}</>");
            $this->line("Failed Jobs: <fg=red>{$failedJobs}</>");
            
            if ($failedJobs > 0) {
                $this->newLine();
                $this->warn('⚠️  You have failed jobs! Run: php artisan queue:failed');
            }
        }

        $this->newLine();
        $this->info('🔑 Environment Check:');
        
        $apiKey = config('services.assemblyai.key');
        if ($apiKey) {
            $masked = substr($apiKey, 0, 8) . '...' . substr($apiKey, -4);
            $this->line("AssemblyAI Key: <fg=green>{$masked}</> ✅");
        } else {
            $this->error('AssemblyAI Key: ❌ NOT SET');
            $this->line('   Set ASSEMBLYAI_API_KEY in your .env file');
        }

        $this->newLine();
        $this->info('📝 To process pending calls manually:');
        $this->line('   php artisan calls:process-pending');
        $this->line('   php artisan calls:process-pending --id=1');

        return Command::SUCCESS;
    }
}

