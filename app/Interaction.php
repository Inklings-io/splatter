<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interaction extends Model
{
    use SoftDeletes;

    // potentially a comment could be in-reply-to multiple posts remember
    public function posts()
    {
        return $this->belongsToMany('App\Post');
    }

    public function author()
    {
        return $this->belongsTo('App\Person', 'person_id', 'id');
    }

    public function interactions()
    {
        return $this->belongsToMany('App\Interaction', 'interaction_interaction', 'parent_id',  'child_id' );
    }

    public function getCommentsAttribute()
    {
        return $this->interactions()->where(['type' => 'reply'])->get();
    }

    public function webmention()
    {
        return $this->belongsTo('App\Webmention');
    }

    public function syndications()
    {
        return $this->hasMany('App\InteractionSyndication');
    }
}
