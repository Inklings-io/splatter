<?php

use Faker\Generator as Faker;
use App\PostSyndication;
use App\SyndicationSite;

$factory->define(PostSyndication::class, function (Faker $faker) {
    return [
        'url' => $faker->url(),
        'syndication_site_id' => function() {
            return SyndicationSite::inRandomOrder()->first()->id;
        },
    ];
});
