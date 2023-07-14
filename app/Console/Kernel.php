<?php

namespace App\Console;

use App\Services\SystemService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function() {

            SystemService::generateUnclaimedDepositQueue(Carbon::now()->format("Y-m-d"));

            Log::info("Generate unclaimed deposit queue scheduler running");

        })->dailyAt("1:00");

        $schedule->call(function() {

            SystemService::deleteOldTransaction();

            Log::info("Delete old transaction scheduler running");

        })->dailyAt("21:00");

        $schedule->call(function() {

            SystemService::generateDepositReport();

            Log::info("Generate deposit report scheduler executed");

        })->everyThreeMinutes();

        $schedule->call(function() {

            SystemService::syncWebsiteTransaction();

            Log::info("Sync website transaction scheduler executed");

        })->everyFiveMinutes();

        // $schedule->command('email:send')->everyFiveMinutes();
        $schedule->command('sms:send')->everyFifteenMinutes();
        $schedule->command('wa:send')->everyThirtyMinutes();
        
        // $schedule->call(function() {

        //     SystemService::processSmsQueue();

        //     Log::info("Process Sms Queue executed");

        // })->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
