<?php

use Faker\Generator as Faker;
use App\Post;
//use App\Category;
use Carbon\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Post::class, function (Faker $faker) {

    // ['article','note','photo','checkin','event','rsvp','like','bookmark','listen','watch','video','audio','tag','follow','unfollow','repost','snark','weight','reply'])
    $types = ['note','note','note', 'article', 'like', 'reply', 'snark', 'checkin', 'weight']; //weight toward notes
    //$now = Carbon::now()->timestamp;
    //$last_week = Carbon::now()->subDays(7)->timestamp;
    //$timestamp = Carbon::createFromTimestamp(rand($last_week, $now));
    $timestamp = Carbon::instance($faker->dateTimeBetween('-90 days'));


    $type = $faker->randomElement($types);

    $like_of = null;
    $title = null;
    $slug = '-';
    $location = null;
    $weight = null;

    if($type == 'article'){
        $content = $faker->text(1000);
        $title = $faker->sentence(rand(3,7));
        $slug = preg_replace('/\ /', '-', $title);

    } elseif($type=='like') {
        $like_of = $faker->url();
        $content = 'I like ' . $like_of;

    } elseif($type=='checkin') {
        $place = $faker->word();
        $content = 'I\'m at ' . $place;
        $location = '{"latitude":'.$faker->latitude() . ',"longitude":'.$faker->longitude().',"name":"'. $place .'"}';

    } elseif($type=='weight') {
        $content = $faker->sentence(rand(3,7));
        $weight = '{"unit":"lbs","num":'. (rand(2000, 2510)/ 10.0)  .'}';

    } else { //note and reply and snark 
        $content = $faker->text();
        if(rand(0,2) >=1 ){
            $title = $faker->sentence(rand(3,7));
            $slug = preg_replace('/\ /', '-', $title);
        }
    }

    //$categories = Category::inRandomOrder()->limit(2);

    return [
        'year' => $timestamp->year,
        'month' => $timestamp->month,
        'day' => $timestamp->day,
        'daycount' => count(Post::where(['year' => $timestamp->year, 'month' => $timestamp->month, 'day' => $timestamp->day])->get()) + 1,
        'slug' => $slug,
        'type' => $type,
        'content-format' => 'plaintext',
        'name' => $title,
        'like-of' => $like_of,
        'content' => $content,
        'location' => $location,
        'weight' => $weight,
        'published' => $timestamp,
        //'categories' => $categories,
        'created_by' => 'https://inklings.io'
    ];

});

