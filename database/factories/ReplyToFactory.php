<?php

use Faker\Generator as Faker;
use App\ReplyTo;

$factory->define(ReplyTo::class, function (Faker $faker) {
    return [
        'url' => $faker->url()
    ];
});
