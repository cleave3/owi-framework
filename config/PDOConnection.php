<?php

namespace App\config;

use PDO;
use PDOException;

class PDOConnection
{
    private static $instance = null;
    private $conn;

    private $dbhost;
    private $dbname;
    private $dbuser;
    private $dbpassword;
    private $dbdriver;

    private function __construct()
    {
        try {
            $this->dbdriver = getenv("DB_TYPE");
            $this->dbhost = getenv("DB_HOST");
            $this->dbname = getenv("DB_NAME");
            $this->dbuser = getenv("DB_USER");
            $this->dbpassword = getenv("DB_PASSWORD");
            
            $port = getenv("DB_PORT");
            $charset = getenv("DB_CHARSET") ?: 'utf8mb4';

            switch ($this->dbdriver) {
                case 'mysql':
                    $port = $port ?: 3306;
                    $driver = "mysql:host={$this->dbhost};port={$port};dbname={$this->dbname};charset={$charset}";
                    break;

                case 'pgsql':
                    $port = $port ?: 5432;
                    $driver = "pgsql:host={$this->dbhost};port={$port};dbname={$this->dbname}";
                    break;

                case 'sqlite':
                    $driver = "sqlite:{$this->dbname}";
                    break;

                case 'sqlsrv':
                    $port = $port ?: 1433;
                    $driver = "sqlsrv:Server={$this->dbhost},{$port};Database={$this->dbname}";
                    break;

                default:
                    throw new PDOException("Unsupported database driver: {$this->dbdriver}");
            }

            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            );

            $this->conn = new PDO($driver, $this->dbuser, $this->dbpassword, $options);
        } catch (PDOException $e) {
            // Handle SQLite connection specifically if needed or generic
             die("Database Connection Error: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new PDOConnection();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
