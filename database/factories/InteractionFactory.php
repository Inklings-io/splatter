<?php

use Faker\Generator as Faker;
use App\Interaction;
use App\Person;
use Carbon\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Interaction::class, function (Faker $faker) {

    $types = ['reply','reply','like', 'like','repost','mention','reacji'];
        //['reply','like','repost','mention','reacji', 'rsvp']
    $now = Carbon::now()->timestamp;
    $last_week = Carbon::now()->subDays(7)->timestamp;
    $timestamp = Carbon::createFromTimestamp(rand($last_week, $now));


    $type = $faker->randomElement($types);

    $content = null;
    $title = null;

    if($type == 'reacji'){
        $content = htmlentities($faker->emoji());

    } elseif($type=='like') {
        $type = 'reacji';
        $content = html_entity_decode('&#10084;'); //a heart emoji

    } elseif($type=='reply') {
        $content = $faker->text(rand(10, 2000));


    } else { //repost and mention
        $content = $faker->text(rand(10,50));
    }


    return [
        'person_id' => function() {
            return Person::inRandomOrder()->first()->id;
        },
        'webmention_id' => factory('App\Webmention')->create()->id,

        'type' => $type,
        'person-mention' => false,

        'approved' => rand(0,10) >= 1,
        'published' => $timestamp,
        'content' => $content,
        'name' => $title,
        'url' => $faker->url(),
        'rsvp' => ''
    ];

});

