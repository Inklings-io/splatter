<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ContextPost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('context_post', function (Blueprint $table) {
            $table->integer('context_id')->unsigned();
            $table->integer('post_id')->unsigned();

            $table->foreign('context_id')->references('id')->on('contexts');
            $table->foreign('post_id')->references('id')->on('posts');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('context_post');
    }
}
