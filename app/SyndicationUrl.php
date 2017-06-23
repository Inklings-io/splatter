<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SyndicationUrl extends Model
{
    protected $table = 'post_syndication';
    public $timestamps = false;

    public function post()
    {
        return $this->belongsTo('App\Post');
    }
    public function site()
    {
        return $this->belongsTo('App\SyndicationSite', 'syndication_site_id');
    }
}
