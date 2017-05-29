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

    public function __construct()
    {
        $this->owner = config('splatter.owner');
        $this->app_url = config('app.url');
        $this->site_name = config('splatter.site.name');
    }
 
    public function compose(View $view)
    {            
        $view->with('owner',    $this->owner);
        $view->with('app_url',  $this->app_url);
        $view->with('site_name',  $this->site_name);

    }

}
