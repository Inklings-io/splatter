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

    public function inReplyTos()
    {
        return $this->hasMany('App\ReplyTo');
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


    //TODO: in-reply-tos

    public function contexts()
    {
        return $this->belongsToMany('App\Context');
    }

    public function getLocationAttribute($value)
    {
        return json_decode($value);
    }

    public function getShortlinkAttribute(){
        $s = "";
        $m = "0123456789ABCDEFGHJKLMNPQRSTUVWXYZ_abcdefghijkmnopqrstuvwxyz";
        if ($this->id === null || $this->id === 0) {
            return 0;
        }
        $id = $this->id;
        while ($id > 0) {
               $d = $id % 60;
                  $s = $m[$d] . $s;
                  $id = ($id - $d) / 60;
        }
        return trim(config('splatter.site.short_url'), '/') . '/s/' .$s;
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

    public static function unshorten($s)
    {
         $n = 0;
          $j = strlen($s);
        for ($i = 0; $i < $j; $i++) { // iterate from first to last char of $s
               $c = ord($s[$i]); //  put current ASCII of char into $c
            if ($c >= 48 && $c <= 57) {
                $c = $c - 48;
            } else if ($c >= 65 && $c <= 72) {
                $c -= 55;
            } else if ($c == 73 || $c == 108) {
                  $c = 1;
            } // typo capital I, lowercase l to 1
                    else if ($c >= 74 && $c <= 78) {
                        $c -= 56;
                    } else if ($c == 79) {
                        $c = 0;
                    } // error correct typo capital O to 0
                    else if ($c >= 80 && $c <= 90) {
                        $c -= 57;
                    } else if ($c == 95) {
                        $c = 34;
                    } // underscore
                    else if ($c >= 97 && $c <= 107) {
                        $c -= 62;
                    } else if ($c >= 109 && $c <= 122) {
                        $c -= 63;
                    } else {
                        $c = 0;
                    } // treat all other noise as 0
                    $n = 60 * $n + $c;
        }
       return $n;
    }
}
