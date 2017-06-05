<?php

namespace App\Http\Controllers;

use DB;
use App\Post;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use IndieAuth;

class FeedController extends Controller
{
   
    public function home()
    {
        $owner = trim(config('splatter.owner.url'), '/');
        if(IndieAuth::is_user($owner)){
            $posts = Post::withoutGlobalScope(SoftDeletingScope::class)
                ->where('draft', 0)->with('media')
                ->with('categories')
                ->orderBy('published', 'desc')
                ->simplePaginate(20);
        } else {
            $posts = Post::where('draft', 0)->with('media')
                ->with('categories')
                ->orderBy('published', 'desc')
                ->simplePaginate(20);
        }
            
        $author = config('splatter.owner');

        return view('feeds.home', ['feed_name' => 'Home Page Feed', 'posts' => $posts, 'author' => $author]);
    }

    public function home_jf2()
    {
        $posts = Post::where('draft', 0)->with('media')
            ->with('categories')
            ->orderBy('published', 'desc')
            ->simplePaginate(20);
        $author = config('splatter.owner');

        $json = array();
        $json['type'] = 'feed';
        $json['url'] = config('app.url');
        $json['name'] = config('app.url') . ' Main Feed';
        
        $json['author'] = array();
        $json['author']['url'] = $author['url'];
        $json['author']['photo'] = $author['image'];
        $json['author']['name'] = $author['name'];

        $json['children'] = array();
        foreach($posts as $post){
            $entry = array(
                'content' => $post->content,
                'url' => config('app.url') . $post->permalink,
                'uid' => config('app.url') . $post->permalink
            );

            if(!empty($post->name)){ $entry['name'] = $post->name; }
            if(!empty($post->summary)){ $entry['summary'] = $post->summary; }
            if(!empty($post['in-reply-to'])){ $entry['in-reply-to'] = $post['in-reply-to']; }
            if(!empty($post->published)){ $entry['published'] = $post->published; }

            $categories = array();
            foreach($post->categories as $category){
                if(isset($category['person_name'])){
                    $categories[] = $category['person_name'];
                } else {
                    $categories[] = $category['person_name'];
                }
            }
            if(!empty($categories)){
                $entry['category'] = $categories;
            }
            
            $json['children'][] = $entry;

        }
        return response()->json($json);
    }

    public function home_yaml()
    {
        $posts = Post::where('draft', 0)->with('media')
            ->with('categories')
            ->orderBy('published', 'desc')
            ->simplePaginate(20);
        $author = config('splatter.owner');

        $json = array();
        $json['type'] = 'feed';
        $json['url'] = config('app.url');
        $json['name'] = config('app.url') . ' Main Feed';
        
        $json['author'] = array();
        $json['author']['url'] = $author['url'];
        $json['author']['photo'] = $author['image'];
        $json['author']['name'] = $author['name'];

        $json['children'] = array();
        foreach($posts as $post){
            $entry = array(
                'content' => $post->content,
                'url' => config('app.url') . $post->permalink,
                'uid' => config('app.url') . $post->permalink
            );

            if(!empty($post->name)){ $entry['name'] = $post->name; }
            if(!empty($post->summary)){ $entry['summary'] = $post->summary; }
            if(!empty($post->published)){ $entry['published'] = $post->published; }

            $categories = array();
            foreach($post->categories as $category){
                if(isset($category['person_name'])){
                    $categories[] = $category['person_name'];
                } else {
                    $categories[] = $category['person_name'];
                }
            }
            if(!empty($categories)){
                $entry['category'] = $categories;
            }
            
            $json['children'][] = $entry;

        }
        //TODO
        return response()->json($json)
            ->header('Content-Type', 'plain/yaml');
    }

    public function monthFeed($year, $month)
    {
        $posts = $this->basicSearch(['year' => $year, 'month' => $month]);      
        $author = config('splatter.owner');
        return view('feeds.default', ['feed_name' => "Month of $year-$month", 'posts' => $posts, 'author' => $author]);
    }

    public function yearFeed($year)
    {
        $posts = $this->basicSearch(['year' => $year]);      
        $author = config('splatter.owner');
        return view('feeds.default', ['feed_name' => "Year of $year", 'posts' => $posts, 'author' => $author]);
    }


    public function typeFeed($type)
    {
        $posts = $this->basicSearch(['type' => $type]);
        $author = config('splatter.owner');
        return view('feeds.default', ['feed_name' => "$type Feed", 'posts' => $posts, 'author' => $author]);
    }

    public function typeFeedRedir($type_i)
    {
        return redirect('/'.strtolower($type_i));
    }

    public function category($name)
    {
        $owner = trim(config('splatter.owner.url'), '/');
        if(IndieAuth::is_user($owner)){
            $posts = Post::withoutGlobalScope(SoftDeletingScope::class)
                ->where('draft', 0)->with('media')
                ->with('categories')
                ->whereHas('categories', function($query) use ($name) {
                    $query->where('name',$name);
                })
                ->orderBy('published', 'desc')
                ->simplePaginate(20);
        } else {
            $posts = Post::where('draft', 0)->with('media')
                ->with('categories')
                ->whereHas('categories', function($query) use ($name) {
                    $query->where('name',$name);
                })
                ->orderBy('published', 'desc')
                ->simplePaginate(20);
        }

            
        $author = config('splatter.owner');
        return view('feeds.default', ['feed_name' => "$name Category", 'posts' => $posts, 'author' => $author]);        
    }

   
    private function basicSearch($where_array = null){

        $owner = trim(config('splatter.owner.url'), '/');
        if(IndieAuth::is_user($owner)){
            return Post::withoutGlobalScope(SoftDeletingScope::class)
                ->where('draft', 0)-> with('media')
                ->with('categories')
                ->where($where_array)
                ->orderBy('published', 'desc')
                ->simplePaginate(20);
        } else {
            return Post::where('draft', 0)-> with('media')
                ->with('categories')
                ->where($where_array)
                ->orderBy('published', 'desc')
                ->simplePaginate(20);
        }
            
    }

}
