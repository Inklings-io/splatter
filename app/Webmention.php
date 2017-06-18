<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;
use DB;

class Webmention extends Model
{
    public function interactions()
    {
        return $this->hasMany('App\Interaction');
    }

}
