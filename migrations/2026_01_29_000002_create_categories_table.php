<?php

use Owi\database\Migration;
use Owi\database\Blueprint;
use Owi\database\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('categories');
    }
}
