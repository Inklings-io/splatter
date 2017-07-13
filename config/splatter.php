<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sitewide Settings
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'site' => [
        'name' => env('SITE_NAME', 'myurl.example'),
        //This should use APP_URL instead
        //'url' => env('SITE_URL', 'https://www.myurl.example/'),
        'short_url' => env('SHORT_URL', env('APP_URL', false)),
    ],

    'webmention' => [
        'enabled' => env('USE_WEBMENTION', true),
        'use_vouch' => env('USE_VOUCH', false)
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |`
    | Supported: "database", "eloquent"
    |
    */

    'owner' => [
        'name' => env('OWNER_NAME', 'Site Owner'),
        'url' => env('OWNER_URL', 'https://www.myurl.example/'),
        'image' => env('OWNER_IMG_URL', 'https://www.myurl.example/image.jpg'),
        'role' => env('OWNER_ROLE', null),
        

    ],

    'me' => [
        //to disable all options, comment out all options in this block;
    
        'facebook' => env('FACEBOOK_URL', false),
        'twitter' =>  env('TWITTER_URL', 'https://www.twitter.com/username'),
        'github' =>   env('GITHUB_URL', false),
    
        // Add as many sites as you like here
        //  'somesite' => 'https://www.site.example/',
        // 
    ],

    'google_analytics_id' =>  env('GOOGLE_ANALYTICS_TRACKING_CODE', false),


];
