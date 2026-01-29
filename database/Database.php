<?php

namespace App\database;

use App\config\PDOConnection;

class Database
{
    protected $connection;

    public function __construct()
    {
        $this->connection = PDOConnection::getInstance()->getConnection();
    }

    public function table($table)
    {
        return (new QueryBuilder($this->connection))->table($table);
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function startTransaction()
    {
        return $this->connection->beginTransaction();
    }

    public function commitTransaction()
    {
        return $this->connection->commit();
    }

    public function rollbackTransaction()
    {
        return $this->connection->rollBack();
    }
}
