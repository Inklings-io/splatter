<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Webmentions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webmentions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('source_url');
            $table->string('target_url');
            $table->string('vouch_url')->nullable();
            $table->string('status')->nullable();
            $table->string('status_code')->default('202');
            $table->string('admin_op')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webmentions');
    }
}
