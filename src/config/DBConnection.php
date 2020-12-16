<?php

namespace App\config;

(new DotEnv(__DIR__ . '/../../.env'))->load();

class DBConnection
{
    private $dbhost = 'localhost';
    private $dbname = 'logistics';
    private $dbuser = 'root';
    private $dbpassword = '';
    public $app_env;

    public function __construct()
    {
        $this->app_env = getenv("APP_ENV");

        if ($this->app_env == "production") {
            $this->dbhost = getenv("DB_HOST");
            $this->dbname = getenv("DB_NAME");
            $this->dbuser = getenv("DB_USER");
            $this->dbpassword = getenv("DB_PASSWORD");
        }

        $dbconn = new \mysqli($this->dbhost, $this->dbuser, $this->dbpassword, $this->dbname);
        return $dbconn;
    }
}
