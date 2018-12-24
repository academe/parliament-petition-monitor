<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ConstituencySignature;
use App\Constituency;
use App\CountrySignature;
use App\Country;
use App\Petition;
use App\FetchJob;
use App\PetitionData;
use Carbon\Carbon;
use Log;

class FetchVotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'petition:fetch-votes
        {petitionNumber : the integer petition number}
        {--dry-run : show data fetched without updating the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch current votes for a single petition';

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
     * TODO: check if a petition is still running before saving counts.
     *
     * @return mixed
     */
    public function handle()
    {
        $petitionNumber = (int)$this->argument('petitionNumber');

        $dryRun = $this->option('dry-run');

        $petition = Petition::where('petition_number', '=', $petitionNumber)->first();

        if ($petition === null) {
            $this->error(sprintf('Petition %d not found', $petitionNumber));
            return false;
        }

        $petitionData = new PetitionData($petitionNumber);

        if ($dryRun) {
            $this->info(json_encode(
                $petitionData->getDataItem(),
                JSON_PRETTY_PRINT
            ));
            return;
        }

        $updatedAt = Carbon::parse(
            $petitionData->getDataItem('data.attributes.updated_at')
        );

        $fetchJob = FetchJob::where('count_time', '=', $updatedAt)
            ->where('petition_id', '=', $petition->id)
            ->first();

        // Should be new; if not, then skip.

        if ($fetchJob !== null) {
            $this->error(sprintf(
                'Already fetched petition %d for %s',
                $petitionNumber,
                $updatedAt
            ));
            return false;
        }

        $fetchJob = FetchJob::create([
            'petition_id' => $petition->id,
            'count_time' => $updatedAt,
            'count' => $petitionData->getCount(),
        ]);

        $signaturesByConstituency = $petitionData->getDataItem(
            'data.attributes.signatures_by_constituency'
        );

        foreach ($signaturesByConstituency as $signatures) {
            $onsCode = $signatures->ons_code;

            $constituency = Constituency::where('ons_code', '=', $onsCode)
                ->first();

            if ($constituency === null) {
                // First encounter - insert it.

                $constituency = Constituency::create([
                    'ons_code' => $onsCode,
                    'name' => $signatures->name,
                    'mp' => $signatures->mp,
                ]);
            }

            $count = $signatures->signature_count;

            $constituencySignature = new ConstituencySignature([
                'count' => $count,
                'fetch_jobs_id' => $fetchJob->id,
                'constituency_id' => $constituency->id,
            ]);

            $constituencySignature->save();
        }

        $signaturesByCountry = $petitionData->getDataItem(
            'data.attributes.signatures_by_country'
        );

        foreach ($signaturesByCountry as $signatures) {
            $code = $signatures->code;

            $country = Country::where('code', '=', $code)
                ->first();

            if ($country === null) {
                // First encounter - insert it.

                $country = Country::create([
                    'code' => $code,
                    'name' => $signatures->name,
                ]);
            }

            $count = $signatures->signature_count;

            $countrySignature = new CountrySignature([
                'count' => $count,
                'fetch_jobs_id' => $fetchJob->id,
                'country_id' => $country->id,
            ]);

            $countrySignature->save();
        }
    }
}
