<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Petition;
use App\FetchJob;
use App\PetitionData;
use Carbon\Carbon;

class CreatePetition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'petition:create-petition
        {petitionNumber : the integer petition number}
        {--dry-run : show data fetched without updating the database}
        {--update : update the petition with current metadata}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update a petition';

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
        $petitionNumber = (int)$this->argument('petitionNumber');
        $dryRun = $this->option('dry-run');
        $update = $this->option('update');

        $petition = Petition::where('petition_number', '=', $petitionNumber)->first();

        if ($petition === null) {
            // Petition does not exist.

            $petition = new Petition([
                'petition_number' => $petitionNumber,
            ]);
        } else {
            // Petition exists.

            if (! $update) {
                $this->error(sprintf(
                    'Petition %d already exists; use the --update flag t0 update it',
                    $petitionNumber
                ));
                return false;
            }
        }

        $petitionData = new PetitionData($petitionNumber);

        // TODO: error if not a valid fetch - do a check.

        $petition->metadata = $petitionData->getMetadata();

        if ($dryRun) {
            $this->info('Dry-run; nothing updated.');
        } else {
            dump($petition->getPetitionData()->getAction());
            $petition->save();
            $this->info(sprintf('Petition %d updated', $petitionNumber));
        }
    }
}
