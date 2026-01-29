<?php

namespace Owi\database;

abstract class Migration
{
    abstract public function up();
    abstract public function down();
}
