<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Petition;

class FetchJob extends Model
{
    //
    protected $guarded = [];

    public function petition()
    {
        return $this->belongsTo(Petition::class);
    }
}
