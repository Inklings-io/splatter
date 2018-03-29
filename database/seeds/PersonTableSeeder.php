<?php

use Illuminate\Database\Seeder;
use App\Person;
use App\PersonUrl;

class PersonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0 ; $i < 200; $i ++){
            $person = factory(Person::class)->create();

            factory(PersonUrl::class)->create(['primary' => 1, 'person_id' => $person->id]);
            factory(PersonUrl::class, rand(0,3))->create(['person_id' => $person->id]);
        }
    }
}
