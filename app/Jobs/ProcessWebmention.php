<?php

namespace App\Jobs;

require_once base_path('vendor/mf2/mf2/Mf2/Parser.php');


use App\Webmention;
use App\Interaction;
use App\Person;
use App\PersonUrl;
use App\Post;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Mf2;

class ProcessWebmention implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $webmention;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Webmention $webmention)
    {
	$this->webmention = $webmention;
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $webmention = $this->webmention;
        if ($webmention->status_code != 202){
            return true;
        } 

        $source_url = trim($webmention['source_url']);
        $target_url = trim($webmention['target_url']);
        $vouch_url = null;
        if ($webmention['vouch_url']) {
            $vouch_url = trim($webmention['vouch_url']);
        }

        $editing = false;

        if(!empty($webmention->interactions->all())){
            $editing = true;
        }

        try {
            $route = app('router')->getRoutes()->match(app('request')->create($target_url));
            $route_name = $route->getName();

            $year = $route->parameters['year'];
            $month = $route->parameters['month'];
            $day = $route->parameters['day'];
            $daycount = $route->parameters['daycount'];

            $post = Post::where(['year' => $year, 'month' => $month, 'day' => $day, 'daycount' => $daycount])
                ->get()->first();

        } catch(Exception $e) {

            $webmention->status_code = '400';
            $webmention->status = 'Does not refer to existing page';
            $webmention->save();
            return true;
        }
            
        //when editing i don't care about the vouch, i've already processed and approved it
        if(!$editing && $vouch_url){
            $valid_link_found = false;
            $c = curl_init();
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_URL, $vouch_url);
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($c, CURLOPT_MAXREDIRS, 20);
            curl_setopt($c, CURLOPT_TIMEOUT, 600);
            $vouch_content = curl_exec($c);
            curl_close($c);
            unset($c);

            $short_vouch  = trim(str_replace(array('http://', 'https://'), array('',''), $vouch_url), '/');

            $reg_ex_match = '/(href=[\'"](?<href>[^\'"]+)[\'"][^>]*(rel=[\'"](?<rel>[^\'"]+)[\'"])?)/';
            $matches = array();
            preg_match_all($reg_ex_match, $vouch_content, $matches);
            for ($i = 0; $i < count($matches['href']); $i++) {
                //$this->log->write('checking '.$href . '   rel '.$rel);
                $href = strtolower($matches['href'][$i]);
                $rel = strtolower($matches['rel'][$i]);

                if (strpos($rel, "nofollow") === false) {
                    if (strpos($href, $short_vouch) !== false) {
                        $valid_link_found = true;
                    }
                }
            }
            if (!$valid_link_found) {
                //repeat all that for rel before href (because preg_match_all doesn't like reused names)
                $reg_ex_match = '/(rel=[\'"](?<rel>[^\'"]+)[\'"][^>]*href=[\'"](?<href>[^\'"]+)[\'"])/';
                $matches = array();
                preg_match_all($reg_ex_match, $vouch_content, $matches);

                for ($i = 0; $i < count($matches['href']); $i++) {
                    //$this->log->write('checking '.$href . '   rel '.$rel);
                    $href = strtolower($matches['href'][$i]);
                    $rel = strtolower($matches['rel'][$i]);

                    if (strpos($rel, "nofollow") === false) {
                        if (strpos($href, $short_vouch) !== false) {
                            $valid_link_found = true;
                        }
                    }
                }
            }
            if (!$valid_link_found) {
                $webmention->status_code = '400';
                $webmention->status = 'Vouch Invalid';
                $webmention->save();
                return true;
            }

        }

        //TODO shortcut this if it matches our HTTP_SERVER OR HTTPS_SERVER


        //to verify that target is on my site
        $c = curl_init();
        curl_setopt($c, CURLOPT_NOBODY, 1);
        curl_setopt($c, CURLOPT_URL, $target_url);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_MAXREDIRS, 20);
        curl_setopt($c, CURLOPT_TIMEOUT, 600);
        $real_url = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
        curl_close($c);
        unset($c);


        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $source_url);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_MAXREDIRS, 20);
        curl_setopt($c, CURLOPT_TIMEOUT, 600);
        //curl_setopt($c, CURLOPT_HEADER, true); //including header causes php-mf2 parsing to fail
        $real_source_url = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
        $page_content = curl_exec($c);

        //$this->log->write(print_r($real_source_url, true));
        $return_code = curl_getinfo($c, CURLINFO_HTTP_CODE);


        //TODO test if vouch points to source_url

        curl_close($c);
        unset($c);

        if ($editing && $return_code == 410) {
            $webmention->interactions()->delete();
            $webmention->status_code = '410';
            $webmention->status = 'Deleted';
            $webmention->save();
            return true;

        } elseif ($page_content === false) {
            $webmention->status_code = '400';
            $webmention->status = 'Failed To Fetch Source';
            $webmention->save();
            return true;

        } elseif (stristr($page_content, $target_url) === false) {
            $webmention->status_code = '400';
            $webmention->status = 'Target Link Not Found At Source';
            $webmention->save();
            if($editing){
                $webmention->interactions()->delete();
            }
            return true;
        } else {

            if($route_name != 'single_post' && $route_name != 'single_post_no_slug') {
                //TODO create generic mention
                return false;
            }

            $mf2_parsed = Mf2\parse($page_content, $real_source_url);
            foreach ($mf2_parsed['items'] as $item) {
                $comment_data = $this->parse($item, $target_url, 300);
                if (!empty($comment_data['url'])) { //break out of loop if we found one
                    break;
                }
            }

	   
            switch ($comment_data['type']) {
                case 'reply':
                    $content_text = trim($comment_data['text']);

                    if(defined('TWITTER_HANDLE')){
                        //backfed twitter reacji include the twitter handle, so lets try stripping that out
                        $content_text = str_replace(TWITTER_HANDLE, '', $content_text);
                        $content_text = trim($content_text);
                    }

                    $interaction_type = 'reply';
                    if(EmojiRecognizer::isSingleEmoji($content_text)) {
                        $interaction_type = 'reacji';
                        $comment_data['text'] = $content_text;
                    }
                break;
                case 'like':
                    $interaction_type = 'reacji';
                    $comment_data['text'] = html_entity_decode('&#10084;'); //a heart emoji
                break;
                    default:
                    $interaction_type = $comment_data['type'];
            }
	    

            //TODO ideally it would update and only delete if needed.
            // soft delete old interactions before saving new one
            $webmention->interactions()->delete();

            //TODO person_mention type
            $interaction = new Interaction;
            $interaction->url = $comment_data['url'];
            if ( isset($comment_data['published']) && !empty($comment_data['published']) ) {
                $interaction->published = new Carbon($comment_data['published']);
            }
            if ( isset($comment_data['name']) && !empty($comment_data['name']) ) {
                $interaction->name = $comment_data['name'];
            }
            if ( isset($comment_data['content']) && !empty($comment_data['content']) ) {
                $interaction->content = $comment_data['content'];
            }
            if ( isset($comment_data['rsvp']) && !empty($comment_data['rsvp']) ) {
                $interaction->rsvp = $comment_data['rsvp'];
            }
            $interaction->type = $interaction_type;

           // TODO move this to a config
            $interaction->approved = 1;

            $interaction->webmention()->associate( $webmention);

            $author = $this->findOrCreatePerson($comment_data['author']);

            $interaction->author()->associate( $author);

            $interaction->save();

            $post->interactions()->attach($interaction);

            //TODO: salmentions

        }


    }

    private function findOrCreatePerson($person_data){
        $person_url = PersonUrl::where(['url' => $person_data['url']])->get()->first();
        if($person_url){
            return $person_url->person()->get()->first();
        } else { // person_url not found inthe DB, createnew person
            $person = new Person;
            if(isset($person_data['name'])){
                $person->name = $person_data['name'];
            }
            if(isset($person_data['photo'])){
                $person->image = $person_data['photo'];
            }
            $person->save();
            $person_url = new PersonUrl;
            $person_url->url = $person_data['url'];
            $person_url->primary = 1;
            $person_url->person()->associate($person);
            $person_url->save();
            return $person;
        }
    }


