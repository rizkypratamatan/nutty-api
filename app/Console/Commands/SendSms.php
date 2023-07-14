<?php

namespace App\Console\Commands;

use App\Services\SystemService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process SMS Queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        SystemService::processSmsQueue();
        Log::info("Process Sms Queue executed");
    }
}
