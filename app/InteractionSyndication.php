<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InteractionSyndication extends Model
{
    protected $table = 'interaction_syndication';
    public $timestamps = false;

    public function interaction()
    {
        return $this->belongsTo('App\Interaction');
    }
    public function site()
    {
        return $this->belongsTo('App\SyndicationSite', 'syndication_site_id');
    }
}
