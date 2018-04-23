<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Vouch;

class ProcessReferer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $referer_url;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url)
    {
        $this->referer_url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //TODO check if it is in DB
        //TODO figure out referer_ignore part

        $referer = $this->referer_url;

        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $referer);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_MAXREDIRS, 20);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 20); 
        curl_setopt($c, CURLOPT_TIMEOUT, 120); 
        $referer = curl_getinfo($c, CURLINFO_EFFECTIVE_URL);
        $page_content = curl_exec($c);

        $reg_ex_match = '/(href=[\'"](?<href>[^\'"]+)[\'"][^>]*(rel=[\'"](?<rel>[^\'"]+)[\'"])?)/';
        $matches = array();
        preg_match_all($reg_ex_match, $page_content, $matches);

        $short_self  = trim(str_replace(array('http://', 'https://'), array('',''), config('app.url')), '/');

        $valid_link_found = false;
        for ($i = 0; $i < count($matches['href']); $i++) {
            $href = strtolower($matches['href'][$i]);
            $rel = strtolower($matches['rel'][$i]);

            if (strpos($rel, "nofollow") === false) {
                if (strpos($href, $short_self) !== false) {
                    $valid_link_found = true;
                }
            }
        }
        if (!$valid_link_found) {
            //repeat all that for rel before href (because preg_match_all doesn't like reused names)
            $reg_ex_match = '/(rel=[\'"](?<rel>[^\'"]+)[\'"][^>]*href=[\'"](?<href>[^\'"]+)[\'"])/';
            $matches = array();
            preg_match_all($reg_ex_match, $page_content, $matches);

            for ($i = 0; $i < count($matches['href']); $i++) {
                $href = strtolower($matches['href'][$i]);
                $rel = strtolower($matches['rel'][$i]);

                if (strpos($rel, "nofollow") === false) {
                    if (strpos($href, $short_self) !== false) {
                        $valid_link_found = true;
                    }
                }
            }

        }


        if ($valid_link_found) {
            //parse out the domain to store this under

            // parse url requires http at the beginning
            if (strpos($referer, 'http://') === 0  || strpos($referer, 'https://') === 0 ) {
                $domain = parse_url($referer, PHP_URL_HOST);
            } else {
                $domain = parse_url('http://' . $referer, PHP_URL_HOST);
            }

            //look for existing record in DB for this domain
            $vouch = Vouch::where(['domain' => $domain])->get()->first();

            if($vouch){
                if(empty($vouch->alt_url) && $vouch->url != $referer ){
                    $vouch->alt_url = $referer;
                    $vouch->save();
                }
            } else {
                $vouch = new Vouch();
                $vouch->domain = $domain;
                $vouch->url = $referer;
                $vouch->save();
            }


        }

    }
}
