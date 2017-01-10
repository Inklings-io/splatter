<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Posts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            //basic metadata
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('context_parsed')->default(false);
            $table->boolean('draft')->default(false);
            $table->enum('content-format', ['html','plaintext', 'markdown'])->default('html');

            //meta data useful for URL
            $table->smallInt('year');
            $table->tinyInteger('month');
            $table->tinyInteger('day');
            $table->smallInt('daycount');
            $table->string('slug')->nullable();
            $table->enum('type', ['article','note','photo','checkin','event','rsvp','like','bookmark','listen','watch','video','audio','tag','follow','unfollow','repost','snark','weight'])->default('note');

            //displayed data
            $table->longtext('content');
            $table->string('name')->nullable();
            $table->string('summary')->nullable();
            $table->string('syndication_extra')->nullable(); //used for bridgy publishing
            $table->string('created_by')->nullable(); //url or name of posting app
            $table->timestamp('published');

//note that in-reply-to is not listed here, it uses its own table to allow multiples

            // post type specific fields
            $table->string('like-of')->nullable(); //url
            $table->string('bookmark-of')->nullable(); //url
            $table->string('repost-of')->nullable(); //url
            $table->string('artist')->nullable(); 
            $table->string('rsvp')->nullable(); //'yes', 'no', 'maybe', etc
            $table->timestamp('event_start')->nullable(); 
            $table->timestamp('event_end')->nullable(); 
            $table->string('location')->nullable(); //stored as JSON
            $table->string('weight')->nullable(); //stored as JSON

            //TODO: tags? followings?
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