private function truncate($text, $maxTextLength, $maxLines) {
  $lines = explode("\n", $text);
  $visibleLines = array_filter($lines);
  if(count($visibleLines) > $maxLines) {
    $newContent = array();
    $visibleLinesAdded = 0;
    $i = 0;
    while($visibleLinesAdded < $maxLines && $i < count($lines)) {
      $line = $lines[$i];
      $newContent[] = $line;
      if(trim($line) != '')
        $visibleLinesAdded++;
      $i++;
    }
    $text = implode("\n", $newContent);
    // Tack on extra chars and then tell cassis to ellipsize it shorter to take advantage of proper ellipsizing logic.
    // This is for when the full text is shorter than $maxTextLength but has more lines than $maxLines
    $text .= ' ....';
  }
  return $text;
}

// Collects all URLs found in the input array, and remove the scheme.
// An input object may be a string URL or also an mf2 object with properties.url
private function collectURLs(&$urls) {
  if(is_array($urls) && array_key_exists(0, $urls)) {
    foreach($urls as $i=>$u) {
      $this->collectURLs($urls[$i]);
    }
  } elseif(is_array($urls)
    && array_key_exists('type', $urls)
    && array_key_exists('properties', $urls)
    && array_key_exists('url', $urls['properties'])
    ) {
    // Flatten the object and turn it just into the URL
    $urls = preg_replace('/^https?/', '', $urls['properties']['url'][0]);
  } elseif(is_string($urls)) {
    $urls = preg_replace('/^https?/', '', $urls);
  }
}

