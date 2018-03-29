<?php

use Faker\Generator as Faker;
use App\Webmention;
use Carbon\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Webmention::class, function (Faker $faker) {

    return [
        'source_url' => $faker->url(),
        'target_url' => $faker->url()
    ];

});

