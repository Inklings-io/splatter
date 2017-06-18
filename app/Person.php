<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class Person extends Model
{
    public function interactions()
    {
        return $this->hasMany('App\Interaction');
    }

    public function getUrlAttribute()
    {
        return DB::table('person_url')->where(['person_id' => $this->id, 'primary' => true])->get()->first()->url;
    }

    public function urls()
    {
        return $this->hasMany('App\PersonUrl');
    }
}
