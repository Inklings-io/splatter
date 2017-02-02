<?php

namespace App\Http\Controllers;

use DB;
use App\Post;
use App\Category;
use Illuminate\Http\Request;

class FeedController extends Controller
{
   
    public function home()
    {
        $posts = Post::with('media')
            ->with('categories')
            ->orderBy('published', 'desc')
            ->simplePaginate(20);
            
        $author = config('splatter.owner');

        return view('home', ['posts' => $posts, 'author' => $author]);
    }

    public function monthFeed($year, $month)
    {
        $posts = $this->basicSearch(['year' => $year, 'month' => $month]);      
        $author = config('splatter.owner');
        return view('home', ['posts' => $posts, 'author' => $author]);
    }

    public function yearFeed($year)
    {
        $posts = $this->basicSearch(['year' => $year]);      
        $author = config('splatter.owner');
        return view('home', ['posts' => $posts, 'author' => $author]);
    }


    public function typeFeed($type)
    {
        $posts = $this->basicSearch(['type' => $type]);      
        $author = config('splatter.owner');
        return view('home', ['posts' => $posts, 'author' => $author]);
    }

    public function category($name)
    {
        $posts = Post::with('media')
            ->with('categories')
            ->whereHas('categories', function($query) use ($name) {
                $query->where('name',$name);
            })
            ->orderBy('published', 'desc')
            ->simplePaginate(20);
            
            
        $author = config('splatter.owner');
        return view('home', ['posts' => $posts, 'author' => $author]);        
    }

   
    private function basicRenderedSearch($where_array = null){

        return Post::with('media')
            ->with('categories')
            ->where($where_array)
            ->orderBy('published', 'desc')
            ->simplePaginate(20);
            
    }

}
