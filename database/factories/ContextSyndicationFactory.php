<?php

use Faker\Generator as Faker;
use App\ContextSyndication;
use App\SyndicationSite;

$factory->define(ContextSyndication::class, function (Faker $faker) {
    return [
        'url' => $faker->url(),
        'syndication_site_id' => function() {
            return SyndicationSite::inRandomOrder()->first()->id;
        }
    ];
});