private function parse($mf, $refURL=false, $maxTextLength=150, $maxLines=2) {
  // When parsing a comment, the $refURL is the URL being commented on.
  // This is used to check for an explicit in-reply-to property set to this URL.

  // Remove the scheme from the refURL and treat http and https links as the same
  $this->collectURLs($refURL);

  $type = 'mention';
  $published = false;
  $name = false;
  $text = false;
  $url = false;
  $author = array(
    'name' => false,
    'photo' => false,
    'url' => false
  );
  $comments = array();
  $rsvp = null;
  $tags = null;
  $syndications = null;

  if(array_key_exists('type', $mf) && (in_array('h-entry', $mf['type']) || in_array('h-cite', $mf['type'])) && array_key_exists('properties', $mf)) {
    $properties = $mf['properties'];

    if(array_key_exists('author', $properties)) {
      $authorProperty = $properties['author'][0];
      if(is_array($authorProperty)) {

        if(array_key_exists('name', $authorProperty['properties'])) {
          $author['name'] = $authorProperty['properties']['name'][0];
        }

        if(array_key_exists('url', $authorProperty['properties'])) {
          $author['url'] = $authorProperty['properties']['url'][0];
        }

        if(array_key_exists('photo', $authorProperty['properties'])) {
          $author['photo'] = $authorProperty['properties']['photo'][0];
        }

      } elseif(is_string($authorProperty)) {
        $author['url'] = $authorProperty;
      }
    }

    if(array_key_exists('published', $properties)) {
      $published = $properties['published'][0];
    }

    if(array_key_exists('url', $properties)) {
      $url = $properties['url'][0];
    }

    if(array_key_exists('comment', $properties)) {
      foreach($properties['comment'] as $comment) {
        $comments[] = parse($comment, $url, $maxTextLength, $maxLines); // recurse for all comments
      }
    }

    if(array_key_exists('syndication', $properties)) {
      $syndications = array();
      foreach($properties['syndication'] as $syndication_link){
        $syndications[] = $syndication_link;
      }
    }

    // Check if this post is a "like-of"
    if($refURL && array_key_exists('like-of', $properties)) {
      collectURLs($properties['like-of']);
      if(in_array($refURL, $properties['like-of']))
        $type = 'like';
    }

    // Check if this post is a "like" (Should be deprecated in the future)
    if($refURL && array_key_exists('like', $properties)) {
      collectURLs($properties['like']);
      if(in_array($refURL, $properties['like']))
        $type = 'like';
    }

    // If the post has an explicit in-reply-to property, verify it matches $refURL and set the type to "reply"
    if($refURL && array_key_exists('in-reply-to', $properties)) {
      // in-reply-to may be a string or an h-cite
      foreach($properties['in-reply-to'] as $check) {
        $this->collectURLs($check);
        if(is_string($check) && $check == $refURL) {
          $type = 'reply';
          continue;
        } elseif(is_array($check)) {
          if(array_key_exists('type', $check) && in_array('h-cite', $check['type'])) {
            if(array_key_exists('properties', $check) && array_key_exists('url', $check['properties'])) {
              if(in_array($refURL, $check['properties']['url'])) {
                $type = 'reply';
              }
            }
          }
        }
      }
    }
    // If the post has an explicit tag-of property, verify it matches $refURL and set the type to "tag"
    if($refURL && array_key_exists('tag-of', $properties)) {
      // tag-of may be a string or an h-cite
      foreach($properties['tag-of'] as $check) {
        removeScheme($check);
        if(is_string($check) && $check == $refURL) {
          $type = 'tag';
          continue;
        } elseif(is_array($check)) {
          if(array_key_exists('type', $check) && in_array('h-cite', $check['type'])) {
            if(array_key_exists('properties', $check) && array_key_exists('url', $check['properties'])) {
              if(in_array($refURL, $check['properties']['url'])) {
                $type = 'tag';
              }
            }
          }
        }
      }
      //this could be something you are actually tagged in
      if($type != 'tag'){
          foreach($properties['category'] as $check){
                     if(isset($check['type']) && in_array('h-card', $check['type'])) {
                         if(array_key_exists('properties', $check) && array_key_exists('url', $check['properties'])){ 
                             
                             foreach( $check['properties']['url'] as $test_url){
                                 removeScheme($test_url); 
                                 if(is_string($test_url) && $test_url == $refURL) {
                                      $type = 'tagged';
                                      continue;
                                 }
                             }

                          }
                     }
              if(is_array($cat)){
                  foreach($cat as $check){
                  }
              }
          }

      }
    }
    if($type=='tag'){
        $tags = array();
        foreach($properties['category'] as $check) {
            if(is_string($check)){
                $tag=array('category' => $check);
            } elseif(is_array($check)){
                $tag=array();
                if(array_key_exists('value', $check) && is_string($check['value'])) {
                    $tag['name'] = $check['value'];
                }
                if(array_key_exists('properties', $check) && is_array($check['properties'])){
                    if(array_key_exists('name', $check['properties'])){
                        if(is_string($check['properties']['name'])) {
                            $tag['name'] = $check['properties']['name'];
                        } elseif (is_array($check['properties']['name']) && is_string($check['properties']['name'][0])) {
                            $tag['name'] = $check['properties']['name'][0];
                        }
                    }
                    if(array_key_exists('url', $check['properties'])){
                        if(is_string($check['properties']['url'])) {
                            $tag['url'] = $check['properties']['url'];
                        } elseif (is_array($check['properties']['url']) && is_string($check['properties']['url'][0])) {
                            $tag['url'] = $check['properties']['url'][0];
                        }
                    }
                }
                if(array_key_exists('shape', $check) && is_string($check['shape'])) {
                    $tag['shape'] = $check['shape'];
                }
                if(array_key_exists('coords', $check) && is_string($check['coords'])) {
                    $tag['coords'] = $check['coords'];
                }
            }
            $tags[] = $tag;
        }
    }

    // Check if the reply is an RSVP
    if(array_key_exists('rsvp', $properties)) {
      $rsvp = $properties['rsvp'][0];
      $type = 'rsvp';
    }

    // Check if the reply is an invitation
    if(array_key_exists('invitee', $properties)) {
      $inviteeProperty = $properties['invitee'][0];
      if(is_array($inviteeProperty)) {

        if(array_key_exists('name', $inviteeProperty['properties'])) {
          $invitee['name'] = $inviteeProperty['properties']['name'][0];
        }

        if(array_key_exists('url', $inviteeProperty['properties'])) {
          $invitee['url'] = $inviteeProperty['properties']['url'][0];
        }

        if(array_key_exists('photo', $inviteeProperty['properties'])) {
          $invitee['photo'] = $inviteeProperty['properties']['photo'][0];
        }

      } elseif(is_string($inviteeProperty)) {
        $invitee['url'] = $inviteeProperty;
      }
      $type = 'invite';
    }

    // Check if this post is a "repost"
    if($refURL && array_key_exists('repost-of', $properties)) {
      $this->collectURLs($properties['repost-of']);
      if(in_array($refURL, $properties['repost-of']))
        $type = 'repost';
    }

    // Also check for "u-repost" since some people are sending that. Probably "u-repost-of" will win out.
    if($refURL && array_key_exists('repost', $properties)) {
      $this->collectURLs($properties['repost']);
      if(in_array($refURL, $properties['repost']))
        $type = 'repost';
    }

    // Check if this post is a "bookmark-of"
    if($refURL && array_key_exists('bookmark-of', $properties)) {
      $this->collectURLs($properties['bookmark-of']);
      if(in_array($refURL, $properties['bookmark-of']))
        $type = 'bookmark';
    }

    // From http://indiewebcamp.com/comments-presentation#How_to_display

    // If the entry has an e-content, and if the content is not too long, use that
    if(array_key_exists('content', $properties)) {
      $content = $properties['content'][0];
      if ((is_array($content) && array_key_exists('value', $content)) || is_string($content)) {
        if (is_array($content)) {
          $content = $content['value'];
        }

        $visibleLines = array_filter(explode("\n", $content));
        if(strlen($content) <= $maxTextLength && count($visibleLines) <= $maxLines) {
          $text = $content;
        }
      }
      // If the content is not a string or array with “value”, something is wrong.
    }

    // If there is no e-content, or if it is too long
    if($text == false) {
      // if the h-entry has a p-summary, and the text is not too long, use that
      if(array_key_exists('summary', $properties)) {
        $summary = $properties['summary'][0];
        if(is_array($summary) && array_key_exists('value', $summary))
          $summary = $summary['value'];

        if(strlen($summary) <= $maxTextLength) {
          $text = $summary;
        } else {
          // if the p-summary is too long, then truncate the p-summary
          $text = $this->truncate($summary, $maxTextLength, $maxLines);
        }
      } else {
        // if no p-summary, but there is an e-content, use a truncated e-content
        if(array_key_exists('content', $properties)) {
          // $content already exists from line 127, and is guaranteed to be a string.
          $text = $this->truncate($content, $maxTextLength, $maxLines);
        }
      }
    }

    // If there is no e-content and no p-summary
    if($text == false) {
      // If there is a p-name, and it's not too long, use that
      if(array_key_exists('name', $properties)) {
        $pname = $properties['name'][0];
        if(strlen($pname) <= $maxTextLength) {
          $text = $pname;
        } else {
          // if the p-name is too long, truncate it
          $text = $this->truncate($pname, $maxTextLength, $maxLines);
        }
      }
    }

    // Now see if the "name" property of the h-entry is unique or part of the content
    if(array_key_exists('name', $properties)) {
      $nameSanitized = strtolower(strip_tags($properties['name'][0]));
      $nameSanitized = preg_replace('/ ?\.+$/', '', $nameSanitized); // Remove trailing ellipses
      // Using the already truncated version of the content here. But the "name" would not have been truncated so may be longer than the content.
      $contentSanitized = strtolower(strip_tags($text));
      $contentSanitized = preg_replace('/ ?\.+$/', '', $contentSanitized); // Remove trailing ellipses

      // If this is a "mention" instead of a "reply", and if there is no "content" property,
      // then we actually want to use the "name" property as the name and leave "text" blank.
      if(($type == 'mention' || $type == 'tagged') && !array_key_exists('content', $properties)) {
        $name = $this->truncate($properties['name'][0], $maxTextLength, $maxLines);
        $text = false;
      } else {
        if($nameSanitized != $contentSanitized and $nameSanitized !== '') {
          // If the name is the beginning of the content, we don't care
          // Same if the content is the beginning of the name (like with really long notes)
          if($contentSanitized === '' 
            || (!(strpos($contentSanitized, $nameSanitized) === 0) && !(strpos($nameSanitized, $contentSanitized) === 0))
            ) {
            // The name was determined to be different from the content, so return it
            $name = $properties['name'][0]; //truncate($properties['name'][0], $maxTextLength, $maxLines);
          }
        }
      }
    }

  }

  $result = array(
    'author' => $author,
    'published' => $published,
    'name' => $name,
    'text' => $text,
    'url' => $url,
    'type' => $type
  );

  if(!empty($syndications)){
    $result['syndications'] = $syndications;
  }
  if($type == 'invite')
    $result['invitee'] = $invitee;

  if($rsvp !== null) {
    $result['rsvp'] = $rsvp;
  }
  if(!empty($comments)) {
    $result['comments'] = $comments;
  }

  if($tags !== null) {
    $result['tags'] = $tags;
  }

  return $result;
}
}
