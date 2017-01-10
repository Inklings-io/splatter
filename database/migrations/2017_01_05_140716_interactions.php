<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Interactions extends Migration
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
            $table->integer('person_id')->unsigned;
            //$table->integer('webmention_id')->unsigned;
            $table->enum('type', ['reply','like','repost','mention','reacji'])->default('reply');

            $table->softDeletes();
            $table->timestamp('parsed');
            $table->boolean('approved')->default(false); 
            $table->timestamp('published');
            $table->longtext('content');
            $table->string('name')->nullable();
            $table->string('url');

            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('people');>

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
