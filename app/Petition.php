<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Country;
use App\Constituency;

class Petition extends Model
{
    //
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'json',
    ];

    protected $attributes = [
        'metadata' => [],
    ];

    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }

    public function constituencies()
    {
        return $this->belongsToMany(Constituency::class);
    }

    /**
     * @returns PetitionData object from the stored metadata
     */
    public function getPetitionData()
    {
        return new PetitionData(
            $this->petition_number,
            $this->metadata
        );
    }
}
