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

        // TODO: read the surveys and frequecies for each from the database.

        // Leave the EU without a deal in March 2019.
        $schedule->command('petition:fetch-votes 229963')
            //->everyMinute()
            ->everyTenMinutes()
            ;

        // STOP BREXIT
        $schedule->command('petition:fetch-votes 226509')
            //->everyMinute()
            ->everyTenMinutes()
            ;
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
