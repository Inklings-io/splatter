<?php

namespace App\Jobs;

use App\Webmention;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
            $route = app('router')->getRoutes()->match(app('request')->create($url));
            $route_name = $route->getName();
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
                $comment_data = IndieWeb\comments\parse($item, $target_url, 300);
                if (!empty($comment_data['url'])) { //break out of loop if we found one
                    break;
                }
            }

            try {
                $route = app('router')->getRoutes()->match(app('request')->create($url));
                $route_name = $route->getName();
            } catch(Exception $e) {

                return false;
            }





            $data = array();


            if($route_name != 'single_post' && $route_name != 'single_post_no_slug') {
                return false;
            }

                foreach ($advanced_routes as $adv_route) {
                    $matches = array();
                    $real_url = ltrim(str_replace(array(HTTP_SERVER, HTTPS_SERVER), array('',''), $real_url), '/');
                    preg_match($adv_route['expression'], $real_url, $matches);
                    if (!empty($matches)) {
                        $model = $adv_route['controller'];
                        foreach ($matches as $field => $value) {
                            $data[$field] = $value;
                        }
                    }
                }

                try {
                    if (!$model) {
                        throw new Exception('No Model Set.');
                    } else {
                        $this->load->model('blog/interaction');
                        if ($editing) {
                            $interaction_id = $this->model_blog_interaction->editWebmention($data, $webmention_id, $comment_data);
                        } else {
                            $interaction_id = $this->model_blog_interaction->addWebmention($data, $webmention_id, $comment_data);
                        }

                        //salmention
                        $res = $this->db->query("SELECT post_id " .
                            " FROM " . DATABASE . ".interaction_post " .
                            " WHERE interaction_id = '" . (int)$interaction_id . "' LIMIT 1");
                        if ($res->row) {
                            $post_id = $res->row['post_id'];
                            if (defined('QUEUED_SEND')) {
                                $this->model_webmention_send_queue->addEntry($post_id);
                            } else {
                                $this->load->controller('webmention/queue/sendWebmention', $post_id);
                            }
                        }
                        //end salmention
                    }
                } catch (Exception $e) {
                    if (empty($comment_data['url'])) {
                        $comment_data['url'] = $real_source_url;
                    }

                    $interaction_type = 'mention';
                    if (isset($comment_data['type']) && $comment_data['type'] == 'like') {
                        $interaction_type = 'like';
                    } elseif (isset($comment_data['type']) && $comment_data['type'] == 'tagged') {
                        $interaction_type = 'tagged';
                    }


                    $this->load->model('blog/person');
                    $person_id = $this->model_blog_person->storePerson($comment_data['author']);

                    $this->db->query(
                        "INSERT INTO " . DATABASE . ".interactions " .
                        " SET source_url = '" . $comment_data['url'] . "'" .
                        ((isset($comment_data['tag-of']) && !empty($comment_data['tag-of']))
                        ? ", tag_of='" . $comment_data['tag-of'] . "'"
                        : "") .
                        ", person_id ='" . $person_id . "'" .
                        ", type='" . $interaction_type . "'" .
                        ", `person-mention` = 1 " . // TODO: does this make sense?
                        ", webmention_id='" . $webmention_id . "'" .
                        ""
                    );
                    $interaction_id = $this->db->getLastId();
                    $this->db->query("UPDATE " . DATABASE . ".webmentions SET status_code = '200', status = 'OK' " .
                        " WHERE id = " . (int)$webmention_id);
                    $this->cache->delete('interactions');


                }

    }
}
