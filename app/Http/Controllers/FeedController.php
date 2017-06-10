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
   
    public function home($format = null)
    {
        $owner = trim(config('splatter.owner.url'), '/');
        $posts = $this->postFetch()
            ->orderBy('published', 'desc')
            ->simplePaginate(20);
        $author = config('splatter.owner');

        if($format=='jf2'){
            return $this->sendJF2($posts, $author);
        } elseif($format=='yaml'){
            return $this->sendYAML($posts, $author);
        } else {
            return view('feeds.home', ['feed_name' => 'Home Page Feed', 'posts' => $posts, 'author' => $author]);
        }

    }

    public function home_jf2()
    {
        return $this->home('jf2');
    }
    public function home_yaml()
    {
        return $this->home('yaml');
    }



    public function monthFeed($year, $month)
    {
        $posts = $this->postFetch()
            ->where(['year' => $year, 'month' => $month])
            ->orderBy('published', 'desc')
            ->simplePaginate(20);
        $author = config('splatter.owner');
        return view('feeds.default', ['feed_name' => "Month of $year-$month", 'posts' => $posts, 'author' => $author]);
    }

    public function yearFeed($year)
    {
        $posts = $this->postFetch()
            ->where(['year' => $year])
            ->orderBy('published', 'desc')
            ->simplePaginate(20);
        $author = config('splatter.owner');
        return view('feeds.default', ['feed_name' => "Year of $year", 'posts' => $posts, 'author' => $author]);
    }


    public function typeFeed($type)
    {
        $posts = $this->postFetch()
            ->where(['type' => $type])
            ->orderBy('published', 'desc')
            ->simplePaginate(20);
        $author = config('splatter.owner');
        return view('feeds.default', ['feed_name' => "$type Feed", 'posts' => $posts, 'author' => $author]);
    }

    public function typeFeedRedir($type_i)
    {
        return redirect('/'.strtolower($type_i));
    }

    public function category($name)
    {
        $posts = $this->postFetch()
            ->whereHas('categories', function($query) use ($name) {
                $query->where('name',$name);
            })
            ->orderBy('published', 'desc')
            ->simplePaginate(20);

            
        $author = config('splatter.owner');
        return view('feeds.default', ['feed_name' => "$name", 'posts' => $posts, 'author' => $author]);        
    }

   
    private function postFetch($include_deletes = true){

        $owner = trim(config('splatter.owner.url'), '/');
        if($include_deletes && IndieAuth::is_user($owner)){
            $resut = Post::withoutGlobalScope(SoftDeletingScope::class)
                ->where('draft', 0);
        } else {
           $result = Post::where('draft', 0);
        }

        $result = $result
            ->with('media')
            ->with('categories')
            ->with('inReplyTos')
            ->with('interactions');

        return $result;
            
    }

    private function sendJF2($posts, $author)
    {
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

    private function sendYAML($posts, $author)
    {
        $posts = $this->postFetch(false)
            ->orderBy('published', 'desc')
            ->simplePaginate(10);
        $author = config('splatter.owner');

        $json = array();
        $json['type'] = 'feed';
        $json['url'] = config('app.url');
        $json['name'] = config('app.url') . ' Main Feed';
        
        $json['author'] = array();
        $json['author']['url'] = $author['url'];
        $json['author']['photo'] = $author['image'];
        $json['author']['name'] = $author['name'];

        $json['items'] = array();
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
            
            $json['items'][] = $entry;

        }
        //TODO
        return response(yaml_emit($json))
            ->header('Content-Type', 'text/x-yaml');
    }

}
