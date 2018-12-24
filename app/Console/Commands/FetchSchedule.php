<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Petition;
use Log;

class FetchSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'petition:fetch-schedule
        {schedule-name : The schedule name to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all petition job fetches taht meet a names schedule';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $schedule = $this->argument('schedule-name');

        // TODO: remove duplicate validation of schedule name
        // across multipel commands.

        $scheduleConstant = sprintf(
            '%s::SCHEDULE_%s',
            Petition::class,
            str_replace('-', '_', strtoupper($schedule))
        );

        if (! defined($scheduleConstant)) {
            $this->error(sprintf(
                'Schedule type "%s" not valid',
                $schedule
            ));
            return false;
        }

        $petitions = Petition::where('schedule', '=', $schedule)->get();

        $message = sprintf(
            'Fetching for %d petitions on the %s schedule',
            $petitions->count(),
            $schedule
        );

        $this->info($message);
        Log::info($message);

        foreach ($petitions as $petition) {
            //Log::info('Number: ' . $petition->petition_number);

            $this->call('petition:fetch-votes', [
                'petitionNumber' => $petition->petition_number,
            ]);
        }
    }
}
