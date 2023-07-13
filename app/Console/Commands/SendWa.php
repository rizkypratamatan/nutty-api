<?php

namespace App\Console\Commands;

use App\Services\SystemService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process WA Queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        SystemService::processWaQueue();
        Log::info("Process WA Queue executed");
    }
}
