<?php

namespace App\Console\Commands;

use App\Services\SystemService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendEmailWorksheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emailWorksheet:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email Worksheet Queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        SystemService::processEmailQueue();
        Log::info("Process Email Queue executed");
    }
}
