<?php

namespace App\Http\Controllers;

use DB;
use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function home()
    {
        $posts = Post::with('media')
            ->where('deleted', 0)
            ->orderBy('published', 'desc')
            ->limit(20)
            ->get();
        $author = Array(
            'name' => config('splatter.owner.name'),
            'url' => config('splatter.owner.url'),
            'image' => config('splatter.owner.image_url')
            );
        return view('home', ['posts' => $posts, 'author' => $author]);
    }

    public function viewweight()
    {
        return 'ok';
    }
    public function view($posttype, $year, $month, $day, $daycount, $slug = null)
    {
        $post = Post::with('media')
            ->where(['deleted' => 0, 'year' => $year, 'month' => $month, 'day' => $day, 'daycount' => $daycount])
            ->get()->first();
        $author = Array(
            'name' => config('splatter.owner.name'),
            'url' => config('splatter.owner.url'),
            'image' => config('splatter.owner.image_url')
            );
        return $post;
        // return view('posts.mini-default.blade', ['post' => $post, 'author' => $author]);
    
    }

}
