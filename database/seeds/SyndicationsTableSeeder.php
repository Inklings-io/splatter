<?php

use Illuminate\Database\Seeder;

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
        factory(App\SyndicationSite::class, 100)->create();
    }
}
