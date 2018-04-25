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

    public function contexts()
    {
        return $this->belongsToMany('App\Context', 'context_context', 'child_id',  'parent_id' );
    }

    public function syndications()
    {
        return $this->hasMany('App\ContextSyndication');
    }
}
