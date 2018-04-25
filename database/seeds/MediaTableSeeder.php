<?php

use Illuminate\Database\Seeder;

class MediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Media::class)->create(['path'=>'/upload/photo/1421008616727.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1421036224207.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1422116040672.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1423069391096.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1423190143696.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1423190794609.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1423538832316.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1424021014135.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1424437286902.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1425262750743.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1425262822449.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1426503143623.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1426514939755.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1426619339047.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1426676339803.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1426866465734.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1429480901482.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1430577228845.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1431214452598.jpg']);
        factory(App\Media::class)->create(['path'=>'/upload/photo/1431829570004.jpg']);
        //
    }
}
