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
	$p_url = DB::table('person_url')->where(['person_id' => $this->id, 'primary' => true])->get()->first();
	if(!$p_url){
	    $p_url = DB::table('person_url')->where(['person_id' => $this->id])->get()->first();
	}
	if(!$p_url){
	    return '';
	} else {
	    return $p_url->url;
	}
    }

    public function urls()
    {
        return $this->hasMany('App\PersonUrl');
    }
}
