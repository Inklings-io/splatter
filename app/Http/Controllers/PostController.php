<?php

namespace App\Http\Controllers;

use DB;
use App\Post;
use App\Category;
use Illuminate\Http\Request;
use Log;
    

class PostController extends Controller
{

    public function view($type, $year, $month, $day, $daycount, $slug = '')
    {
        $post = Post::with('media')
            ->with('interactions')
            ->where(['year' => $year, 'month' => $month, 'day' => $day, 'daycount' => $daycount])
            ->get()->first();
        if($type != $post->type || $slug != $post->slug){
            $type = $post->type;
            $slug = $post->slug;
            return redirect("/$type/$year/$month/$day/$daycount/$slug");
        }
        $author = config('splatter.owner');

        //Log::debug('asdf: ' . $year);

        // return $post;
        return view('post', ['post' => $post, 'author' => $author]);
    
    }

}
 