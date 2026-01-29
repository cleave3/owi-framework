<?php

namespace Owi\database;

use PDO;

class QueryBuilder
{
    protected $pdo;
    protected $table;
    protected $select = '*';
    protected $where = [];
    protected $params = [];
    protected $limit;
    protected $orderBy;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function select($columns = '*')
    {
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    public function where($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->where[] = "$column $operator ?";
        $this->params[] = $value;
        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy = "$column $direction";
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function get()
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy}";
        }

        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first()
    {
        $this->limit(1);
        $result = $this->get();
        return !empty($result) ? $result[0] : null;
    }

    public function insert(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    public function update(array $data)
    {
        $set = implode(', ', array_map(function ($key) {
            return "$key = ?";
        }, array_keys($data)));

        $sql = "UPDATE {$this->table} SET $set";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        $params = array_merge(array_values($data), $this->params);

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->params);
    }
}
