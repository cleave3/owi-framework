<?php

namespace App\database;

use App\utils\DB;
use Closure;

class Schema
{
    public static function create($table, Closure $callback)
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        
        $columns = $blueprint->build();
        $sql = "CREATE TABLE IF NOT EXISTS $table ($columns)";
        
        DB::query($sql);
    }

    public static function drop($table)
    {
        DB::query("DROP TABLE IF EXISTS $table");
    }
}
