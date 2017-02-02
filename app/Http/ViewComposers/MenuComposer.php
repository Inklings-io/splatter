<?php

namespace App\Http\ViewComposers;

use DB;
use App\Post;
use App\Category;

use Illuminate\View\View;


class MenuComposer
{

    protected $recent_drafts;

    public function __construct()
    {
        $this->recent_drafts = Post::where('draft', 1)
            ->orderBy('published', 'desc')
            ->limit(10)
            ->get();
        
    }
 
    public function compose(View $view)
    {            
        $view->with('recent_drafts',  $this->recent_drafts);
    }

}
