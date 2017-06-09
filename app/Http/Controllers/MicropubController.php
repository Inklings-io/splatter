<?php
namespace App\Http\Controllers;

use DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Post;
use Log;

class MicropubController extends Controller
{
    public function get_index()
    {
        $request = request();

        if($request->input('q') == 'config'){
            $json = array();
            $json['media-endpoint'] = config('app.url') . '/api/media';
            $json['syndicate-to'] = array();
            //TODO syndication-to
            return response()->json($json);

        } elseif($request->input('q') == 'syndicate-to'){
            $json = array();
            $json['syndicate-to'] = array();
            //TODO syndication-to
            return response()->json($json);

        } elseif($request->input('q') == 'source'){
            if(empty($request->input('url'))){
                
                $post = $this->getPostFromUrl($request->input('url'));

                if($post === null) {
                    return response()
                        ->view('special_errors.400_micropub')
                        ->setStatusCode(400);
                } elseif(empty($post)){
                    abort(404);
                }
                
                $json = array();
                if(!empty($request->input('properties'))){
                    foreach($request->input('properties') as $requested_property){
                        if($requested_property == 'in-reply-to') {
                            if(!empty($post->inReplyTo->all())){
                                $json['in-reply-to'] = array();
                                foreach($post->inReplyTo as $replyTo){
                                    $json['in-reply-to'][] = $replyTo->url;
                                }
                            }
                        } else {
                            if(!empty($post[$requested_property])){
                                $json[$requested_property] = $post[$requested_property];
                            }
                        }
                    }

                } else {
                    if(!empty($post['content'])){$json['content'] = $post['content'];}
                    if(!empty($post['summary'])){$json['summary'] = $post['summary'];}
                    if(!empty($post['name'])){$json['name'] = $post['name'];}
                    if(!empty($post['like-of'])){$json['like-of'] = $post['like-of'];}
                    if(!empty($post['bookmark-of'])){$json['bookmark-of'] = $post['bookmark-of'];}
                    if(!empty($post['repost-of'])){$json['repost-of'] = $post['repost-of'];}
                    if(!empty($post['artist'])){$json['artist'] = $post['artist'];}
                    if(!empty($post['rsvp'])){$json['rsvp'] = $post['rsvp'];}
                    if(!empty($post['location'])){$json['location'] = $post['location'];}
                    if(!empty($post['weight'])){$json['weight'] = $post['weight'];}
                    if(!empty($post->inReplyTo->all())){
                        $json['in-reply-to'] = array();
                        foreach($post->inReplyTo as $replyTo){
                            $json['in-reply-to'][] = $replyTo->url;
                        }
                    }
                    //TODO event start/end
                }
                return response()->json($json);
            } else {
                return response()
                    ->view('special_errors.400_micropub')
                    ->setStatusCode(400);
            }
        } else {

            return response()
                ->view('special_errors.400_micropub')
                ->setStatusCode(400);
        }
    }
    public function post_index()
    {
        $request = request();

        $scopes = $request->attributes->get('scope');
        //$user = $request->attributes->get('user');
        $request = request();
        if($request->isJson()){
            $input_data = $request->json();
            //TODO
            //
        } else {
            $input_data = $request->input();
            if(isset($input_data['action'])){

                if($input_data['action'] == 'delete') {
                    return deletePost($url);
                } elseif($input_data['action'] == 'undelete') {
                    return undeletePost($url);
                } else {
                    return response()
                        ->view('special_errors.400_micropub')
                        ->setStatusCode(400);
                }

            } elseif(in_array('create', $scopes) && !empty($input_data)){
                // if h=
                $post = new Post;
                $modified = false;
                if(isset($input_data['content'])){
                    $post->content = $input_data['content'];
                    $modified = true;
                }
                if(isset($input_data['summary'])){
                    $post->summary = $input_data['summary'];
                    $modified = true;
                }
                if(isset($input_data['name'])){
                    $post->name = $input_data['name'];
                    $modified = true;
                }
                if(isset($input_data['like-of'])){
                    $post['like-of'] = $input_data['like-of'];
                    $modified = true;
                }
                if(isset($input_data['slug'])){
                    $post->slug = $input_data['slug'];
                    $modified = true;
                } else {
                    $post->slug = $input_data[''];
                }
                //TODO make this a function ?
                //TODO add all the things

                if($modified){

                    $time = Carbon::now();
                    $year = $time->year;
                    $month = $time->month;
                    $day = $time->day;

                    $last_post = Post::where(['year' => $year, 'month' => $month, 'day' => $day])
                        ->orderBy('daycount', 'desc')
                        ->get()
                        ->first();
                    $daycount = 1;
                    if($last_post){
                        $daycount = $last_post->daycount +1;
                    }

                    $post->year = $year;
                    $post->month = $month;
                    $post->day = $day;
                    $post->daycount = $daycount;

                    $post->slug = '';
                    $post->type = 'note';
                    $post->save();

                    //TODO add categories and in-reply-tos after saving

                    return response('Created', 201)
                        ->header('Location', config('app.url') . $post->permalink);
                } else {
                    abort(400);
                }
            }
        }

    }
 

    /* returns a Post object if a post is found at the specified url,
     * if not, if there is something wrong with the url it returns false 
     * if no post was found at the url, it returns []
     *
     * input: url string,  the url to look up
     * input: with_trashed bool, returns deleted posts or not
     */
    private function getPostFromUrl($url, $with_trashed = false){
        try {
            $route = app('router')->getRoutes()->match(app('request')->create($url));
            $route_name = $route->getName();
        } catch(Exception $e) {
            return false;
        }

        if($route_name != 'single_post' && $route_name != 'single_post_no_slug') {
            return false;
        }

        $year = $route->parameters['year'];
        $month = $route->parameters['month'];
        $day = $route->parameters['day'];
        $daycount = $route->parameters['daycount'];

        if($with_trashed){
            $post = Post::withTrashed()
                ->where(['year' => $year, 'month' => $month, 'day' => $day, 'daycount' => $daycount])
                ->get()->first();
        } else {
            $post = Post::where(['year' => $year, 'month' => $month, 'day' => $day, 'daycount' => $daycount])
                ->get()->first();
        }

        return $post;

    }

    private function deleteEntry($url){
        if(!empty($url)){
            return response()
                ->view('special_errors.400_micropub')
                ->setStatusCode(400);
        }

        $post = $this->getPostFromUrl($url);

        if($post === null) {
            return response()
                ->view('special_errors.400_micropub')
                ->setStatusCode(400);
        } elseif(empty($post)){
            abort(404);
        }

        $post->delete();
        return response()->json(array('result' => 'post deleted'));
    }

    private function undeleteEntry($url){
        if(!empty($url)){
            return response()
                ->view('special_errors.400_micropub')
                ->setStatusCode(400);
        }

        $post = $this->getPostFromUrl($url, true);

        if($post === null) {
            return response()
                ->view('special_errors.400_micropub')
                ->setStatusCode(400);
        } elseif(empty($post)){
            abort(404);
        }

        $post->restore();
        return response()->json(array('result' => 'post restored'));
    }




}
