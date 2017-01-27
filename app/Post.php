<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function media()
    {
        return $this->belongsToMany('App\Media');
    }

    public function permalink()
    {
        return '/' . $this->post_type .
                '/'. $this->year .
                '/'. $this->month .
                '/'. $this->day .
                '/'. $this->daycount .
                '/'. $this->slug ;

    }
}
