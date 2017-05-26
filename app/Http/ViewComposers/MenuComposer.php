<?php

namespace App\Http\ViewComposers;

use DB;
use App\Post;
use App\Category;

use Illuminate\View\View;
use IndieAuth;

class MenuComposer
{

    protected $recent_drafts;
    protected $top_categories;
    protected $rel_mes;

    public function __construct()
    {
        if(IndieAuth::is_user('ben.thatmustbe.me')){
            $this->recent_drafts = Post::where('draft', 1)
                ->orderBy('published', 'desc')
                ->limit(10)
                ->get();
        } else {
            $this->recent_drafts = [];
        }
        
        $this->top_categories = Category::orderBy('name')->get()
        ->reject(function($category){
            return empty($category->name) || $category->posts_count < 2; 
        });

        $this->rel_mes = config('splatter.me');
    }
 
    public function compose(View $view)
    {            
        $view->with('recent_drafts',    $this->recent_drafts);
        $view->with('categories',       $this->top_categories);
        $view->with('rel_mes',          $this->rel_mes);

    }

}
