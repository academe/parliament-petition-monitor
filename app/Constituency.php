<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Signature;
use App\Petition;

class Constituency extends Model
{
    //
    protected $guarded = [];

    // ???
    public function petitions()
    {
        return $this->belongsToMany(Petition::class);
    }

    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }
}
