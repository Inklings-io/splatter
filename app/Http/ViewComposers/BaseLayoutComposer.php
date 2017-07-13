<?php

namespace App\Http\ViewComposers;

use DB;
use App\Post;
use App\Category;

use Illuminate\View\View;
use IndieAuth;

class BaseLayoutComposer
{

    protected $owner;
    protected $app_url;
    protected $site_name;
    protected $google_analytics_id;

    public function __construct()
    {
        $this->owner = config('splatter.owner');
        $this->app_url = config('app.url');
        $this->site_name = config('splatter.site.name');
        $this->google_analytics_id = config('splatter.google_analytics_id');
    }
 
    public function compose(View $view)
    {            
        $view->with('owner',    $this->owner);
        $view->with('app_url',  $this->app_url);
        $view->with('site_name',  $this->site_name);
        $added_headers = array();
        if($this->google_analytics_id){
            $added_headers[] = 
"<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', '".$this->google_analytics_id."', 'auto');
ga('send', 'pageview');

</script>";
        }
        $view->with('added_headers',  $added_headers);

    }

}
