<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

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
