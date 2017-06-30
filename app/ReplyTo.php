<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class ReplyTo extends Model
{
    protected $table = 'post_reply_to'; // the expected name is person_urls, may change this in the future
    public $timestamps = false;
    //TODO: standardize urls going in to this table
}
