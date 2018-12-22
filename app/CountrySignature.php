<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Petition;
use App\FetchJob;
use App\Country;

class CountrySignature extends Model
{
    //
    protected $guarded = [];

    protected $table = 'country_signature';

    public function fetchJob()
    {
        return $this->belongsTo(FetchJob::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
