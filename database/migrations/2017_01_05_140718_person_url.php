<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PersonUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('person_url', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url');
            $table->boolean('primary')->default(false);
            $table->integer('person_id')->unsigned();

            $table->foreign('person_id')->references('id')->on('people');
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('person_url');
    }
}
