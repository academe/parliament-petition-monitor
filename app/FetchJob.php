<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Petition;
use App\ConstituencySignature;
use Carbon\Carbon;

class FetchJob extends Model
{
    protected $guarded = [];

    public function petition()
    {
        return $this->belongsTo(Petition::class);
    }

    public function constituencySignatures()
    {
        return $this->hasMany(ConstituencySignature::class, 'fetch_jobs_id');
    }

    public function countrySignatures()
    {
        return $this->hasMany(CountrySignature::class, 'fetch_jobs_id');
    }

    /**
     * The count time formatted to the nearest minute.
     */
    public function getCountTimeMinuteAttribute()
    {
        return Carbon::parse($this->count_time)->format('Y-m-d H:i');
    }

    /**
     * The count time formatted to the nearest five minute.
     * Requires Carbon v2 to do this.
     */
    public function getCountTimeFiveMinuteAttribute()
    {
        return Carbon::parse($this->count_time)->roundMinute(5)->format('Y-m-d H:i');
    }
}
