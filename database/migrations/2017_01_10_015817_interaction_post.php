<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InteractionPost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interaction_post', function (Blueprint $table) {
            $table->integer('post_id')->unsigned();
            $table->integer('interaction_id')->unsigned();

            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('interaction_id')->references('id')->on('interactions');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interaction_post');
    }
}
