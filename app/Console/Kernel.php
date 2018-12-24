<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        // /opt/plesk/php/7.2/bin/php
        // /var/www/vhosts/acadweb.co.uk/httpdocs/petitions/artisan

        // Read the surveys and frequecies for each from the database.
        // For each schedule frequency, see what needs to be run.

        $schedule->command('petition:fetch-schedule day')
            ->daily();

        $schedule->command('petition:fetch-schedule hour')
            ->hourly();

        $schedule->command('petition:fetch-schedule half-hour')
            ->everyThirtyMinutes();

        $schedule->command('petition:fetch-schedule quarter-hour')
            ->everyFifteenMinutes();

        $schedule->command('petition:fetch-schedule ten-minutes')
            ->everyTenMinutes();
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
