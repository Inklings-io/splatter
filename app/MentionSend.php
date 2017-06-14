<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MentionSend extends Model
{
    protected $table = 'mention_send_queue';
    public $timestamps = false;

    public function post()
    {
        return $this->belongsTo('App\Post');
    }
}
