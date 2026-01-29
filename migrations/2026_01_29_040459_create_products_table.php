<?php

use App\database\Migration;
use App\database\Blueprint;
use App\database\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('price');
            $table->text('description');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('products');
    }
}
