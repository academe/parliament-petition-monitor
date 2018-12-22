<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Petition;

class Country extends Model
{
    //
    protected $guarded = [];

    // ???
    public function petitions()
    {
        return $this->belongsToMany(Petition::class);
    }
}
