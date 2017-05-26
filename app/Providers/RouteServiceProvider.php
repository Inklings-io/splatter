<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //
        Route::pattern('type', '(article|note|photo|checkin|event|rsvp|like|bookmark|listen|watch|video|audio|tag|follow|unfollow|repost|snark|weight)');
        
        Route::pattern('type_i', '(?i)(article|note|photo|checkin|event|rsvp|like|bookmark|listen|watch|video|audio|tag|follow|unfollow|repost|snark|weight)(?-i)');
        Route::pattern('type_any', '[a-zA-Z]+');
        
        Route::pattern('year', '[0-9][0-9][0-9][0-9]');
        Route::pattern('month', '(0?[0-9]|1[0-2])');
        Route::pattern('day', '([0-2]?[0-9]|3[01])');
        Route::pattern('daycount', '[0-9]+');

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }
}
