<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContextSyndication extends Model
{
    protected $table = 'context_syndication';
    public $timestamps = false;

    public function context()
    {
        return $this->belongsTo('App\Context');
    }
    public function site()
    {
        return $this->belongsTo('App\SyndicationSite', 'syndication_site_id');
    }
}
