<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ConstituencySignature;
use App\CountrySignature;
use App\FetchJob;

class FixData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'petition:fix-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $constituencySignatures = ConstituencySignature::whereNotNull('count_time')
            ->whereNotNull('petition_id')
            ->whereNull('fetch_jobs_id')
            ->get();

        foreach ($constituencySignatures as $constituencySignature) {
            $fetchJob = FetchJob::where('count_time', '=', $constituencySignature->count_time)
                ->first();

            if ($fetchJob === null) {
                $fetchJob = FetchJob::create([
                    'count_time' => $constituencySignature->count_time,
                    'petition_id' => $constituencySignature->petition_id,
                ]);
            }

            $constituencySignature->fetch_jobs_id = $fetchJob->id;
            $constituencySignature->save();
        }

        $countrySignatures = CountrySignature::whereNotNull('count_time')
            ->whereNotNull('petition_id')
            ->whereNull('fetch_jobs_id')
            ->get();

        foreach ($countrySignatures as $countrySignature) {
            $fetchJob = FetchJob::where('count_time', '=', $countrySignature->count_time)
                ->first();

            if ($fetchJob === null) {
                $fetchJob = FetchJob::create([
                    'count_time' => $countrySignature->count_time,
                    'petition_id' => $countrySignature->petition_id,
                ]);
            }

            $countrySignature->fetch_jobs_id = $fetchJob->id;
            $countrySignature->save();
        }
    }
}
