<?php

use Faker\Generator as Faker;
use App\PersonUrl;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(PersonUrl::class, function (Faker $faker) {

    return [
        'url' => $faker->url(),
        'primary' => 0
    ];

});

