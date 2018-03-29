<?php

use Faker\Generator as Faker;
use App\SyndicationUrl;
use App\SyndicationSite;

$factory->define(SyndicationUrl::class, function (Faker $faker) {
    return [
        'url' => $faker->url(),
        'syndication_site_id' => function() {
            return SyndicationSite::inRandomOrder()->first()->id;
        },
    ];
});
