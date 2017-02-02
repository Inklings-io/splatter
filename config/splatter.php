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
        'url' => env('SITE_URL', 'https://www.myurl.example/'),
        'short_url' => env('SHORT_URL', false),
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
    |
    | Supported: "database", "eloquent"
    |
    */

    'owner' => [
        'name' => env('OWNER_NAME', 'Site Owner'),
        'url' => env('OWNER_URL', 'https://www.myurl.example/'),
        'image' => env('OWNER_IMG_URL', 'https://www.myurl.example/image.jpg'),
        

    ],


];
