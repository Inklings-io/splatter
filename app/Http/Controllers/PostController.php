<?php

namespace App\Http\Controllers;

use DB;
use App\Post;
use App\Category;
use Illuminate\Http\Request;
#use Illuminate\Database\Eloquent\SoftDeletingScope;
use IndieAuth;
use Log;
    

class PostController extends Controller
{
    public function shortener($eid){
        $post_id = Post::unshorten($eid);

        $post = Post::withTrashed()
            ->find($post_id);
        if($post == null){
            abort(404);
        }
        return redirect($post->permalink);
    }

    public function view($type, $year, $month, $day, $daycount, $slug = '')
    {
        //remove the soft delete scope to allow to return 410 gone on deleted items
        $post = Post::withTrashed()
            ->with('media')
            ->with('inReplyTo')
            ->with('interactions')
            ->with('contexts')
            ->where(['year' => $year, 'month' => $month, 'day' => $day, 'daycount' => $daycount])
            ->get()->first();
        if($post){

            $owner = trim(config('splatter.owner.url'), '/');

            $context_history = $this->getContexts($post);

            if($post->deleted_at && !IndieAuth::is_user($owner)){
                abort(410);
            }
            if($type != $post->type || $slug != $post->slug){
                $type = $post->type;
                $slug = $post->slug;
                return redirect("/$type/$year/$month/$day/$daycount/$slug");
            }

            $author = config('splatter.owner');
            return view('post', ['post' => $post, 'author' => $author, 'context_history' => $context_history]);
        } else {
            abort(404);
        }
    
    }

    private function getContexts($obj){
        if($obj->contexts){
            $res = array();
            foreach($obj->contexts as $ctx){
                $res = array_merge($this->getContexts($ctx));
                $res[] = $ctx;
            }
            return $res;
        } else {
            return array();
        }

    }

}
 
