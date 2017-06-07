<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Context extends Model
{
    //
    public function author()
    {
        return $this->belongsTo('App\Person', 'person_id', 'id');
    }
}
