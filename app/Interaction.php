<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    // potentially a comment could be in-reply-to multiple posts remember
    public function posts()
    {
        return $this->belongsToMany('App\Post');
    }

    public function author()
    {
        return $this->belongsTo('App\Person', 'person_id', 'id');
    }
}
