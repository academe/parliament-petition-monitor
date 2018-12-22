<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Constituency;
use App\FetchJob;
use App\Petition;

class ConstituencySignature extends Model
{
    //
    protected $guarded = [];

    protected $table = 'constituency_signature';

    public function fetchJob()
    {
        return $this->belongsTo(FetchJob::class);
    }

    public function constituency()
    {
        return $this->belongsTo(Constituency::class);
    }
}
