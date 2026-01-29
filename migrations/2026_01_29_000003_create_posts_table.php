<?php

use Owi\database\Migration;
use Owi\database\Blueprint;
use Owi\database\Schema;

class CreatePostsTable extends Migration
{
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug');
            $table->string('content'); // Using string for simplicity, could be text if supported by Blueprint
            $table->integer('user_id');
            $table->integer('category_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('posts');
    }
}
