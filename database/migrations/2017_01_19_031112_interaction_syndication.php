<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InteractionSyndication extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interaction_syndication', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('interaction_id')->unsigned();
            $table->integer('syndication_site_id')->unsigned();
            $table->string('url');

            $table->foreign('interaction_id')->references('id')->on('interactions');
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
        Schema::dropIfExists('interaction_syndication');
    }
}
