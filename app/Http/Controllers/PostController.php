<?php

namespace App\Http\Controllers;

use DB;
use App\Post;
use App\Category;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function view($type, $year, $month, $day, $daycount, $slug = null)
    {
        $post = Post::with('media')
            ->where(['year' => $year, 'month' => $month, 'day' => $day, 'daycount' => $daycount])
            ->get()->first();
        $author = config('splatter.owner');

        // return $post;
        return view('post', ['post' => $post, 'author' => $author]);
    
    }

}
