<?php

use Illuminate\Database\Seeder;
use App\SyndicationSite;

class SyndicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //factory(App\SyndicationSite::class, 10)->create();

        factory(SyndicationSite::class)->create(['name' =>'Facebook',  'image' => '/image/static/facebook-app.png', 'url_match' => 'https://www.facebook.com/']);
        factory(SyndicationSite::class)->create(['name' =>'Twitter',   'image' => '/image/static/twitter.png',      'url_match' => 'https://twitter.com/']);
        factory(SyndicationSite::class)->create(['name' =>'Google+',   'image' => '/image/static/googleplus.png',   'url_match' => 'https://plus.google.com/']);
        factory(SyndicationSite::class)->create(['name' =>'Instagram', 'image' => '/image/static/instagram.png',    'url_match' => 'http://instagram.com/']);
        factory(SyndicationSite::class)->create(['name' =>'IndieNews', 'image' => NULL,                             'url_match' => 'http://news.indiewebcamp.com/']);

    }
}
