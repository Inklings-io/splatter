<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SyndicationsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(PersonTableSeeder::class);
        $this->call(PostsTableSeeder::class);
    }
}
