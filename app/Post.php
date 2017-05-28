<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;
use DB;
use App\Scopes\AuthorizedScope;

class Post extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new AuthorizedScope);
    }

    public function media()
    {
        return $this->belongsToMany('App\Media');
    }

    public function acls()
    {
        return $this->belongsToMany('App\Person', 'post_access', 'post_id','person_id')->withPivot('displayed');

    }

    public function categories()
    {
        return $this->belongsToMany('App\Category');
    }

    public function interactions()
    {
        return $this->belongsToMany('App\Interaction');
    }

    public function getCommentsAttribute()
    {
        return $this->interactions()->where(['type' => 'reply'])->get();
    }
    public function getRepostsAttribute()
    {
        return $this->interactions()->where(['type' => 'repost'])->get();
    }

    public function getReacjisAttribute()
    {

        $reacjis = $this->interactions()->with('author')->where(['type' => 'reacji'])->get();

        return $reacjis->groupBy('content');
    }

    // public function groupedReacjis()
    // {

    //     $r = DB::table('interactions')
    //         ->join('interaction_post', 'interactions.id', '=', 'interaction_post.interaction_id')
    //         ->select('content', 'post_id', DB::raw('count(interaction_id) as count'))
    //         ->where(['type' => 'reacji', 'post_id' => $this->id])
    //         ->groupBy('content', 'post_id')
    //         ->get(); 

    //     if (!$r->isEmpty()) {
    //         Log::debug(print_r( $r, true));
    //     }

        
    //     return $r;
    // }

    public function contexts()
    {
        return $this->belongsToMany('App\Context');
    }

    public function getLocationAttribute($value)
    {
        return json_decode($value);
    }


    public function getWeightAttribute($value)
    {
        return json_decode($value);
    }

    public function getPermalinkAttribute()
    {
        return  '/'. $this->type .
                '/'. $this->year .
                '/'. $this->month .
                '/'. $this->day .
                '/'. $this->daycount .
                '/'. $this->slug ;

    }
}
