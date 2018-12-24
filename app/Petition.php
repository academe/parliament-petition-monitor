<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Country;
use App\Constituency;

class Petition extends Model
{
    /**
     * Schedule frequency.
     */
    const SCHEDULE_NONE = 'none';
    const SCHEDULE_DAY = 'day';
    const SCHEDULE_HOUR = 'hour';
    const SCHEDULE_HALF_HOUR = 'half-hour';
    const SCHEDULE_QUARTER_HOUR = 'quarter-hour';
    const SCHEDULE_TEN_MINUTES = 'ten-minutes';

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'json',
    ];

    protected $attributes = [
        'metadata' => '{}',
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
