<?php

namespace Owi\database;

class Blueprint
{
    protected $table;
    protected $columns = [];
    protected $primaryKey;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function increments($column)
    {
        $this->columns[] = "$column INT AUTO_INCREMENT PRIMARY KEY";
        $this->primaryKey = $column;
        return $this;
    }

    public function integer($column)
    {
        $this->columns[] = "$column INT";
        return $this;
    }

    public function string($column, $length = 255)
    {
        $this->columns[] = "$column VARCHAR($length)";
        return $this;
    }

    public function text($column)
    {
        $this->columns[] = "$column TEXT";
        return $this;
    }

    public function boolean($column)
    {
        $this->columns[] = "$column TINYINT(1)";
        return $this;
    }

    public function timestamps()
    {
        $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    protected $foreignKeyDefs = [];

    public function foreign($column)
    {
        $this->foreignKeyDefs[] = [
            'key' => $column,
            'reference' => null,
            'table' => null,
            'on_delete' => null
        ];
        return $this;
    }

    public function references($column)
    {
        $lastIndex = count($this->foreignKeyDefs) - 1;
        if ($lastIndex >= 0) {
            $this->foreignKeyDefs[$lastIndex]['reference'] = $column;
        }
        return $this;
    }

    public function on($table)
    {
        $lastIndex = count($this->foreignKeyDefs) - 1;
        if ($lastIndex >= 0) {
            $this->foreignKeyDefs[$lastIndex]['table'] = $table;
        }
        return $this;
    }

    public function onDelete($action)
    {
        $lastIndex = count($this->foreignKeyDefs) - 1;
        if ($lastIndex >= 0) {
            $this->foreignKeyDefs[$lastIndex]['on_delete'] = $action;
        }
        return $this;
    }

    public function build()
    {
        foreach ($this->foreignKeyDefs as $fk) {
            if ($fk['key'] && $fk['reference'] && $fk['table']) {
                $sql = "FOREIGN KEY ({$fk['key']}) REFERENCES {$fk['table']}({$fk['reference']})";
                if ($fk['on_delete']) {
                    $sql .= " ON DELETE {$fk['on_delete']}";
                }
                $this->columns[] = $sql;
            }
        }
        return implode(", ", $this->columns);
    }
}

