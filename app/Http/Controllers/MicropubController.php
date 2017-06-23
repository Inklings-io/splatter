<?php
namespace App\Http\Controllers;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Post;
use App\Category;
use App\Media;
use App\ReplyTo;
use App\MentionSend;
use App\Jobs\SendWebmentions;
use Log;

class MicropubController extends Controller
{
    protected $media_fields = array('photo', 'video', 'audio');
    protected $basic_fields = array('summary', 'content', 'draft', 'name', 'like-of', 'bookmark-of', 'description', 'height', 'location', 'weight_value', 'weight_unit', 'artist' );
    
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
            if(!empty($request->input('url'))){
                
                $post = $this->getPostFromUrl($request->input('url'));

                if($post === null) {
                    return response()
                        ->view('special_errors.400_micropub')
                        ->setStatusCode(400);
                } elseif(empty($post)){
                    abort(404);
                }
                
		$json = array('properties' => array());
		if(isset($post->unknown)){
		    $json['properties'] = json_decode($post->unknown);
		}
                if(!empty($request->input('properties'))){
                    foreach($request->input('properties') as $requested_property){
                        if($requested_property == 'in-reply-to') {
                            if(!empty($post->inReplyTos->all())){
                                $json['properties']['in-reply-to'] = array();
                                foreach($post->inReplyTos as $replyTo){
                                    $json['properties']['in-reply-to'][] = $replyTo->url;
                                }
                            }
                        } elseif($requested_property == 'category') {
                            if(!empty($post->categories->all())){
                                $json['properties']['category'] = array();
                                foreach($post->categories as $category){
                                    $json['properties']['category'][] = $category->name;
                                }
                            }
                        } else {
                            if(!empty($post[$requested_property])){
                                $json['properties'][$requested_property] = arraY($post[$requested_property]);
                            }
                        }
                    }

                } else {
		    $json['type'] = array('h-entry');
		 
		    foreach($this->basic_fields as $field_name){
		        if(!empty($post[$field_name])){
			    $json['properties'][$field_name] = array($post[$field_name]);
		        }
                    }
                    if(!empty($post->inReplyTos->all())){
                        $json['properties']['in-reply-to'] = array();
                        foreach($post->inReplyTos as $replyTo){
                            $json['properties']['in-reply-to'][] = $replyTo->url;
                        }
                    }
                    if(!empty($post->categories->all())){
                        $json['properties']['category'] = array();
                        foreach($post->categories as $category){
                            $json['properties']['category'][] = $category->name;
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

        //$user = $request->attributes->get('user');
        $request = request();
        if($request->isJson()){
            $data = $request->input();
        } else {
            $input_data = $request->except(['access_token']);
            if(isset($input_data['action'])){
                $data = $input_data;
            } else {
                if(isset($input_data['h'])){
                    $data['type'] = array('h-' . $input_data['h']);
                } else {
                    $data['type'] = array('h-entry');
                }
                $data['properties'] = array();
                foreach ($input_data as $key => $input_entry){
                    if(is_array($input_entry)){
                        $data['properties'][$key] = $input_entry;
                    } else {
                        $data['properties'][$key] = array($input_entry);

                    }

                }
                foreach($this->media_fields as $media_field){
                    if($request->hasFile($media_field)){
			$file_or_files = $request->file($media_field);
			if(is_array($file_or_files)){
			    $data['properties'][$media_field] = array();
			    foreach($file_or_files as $single_file){
			        $data['properties'][$media_field][] = $this->uploadFile($single_file);
			    }
			} else {
			    $data['properties'][$media_field] = array($this->uploadFile($file_or_files));
			}
                    }
                }
            }

        }

        if(isset($data['action'])){

            if($data['action'] == 'delete') {
                return $this->deleteEntry($request->input('url'));

            } elseif($data['action'] == 'undelete') {
                return $this->undeleteEntry($request->input('url'));

            } elseif($data['action'] == 'update') {
                return $this->updateEntry($request->input());

            } else {
                return response()
                    ->view('special_errors.400_micropub')
                    ->setStatusCode(400);
            }
        } elseif(!empty($data)){
            return $this->createPost($data);

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

    private function createPost($data){
        $request = request();
        $scopes = $request->attributes->get('scope');
        if(!in_array('create', $scopes)){
            abort('401');
        } 
        $input_data = $data['properties'];

        // todo if h=entry
        unset($input_data['h']);
        $post = new Post;

        if(isset($input_data['mp-type']) && !empty($input_data['mp-type'])){
            $post->type = $input_data['mp-type'][0];
            unset($input_data['mp-type']);
        } else {
            //Post Type Discovery Algorithm
            if(isset($input_data['rsvp']) && isset($input_data['rsvp'][0]) && in_array($input_data['rsvp'][0], array('yes', 'no', 'maybe','interested'))){
                $post->type = 'rsvp';

            } elseif(isset($input_data['tag-of']) && isset($input_data['tag-of'][0]) && !empty($input_data['tag-of'][0])) {  //should check this is a valid url
                $post->type = 'tag';

            } elseif(isset($input_data['bookmark-of']) && isset($input_data['bookmark-of'][0]) && !empty($input_data['bookmark-of'][0])) {  //should check this is a valid url
                $post->type = 'bookmark';

            } elseif(isset($input_data['in-reply-to']) && isset($input_data['in-reply-to'][0]) && !empty($input_data['in-reply-to'][0])) {  //should check this is a valid url
                $post->type = 'reply';

            } elseif(isset($input_data['repost-of']) && isset($input_data['repost-of'][0]) && !empty($input_data['repost-of'][0])) {  //should check this is a valid url
                $post->type = 'repost';

            } elseif(isset($input_data['like-of']) && isset($input_data['like-of'][0]) && !empty($input_data['like-of'][0])) {  //should check this is a valid url
                $post->type = 'like';

            } elseif(isset($input_data['photo']) && isset($input_data['photo'][0]) && !empty($input_data['photo'][0])) {
                $post->type = 'photo';

            } elseif(isset($input_data['video']) && isset($input_data['video'][0]) && !empty($input_data['video'][0])) {
                $post->type = 'video';

            } elseif(isset($input_data['audio']) && isset($input_data['audio'][0]) && !empty($input_data['audio'][0])) {
                $post->type = 'audio';

            } elseif(isset($input_data['location']) && isset($input_data['location'][0]) && !empty($input_data['location'][0]) && !isset($input_data['content'])) { //TODO will have to review this at some point
                $post->type = 'checkin';

            } else {
                $post->type = 'note';
                //I don't do other types such as article, those will need to be specifically set by mp-type

            }
            
        } // end Post Type Discovery

	

        //TODO tag-of, weight, rsvp
        foreach($this->basic_fields as $field_name){
            // content is a special case for HTML content
            // tod remove from basic fields?
            if($field_name != 'content'){
                if(isset($input_data[$field_name]) && !empty($input_data[$field_name])){
                    $post[$field_name] = $input_data[$field_name][0];
		    unset($input_data[$field_name]);
                }
            }
        }

        if(isset($input_data['content']) && !empty($input_data['content'])){
            if(is_array($input_data['content'][0])){
                $post->content = $input_data['content'][0]['html'];
                $post['content-format'] = 'html';
            } else {
                $post->content = $input_data['content'][0];
            }
	    unset($input_data['content']);
        }

        if(isset($input_data['slug'])){
	    if(!empty($input_data['slug'])){
                $post->slug = $input_data['slug'][0];
                $modified = true;
	    }
            unset($input_data['slug']);
        } else {
            $post->slug = '';
        }

        $time = Carbon::now();
        $year = $time->year;
        $month = $time->month;
        $day = $time->day;

        $last_post = Post::withTrashed()
            ->where(['year' => $year, 'month' => $month, 'day' => $day])
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

        if(isset($input_data['published']) && !empty($input_data['published'])){
            $post->published = $input_data['published'][0];
            unset($input_data['published']);
        } else {
            $post->published = $time;
        }

        $post->created_by = $request->attributes->get('client_id');

        $post->save();

        //Add categories to post
        if(isset($input_data['category']) && !empty($input_data['category'])){
            foreach($input_data['category'] as $category_name){
                $category = Category::firstOrCreate(['name' => $category_name]);
                $post->categories()->save($category);
            }
            unset($input_data['category']);
        } 

        //Add in-reply-to values to post
        if(isset($input_data['in-reply-to']) && !empty($input_data['in-reply-to'])){
            foreach($input_data['in-reply-to'] as $reply_url){

                $reply_to = new ReplyTo;
                $reply_to->url = $reply_url;
                $post->inReplyTos()->save($reply_to);
            }
            unset($input_data['in-reply-to']);
        } 

        //Add media items to post
        foreach($this->media_fields as $field_name){
            if(isset($input_data[$field_name]) && !empty($input_data[$field_name])){
                foreach($input_data[$field_name] as $media_obj_or_str){
                    $media = new Media;
                    $media->type = $field_name;
                    if(is_array($media_obj_or_str)){
                        if(isset($media_obj_or_str['value'])){
                            $media->path = $media_obj_or_str['value'];
                        }
                        if(isset($media_obj_or_str['alt'])){
                            $media->alt = $media_obj_or_str['alt'];
                        }
                    } else {
                        $media->path = $media_obj_or_str;
                    }
                    $media->save();
                    $post->media()->save($media);
                }
            } 
            unset($input_data[$field_name]);
        } 

        if(!empty($input_data)){
            $unknown = json_encode($input_data);
            $post->unknown = $unknown;
            $post->save();
        }
    
        $this->queueWebmentionSending($post);

        return response('Created', 201)
            ->header('Location', config('app.url') . $post->permalink);
    }


    private function deleteEntry($url){
        $request = request();
        $scopes = $request->attributes->get('scope');
        if(!in_array('delete', $scopes)){
            abort(401);
        }
        
        if(empty($url)){
            return response()
                ->view('special_errors.400_micropub')
                ->setStatusCode(400);
        }

        $post = $this->getPostFromUrl($url, true); //don't really care if someone tries to delete an already deleted item

        if($post === null) {
            return response()
                ->view('special_errors.400_micropub')
                ->setStatusCode(400);
        } elseif(empty($post)){
            abort(404);
        }

        if(!$post->trashed()){
            $post->delete();
            $this->queueWebmentionSending($post);
            return response()->json(array('result' => 'post deleted'));
        } else {
            return response()->json(array('result' => 'post was already deleted'));
        }
    }

    private function undeleteEntry($url)
    {

        $request = request();
        $scopes = $request->attributes->get('scope');

        if(!in_array('undelete', $scopes)){
            abort(401);
        }

        if(empty($url)){
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

        if($post->trashed()){
            $post->restore();
            $this->queueWebmentionSending($post);
            return response()->json(array('result' => 'post restored'));
        } else {
            return response()->json(array('result' => 'post was not deleted'));
        }
    }


    private function updateEntry($mp_data)
    {
        $request = request();
        $scopes = $request->attributes->get('scope');

        if(!in_array('update', $scopes)){
            abort(401);
        }

        $post = $this->getPostFromUrl($mp_data['url']);

        if($post === null) {
            return response()
                ->view('special_errors.400_micropub')
                ->setStatusCode(400);
        } elseif(empty($post)){
            abort(404);
        }

        // 'add'
        if(isset($mp_data['add'])){
	    if(!is_array($mp_data['add'])){
	        return response()
		    ->view('special_errors.400_micropub')
		    ->setStatusCode(400);
	    }
            foreach($mp_data['add'] as $key => $attrs){
                if(in_array($key, $this->basic_fields)){
                    if($post[$key] === null && isset($attrs[0])){
                        $post[$key] = $attrs[0];
                    }
                } elseif($key == 'category') {
                    foreach($attrs as $category_name){
                        $category = Category::firstOrCreate(['name' => $category_name]);
                        $post->categories()->save($category);
                    }

                } elseif(in_array($key, $this->media_fields)){
                    foreach($mp_data[$key] as $media_obj_or_str){
                        $media = new Media;
                        $media->type = $key;
                        if(is_array($media_obj_or_str)){
                            if(isset($media_obj_or_str['value'])){
                                $media->path = $media_obj_or_str['value'];
                            }
                            if(isset($media_obj_or_str['alt'])){
                                $media->alt = $media_obj_or_str['alt'];
                            }
                        } else {
                            $media->path = $media_obj_or_str;
                        }
                        $media->save();
                        $post->media()->save($media);
                    }
                } 
            }
        }
        if(isset($mp_data['delete'])){
	    if(!is_array($mp_data['delete'])){
	        return response()
		    ->view('special_errors.400_micropub')
		    ->setStatusCode(400);
	    }
            if($this->isHash($mp_data['delete'])){
                foreach($mp_data['delete'] as $key => $attrs){
                    foreach($attrs as $category_name){
                        $category = Category::where(['name' => $category_name])->get()->first();
                        $post->categories()->detach($category->id);
                    }
                }
            } else {
                foreach($mp_data['delete'] as $key){
                    if(in_array($key, $this->basic_fields)){
                        $post[$key] = null;

                    } elseif($key == 'category'){
                        $post->categories()->detach();

                    } elseif(in_array($key, $this->media_fields)){
                        $post->media()->where(['type' => $key])->detach();
                    }
                }
            }
        }
        //TODO 'replace'
        if(isset($mp_data['replace'])){
	    if(!is_array($mp_data['replace'])){
	        return response()
		    ->view('special_errors.400_micropub')
		    ->setStatusCode(400);
	    }
            foreach($mp_data['replace'] as $key => $attrs){

                if(in_array($key, $this->basic_fields)){
                    if(isset($attrs[0])){
                        $post[$key] = $attrs[0];
                    }
                } elseif($key == 'category') {
                    foreach($attrs as $category_name){
                        $post->categories()->detach();
                        $category = Category::firstOrCreate(['name' => $category_name]);
                        $post->categories()->save($category);
                    }

                } elseif(in_array($key, $this->media_fields)){
                    $post->media()->where(['type' => $key])->detach();

                    foreach($mp_data[$key] as $media_obj_or_str){
                        $media = new Media;
                        $media->type = $key;
                        if(is_array($media_obj_or_str)){
                            if(isset($media_obj_or_str['value'])){
                                $media->path = $media_obj_or_str['value'];
                            }
                            if(isset($media_obj_or_str['alt'])){
                                $media->alt = $media_obj_or_str['alt'];
                            }
                        } else {
                            $media->path = $media_obj_or_str;
                        }
                        $media->save();
                        $post->media()->save($media);
                    }
                } 


            }
        }

        $post->save();
        $this->queueWebmentionSending($post);

    }

    private function isHash(array $in)
    {
        return is_array($in) && count(array_filter(array_keys($in), 'is_string')) > 0;
    }

    private function uploadFile($file){
        $request = request();
        $scopes = $request->attributes->get('scope');

        if(!in_array('create', $scopes)){
            abort(401);
        }

        if(!$file->isValid()){
            abort(400);
        }

        $path = Storage::putFile('public', $file, 'public');
        
        $path = Storage::url($path);

        return $path;

        
    }

    private function queueWebmentionSending($post){
        $ms = new MentionSend;
        $ms->post_id = $post->id;
        $ms->save();
        $job = new SendWebmentions($ms);
        dispatch($job);

    }

}
