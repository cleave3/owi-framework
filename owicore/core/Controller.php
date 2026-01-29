<?php

namespace Owi\core;

use Owi\config\DotEnv;
use Owi\config\PDOConnection;
use Exception;
use PDO;



abstract class Controller
{
    protected $conn;
    public $body;
    public $query;
    public $file;
    
    /**
     * @var array List of middleware class names
     */
    protected $middlewares = [];

    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    public function __construct()
    {
        $this->body = $_POST;
        $this->query = $_GET;
        $this->file = $_FILES;
        $this->conn = PDOConnection::getInstance()->getConnection();
    }

    /**
     * Start transaction
     */
    public function startTransaction()
    {
        $this->conn->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commitTransaction()
    {
        $this->conn->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollbackTransaction()
    {
        $this->conn->rollBack();
    }

    /**
     * Retrieve a single record
     *
     * @param string $table
     * @param array $conditions Associative array of column => value
     * @param string $fields
     * @return mixed
     */
    public function findOne(string $table, array $conditions = [], string $fields = "*")
    {
        $where = "";
        if (!empty($conditions)) {
            $whereParts = [];
            foreach (array_keys($conditions) as $key) {
                $whereParts[] = "$key = :$key";
            }
            $where = "WHERE " . implode(" AND ", $whereParts);
        }

        $sql = "SELECT $fields FROM $table $where LIMIT 1";
        $stmt = $this->conn->prepare($sql);

        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Retrieve multiple records
     *
     * @param string $table
     * @param array $conditions Associative array of column => value
     * @param string $fields
     * @return array
     */
    public function findAll(string $table, array $conditions = [], string $fields = "*"): array
    {
        $where = "";
        if (!empty($conditions)) {
            $whereParts = [];
            foreach (array_keys($conditions) as $key) {
                $whereParts[] = "$key = :$key";
            }
            $where = "WHERE " . implode(" AND ", $whereParts);
        }

        $sql = "SELECT $fields FROM $table $where";
        $stmt = $this->conn->prepare($sql);

        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Update records
     *
     * @param string $table
     * @param array $data Associative array of column => value to update
     * @param array $conditions Associative array of column => value for WHERE clause
     * @return bool
     */
    public function update(string $table, array $data, array $conditions): bool
    {
        if (empty($data)) return false;

        $setParts = [];
        foreach (array_keys($data) as $key) {
            $setParts[] = "$key = :set_$key";
        }
        $set = implode(", ", $setParts);

        $whereParts = [];
        foreach (array_keys($conditions) as $key) {
            $whereParts[] = "$key = :where_$key";
        }
        $where = implode(" AND ", $whereParts);

        $sql = "UPDATE $table SET $set WHERE $where";
        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":set_$key", $value);
        }
        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":where_$key", $value);
        }

        return $stmt->execute();
    }

    /**
     * Delete records
     *
     * @param string $table
     * @param array $conditions
     * @return bool
     */
    public function destroy(string $table, array $conditions): bool
    {
        if (empty($conditions)) {
            // Prevent accidental delete all
            throw new Exception("Conditions required for delete");
        }

        $whereParts = [];
        foreach (array_keys($conditions) as $key) {
            $whereParts[] = "$key = :$key";
        }
        $where = implode(" AND ", $whereParts);

        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $this->conn->prepare($sql);

        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    /**
     * Get count of records
     *
     * @param string $table
     * @param array $conditions
     * @return int
     */
    public function getCount(string $table, array $conditions = []): int
    {
        $where = "";
        if (!empty($conditions)) {
            $whereParts = [];
            foreach (array_keys($conditions) as $key) {
                $whereParts[] = "$key = :$key";
            }
            $where = "WHERE " . implode(" AND ", $whereParts);
        }

        $sql = "SELECT COUNT(*) FROM $table $where";
        $stmt = $this->conn->prepare($sql);

        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Get paginated records
     *
     * @param string $table
     * @param int $page
     * @param int $limit
     * @param array $conditions
     * @param string $fields
     * @return array
     */
    public function paginate(string $table, int $page = 1, int $limit = 20, array $conditions = [], string $fields = "*"): array
    {
        $page = $page < 1 ? 1 : $page;
        $offset = ($page - 1) * $limit;

        $total = $this->getCount($table, $conditions);
        $totalPages = ceil($total / $limit);

        $where = "";
        if (!empty($conditions)) {
            $whereParts = [];
            foreach (array_keys($conditions) as $key) {
                $whereParts[] = "$key = :$key";
            }
            $where = "WHERE " . implode(" AND ", $whereParts);
        }

        $sql = "SELECT $fields FROM $table $where LIMIT $limit OFFSET $offset";
        $stmt = $this->conn->prepare($sql);

        foreach ($conditions as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        $data = $stmt->fetchAll();

        return [
            "totalrecords" => $total,
            "rows" => count($data),
            "offset" => $offset,
            "limit" => $limit,
            "totalpages" => $totalPages,
            "currentpage" => $page,
            "data" => $data
        ];
    }

    /**
     * Execute custom query
     *
     * @param string $query
     * @param array $params
     * @return mixed
     */
    public function exec_query(string $query, array $params = [])
    {
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        if (preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)/i', $query)) {
            return $stmt->fetchAll();
        }
        return true;
    }

    public function lastId()
    {
        return $this->conn->lastInsertId();
    }
}
