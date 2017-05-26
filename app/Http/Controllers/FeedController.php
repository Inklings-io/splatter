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

        return view('feed', ['feed_name' => 'Home Page Feed', 'posts' => $posts, 'author' => $author]);
    }

    public function monthFeed($year, $month)
    {
        $posts = $this->basicSearch(['year' => $year, 'month' => $month]);      
        $author = config('splatter.owner');
        return view('feed', ['feed_name' => "Month of $year-$month", 'posts' => $posts, 'author' => $author]);
    }

    public function yearFeed($year)
    {
        $posts = $this->basicSearch(['year' => $year]);      
        $author = config('splatter.owner');
        return view('feed', ['feed_name' => "Year of $year", 'posts' => $posts, 'author' => $author]);
    }


    public function typeFeed($type)
    {
        $posts = $this->basicSearch(['type' => $type]);
        $author = config('splatter.owner');
        return view('feed', ['feed_name' => "$type Feed", 'posts' => $posts, 'author' => $author]);
    }

    public function typeFeedRedir($type_i)
    {
        return redirect('/'.strtolower($type_i));
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
        return view('feed', ['feed_name' => "$name Category", 'posts' => $posts, 'author' => $author]);        
    }

   
    private function basicSearch($where_array = null){

        return Post::with('media')
            ->with('categories')
            ->where($where_array)
            ->orderBy('published', 'desc')
            ->simplePaginate(20);
            
    }

}
