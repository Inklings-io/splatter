<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PostSyndication extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_syndication', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url');

            $table->integer('syndication_site_id')->unsigned();

            $table->foreign('syndication_site_id')->references('id')->on('syndication_sites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_syndication');
    }
}
