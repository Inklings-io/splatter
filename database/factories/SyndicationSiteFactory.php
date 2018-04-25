<?php

use Faker\Generator as Faker;
use App\SyndicationSite;
use Carbon\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(SyndicationSite::class, function (Faker $faker) {

    return [
        'name' => $faker->word(),
        'image' => $faker->url(),
        'url_match' => $faker->url()
    ];

});

