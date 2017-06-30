<?php

namespace App\Jobs;

require_once base_path('vendor/indieweb/mention-client/src/IndieWeb/MentionClient.php');

use App\MentionSend;
use App\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use IndieWeb\MentionClient;
use Log;


class SendWebmentions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mention_send_entry;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MentionSend $mention_send_entry)
    {
        //
        $post_id = $this->mention_send_entry = $mention_send_entry;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $post_id = $this->mention_send_entry->post_id;
        //
        $post = Post::find($post_id);

        $url_set = array();

        if(isset($post['like-of'])){
            $url_set[] = $post['like-of'];
        }
        if(isset($post['bookmark-of'])){
            $url_set[] = $post['bookmark-of'];
        }
        if(!empty($post->comments->all())){
            foreach($post->comments as $comment){
                $url_set[] = $comment->url;
            }
        }
        if(!empty($post->inReplyTos->all())){
            foreach($post->inReplyTos as $replyTo){
                $url_set[] = $replyTo->url;
            }
        }
        if(!empty($post->categories->all())){
            foreach($post->categories as $category){
                if(!empty($category->url)){
                    $url_set[] = $category->url;
                }
            }
        }
        if(!empty($post->interactions->all())){
            foreach($post->interactions as $interaction){
                if(!empty($interaction->url)){
                    $url_set[] = $interaction->url;
                }
            }
        }

        $client = new MentionClient();
        $urls = $client->findOutgoingLinks($post->content);

        $url_set = array_merge($url_set, $urls);

        $url_set = array_unique($url_set);
        
        $source_url = config('app.url') . $post->permalink;

        foreach($url_set as $url){
            //TODO  IF VOUCH ..
            //$response = $client->sendWebmention($sourceURL, $targetURL, ['vouch'=>$vouch]);
            Log::debug('sending webmention to ' . $url . ' from '. $source_url);
            $response = $client->sendWebmention($source_url, $url);
        }

        $syndication_urls = $client->findOutgoingLinks($post->syndication_extra);
        
        foreach($syndication_urls as $url){
            $response = $client->sendWebmention($source_url, $url);
	    Log::debug(print_r($response, true));
            // TODO add to syndications 
        }

        MentionSend::where(['post_id' => $post_id])->delete();
    }
}
