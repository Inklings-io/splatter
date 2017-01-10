<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InteractionInteraction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interaction_interaction', function (Blueprint $table) {
            $table->integer('child_id')->unsigned();
            $table->integer('parent_id')->unsigned();

            $table->foreign('parent_id')->references('id')->on('interactions');
            $table->foreign('child_id')->references('id')->on('interactions');
 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interaction_interaction');
    }
}
