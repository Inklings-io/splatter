<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function posts()
    {
        return $this->hasMany('App\Post');
    }

    public function getPermalinkAttribute()
    {
        return '/category/' . $this->name;
    }

    // accessor for easier fetching the count
    public function getPostsCountAttribute()
    {
        $q = DB::table('category_post')
            ->select('category_id', DB::raw('count(post_id) as ct'))
            ->where('category_id', $this->id)
            ->groupBy('category_id')
            ->get();
        return $q->first()->ct;
    }


}
