<?php

namespace Owi\database;

use Owi\config\PDOConnection;
use Owi\database\QueryBuilder;
use Exception;

#[ \AllowDynamicProperties ]
abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $conn;
    protected $queryBuilder;

    public function __construct()
    {
        $this->conn = PDOConnection::getInstance()->getConnection();
        $this->queryBuilder = new QueryBuilder($this->conn);
        
        if (!$this->table) {
            // Default table name strategy: singular class name snake_case + s
            // For now, require explicit definition or use simple pluralizer if needed
            // Let's assume the user defines it or we just use class name lowercase + s
            $path = explode('\\', static::class);
            $this->table = strtolower(array_pop($path)) . 's';
        }
    }

    /**
     * Define a one-to-one relationship.
     *
     * @param string $relatedModel The related model class name
     * @param string|null $foreignKey The foreign key in the related model
     * @param string|null $localKey The local key in the current model
     * @return mixed
     */
    public function hasOne($relatedModel, $foreignKey = null, $localKey = null)
    {
        $instance = new $relatedModel();
        
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->primaryKey;

        return $instance->where($foreignKey, $this->{$localKey})->first();
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param string $relatedModel The related model class name
     * @param string|null $foreignKey The foreign key in the related model
     * @param string|null $localKey The local key in the current model
     * @return array
     */
    public function hasMany($relatedModel, $foreignKey = null, $localKey = null)
    {
        $instance = new $relatedModel();
        
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->primaryKey;

        return $instance->where($foreignKey, $this->{$localKey})->get();
    }

    /**
     * Define an inverse one-to-one or many-to-one relationship.
     *
     * @param string $relatedModel The related model class name
     * @param string|null $foreignKey The foreign key in the current model
     * @param string|null $ownerKey The local key in the related model
     * @return mixed
     */
    public function belongsTo($relatedModel, $foreignKey = null, $ownerKey = null)
    {
        $instance = new $relatedModel();
        
        $foreignKey = $foreignKey ?: $instance->getForeignKey();
        $ownerKey = $ownerKey ?: $instance->primaryKey;

        return $instance->where($ownerKey, $this->{$foreignKey})->first();
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param string $relatedModel The related model class name
     * @param string|null $pivotTable The pivot table name
     * @param string|null $foreignPivotKey The foreign key in the pivot table for this model
     * @param string|null $relatedPivotKey The foreign key in the pivot table for the related model
     * @return array
     */
    public function belongsToMany($relatedModel, $pivotTable = null, $foreignPivotKey = null, $relatedPivotKey = null)
    {
        $instance = new $relatedModel();
        
        if ($pivotTable === null) {
            // Alphabetical order of table names for pivot table
            $tables = [$this->table, $instance->table];
            sort($tables);
            $pivotTable = implode('_', $tables);
        }

        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();
        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        // SQL to fetch related records through pivot
        $sql = "SELECT r.* FROM {$instance->table} r
                INNER JOIN $pivotTable p ON r.{$instance->primaryKey} = p.$relatedPivotKey
                WHERE p.$foreignPivotKey = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$this->{$this->primaryKey}]);
        
        return $stmt->fetchAll(\PDO::FETCH_CLASS, $relatedModel);
    }

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey()
    {
        // Example: User -> user_id
        $path = explode('\\', static::class);
        $className = array_pop($path);
        return strtolower($className) . '_id';
    }

    /**
     * Get the model's attributes (excluding internal properties).
     * 
     * @return array
     */
    public function getAttributes()
    {
        $attributes = get_object_vars($this);
        // Remove internal protected properties
        unset($attributes['table'], $attributes['primaryKey'], $attributes['conn'], $attributes['queryBuilder']);
        return $attributes;
    }

    /**
     * Save the model to the database.
     * 
     * @return bool
     */
    public function save()
    {
        $data = $this->getAttributes();
        
        // If primary key is set and not null, perform update
        if (isset($this->{$this->primaryKey}) && !empty($this->{$this->primaryKey})) {
            // Unset primary key from data to avoid updating it (optional, but good practice)
            // But we need it for the WHERE clause, which is separate.
             // We shouldn't include primary key in SET clause typically if it's auto-increment, 
             // though standard update usually allows it. Safe to remove.
             $pkVal = $this->{$this->primaryKey};
             unset($data[$this->primaryKey]);
             
             return $this->queryBuilder
                        ->table($this->table)
                        ->where($this->primaryKey, $pkVal)
                        ->update($data);
        } else {
            // Insert
            $result = $this->queryBuilder
                         ->table($this->table)
                         ->insert($data);
            
            if ($result) {
                // Set the ID on the model
                $this->{$this->primaryKey} = $this->conn->lastInsertId();
                 return true;
            }
            return false;
        }
    }

    /**
     * Find a model by its primary key.
     * 
     * @param mixed $id
     * @return static|null
     */
    public function find($id)
    {
        return $this->where($this->primaryKey, $id)->first();
    }
    public function __call($method, $args)
    {
        // Initialize QueryBuilder with table if not already set
        $result = $this->queryBuilder->table($this->table)->$method(...$args);
        
        // If the result is the builder itself, return $this (the model instance) to keep chain
        if ($result instanceof QueryBuilder) {
            return $this;
        }
        
        if ($method === 'get') {
            // Convert arrays to instances of this Model
            $models = [];
            foreach ($result as $row) {
                $model = new static();
                foreach ($row as $key => $value) {
                    $model->$key = $value;
                }
                $models[] = $model;
            }
            return $models;
        }
        
        if ($method === 'first') {
            if ($result) {
                $model = new static();
                foreach ($result as $key => $value) {
                    $model->$key = $value;
                }
                return $model;
            }
            return null;
        }

        return $result;
    }
}
