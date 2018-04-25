<?php

use Faker\Generator as Faker;

$factory->define(App\Media::class, function (Faker $faker) {
    $alt = null;
    if(rand(0,1) == 0){
        $alt = $faker->text(rand(10,35));
    }
    return [
        'type' => 'photo',
        'alt' => $alt,
        'path' => '/upload/photo/' . $faker->word . '.jpg'
    ];
});
