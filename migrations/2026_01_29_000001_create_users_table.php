<?php

use Owi\database\Migration;
use Owi\database\Blueprint;
use Owi\database\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('users');
    }
}
