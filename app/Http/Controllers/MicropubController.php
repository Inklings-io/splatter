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
                //$url = preg_replace('/^https?:\/\//', '', $request->input('url'));
                //$app_domain_and_path = preg_replace( '/^https?:\/\//', '', config('app.url'));
                

                //explode('/', config('app.url'))
                try {
                    $route = app('router')->getRoutes()->match(app('request')->create($url));
                    $route_name = $route->getName();
                } catch(Exception $e) {
                    //TODO make a better specific response for this
                    return response()
                        ->view('special_errors.400_micropub')
                        ->setStatusCode(400);
                }

                if($route_name != 'single_post' && $route_name != 'single_post_no_slug') {
                    //TODO make a better specific response for this
                    return response()
                        ->view('special_errors.400_micropub')
                        ->setStatusCode(400);
                }

                $year = $route->parameters['year'];
                $month = $route->parameters['month'];
                $day = $route->parameters['day'];
                $daycount = $route->parameters['daycount'];

                //NOTE: might want to remove the softdeletes scope so micropub clients
                //      can find deleted posts to undelete?
                $post = Post::with('media')
                    ->where(['year' => $year, 'month' => $month, 'day' => $day, 'daycount' => $daycount])
                    ->get()->first();
                
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

        $scopes = $request->attributes->get('scope');
        //$user = $request->attributes->get('user');
        $request = request();
        if($request->isJson()){
            $input_data = $request->json();
        } else {
            $input_data = $requext->input();
            if($input_data['action']){

            } elseif(in_array('create', $scopes) && !empty($input_data)){
                $post = new Token;
                $modified = false;
                foreach($request->input() as $key => $value){
                    switch($key){
                    case 'content':
                        $post->content = $value;
                        $modified = true;
                        break;
                    default:
                        break;
                    }
                }
                if($modified){
                    $post->save();

                    return response('Created', 201)
                        ->header('Location',$post->permalink);
                } else {
                    abort(400);
                }
            }
        }

    }
 




}
