<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Log;

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
        $schedule->command('notif-active-member')
            ->everyMinute();
        $schedule->command('notif-target-paket')
            ->everyMinute();
        $schedule->command('deactive:member')
            ->everyMinute();
        $schedule->command('cache:clear')
            ->everyMinute();
        $schedule->command('route:clear')
            ->everyMinute();
        $schedule->command('view:clear')
            ->everyMinute();
        $schedule->command('view:cache')
            ->everyMinute();
        $schedule->command('event:clear')
            ->everyMinute();
        $schedule->command('event:cache')
            ->everyMinute();
        $schedule->command('optimize:clear')
            ->everyMinute();
        $schedule->call(function () {
            // Log::info('Cronjob berhasil dijalankan');
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
