<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Petition;
use Carbon\Carbon;

class FetchJob extends Model
{
    protected $guarded = [];

    public function petition()
    {
        return $this->belongsTo(Petition::class);
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
