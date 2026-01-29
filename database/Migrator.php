<?php

namespace App\database;

use App\utils\DB;

class Migrator
{
    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        Schema::create('migrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('migration');
            $table->timestamps();
        });
    }

    public function run()
    {
        $files = glob(__DIR__ . '/../../migrations/*.php');
        $applied = $this->getAppliedMigrations();
        
        $newMigrations = [];

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            
            if (in_array($filename, $applied)) {
                continue;
            }

            require_once $file;
            
            // Assuming class name matches filename but StudlyCase and removing timestamp if present
            // Convention: 2024_01_01_000000_create_users_table => CreateUsersTable
            
            $className = $this->resolveClassName($filename);
            
            if (class_exists($className)) {
                $migration = new $className();
                echo "Migrating: $filename\n";
                $migration->up();
                $this->log($filename);
                echo "Migrated:  $filename\n";
            }
        }
    }

    protected function getAppliedMigrations()
    {
        return array_column(DB::table('migrations')->get(), 'migration');
    }

    protected function log($migration)
    {
        DB::table('migrations')->insert(['migration' => $migration]);
    }

    protected function resolveClassName($filename)
    {
        // Remove timestamp (first 18 chars usually: YYYY_MM_DD_HHMMSS_)
        $parts = explode('_', $filename);
        // If it starts with numeric date parts, shift them off. 
        // Simple heuristic: if first part is numeric, it's a timestamp part.
        while (is_numeric($parts[0])) {
            array_shift($parts);
        }
        
        $name = implode(' ', $parts);
        return str_replace(' ', '', ucwords($name));
    }
}
