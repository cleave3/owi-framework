<?php

namespace App\database;

abstract class Migration
{
    abstract public function up();
    abstract public function down();
}
