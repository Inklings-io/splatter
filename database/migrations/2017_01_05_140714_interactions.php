<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Interactions 
extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('person_id')->unsigned();
            $table->integer('webmention_id')->unsigned();
            $table->enum('type', ['reply','like','repost','mention','reacji', 'rsvp'])->default('reply');
            $table->boolean('person-mention')->default(false);

            $table->boolean('approved')->default(false); 
            $table->timestamp('published')->nullable();
            $table->longtext('content');
            $table->string('name')->nullable();
            $table->string('url');
            $table->string('rsvp');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('person_id')->references('id')->on('people');
            $table->foreign('webmention_id')->references('id')->on('webmentions');

            //more can be added for tagging and rsvp, etc

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interactions');
    }
}
