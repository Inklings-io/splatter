<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class PersonUrl extends Model
{
    protected $table = 'person_url'; // the expected name is person_urls, may change this in the future
    //TODO: standardize urls going in to this table
    public function person()
    {
        return $this->belongsTo('App\Person');
    }

}
