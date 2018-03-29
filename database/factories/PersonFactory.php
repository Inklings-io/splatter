<?php

use Faker\Generator as Faker;
use App\Person;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Person::class, function (Faker $faker) {

    return [
        'name' => $faker->name(),
        'image' => '/image/person.png'
        //'image' => $faker->url()
    ];

});

