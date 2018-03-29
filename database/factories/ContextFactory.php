<?php

use Faker\Generator as Faker;
use App\Context;
use App\Person;
use Carbon\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Context::class, function (Faker $faker) {

    $now = Carbon::now()->timestamp;
    $last_week = Carbon::now()->subDays(7)->timestamp;
    $timestamp = Carbon::createFromTimestamp(rand($last_week, $now));

    $content = null;
    $title = null;

    if(rand(0,5) == 0){
        $title = $faker->sentence(rand(2,5));
    }
    $content = $faker->text(rand(20,500));


    return [
        'person_id' => function() {
            return Person::inRandomOrder()->first()->id;
        },
        'published' => $timestamp,
        'content' => $faker->text(rand(20,500)),
        'name' => $title,
        'url' => $faker->url()
    ];

});

