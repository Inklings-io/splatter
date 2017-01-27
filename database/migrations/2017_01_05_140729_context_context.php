<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ContextContext extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('context_context', function (Blueprint $table) {
            $table->integer('child_id')->unsigned();
            $table->integer('parent_id')->unsigned();

            $table->foreign('child_id')->references('id')->on('contexts');
            $table->foreign('parent_id')->references('id')->on('contexts');
 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('context_context');
    }
}
