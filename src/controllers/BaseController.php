<?php

namespace App\controllers;

use App\config\DBConnection;
use App\utils\Response;

class BaseController
{
    public $response;
    private $conn;


    public function __construct()
    {
        $this->response = new Response();
        $this->conn = (new DBConnection())->__construct();
    }

    /**
     * DB error logger
     *
     * @param string $error
     * @return void
     */
    public function errorhandler($error = '')
    {
        $debug = debug_backtrace()[1];
        $line = $debug['line'];
        $file = $debug['file'];
        error_log("[" . date("D d-m-Y h:i:s A T") . "] Error: " . $error . " at $file on line $line \n", 3, "error.log");
    }

    /**
     * findOne
     *
     * Gets a single record from a database table
     * 
     * @param string $tabelename - target table name
     * @param string $condition - search condition
     * @param string $fields - optional param for specifying fields to return.if not passed, all fields will be returned
     * @return object - the response object
     */
    public function findOne($tabelename, $condition, $fields = "*")
    {
        $query = "SELECT $fields FROM $tabelename WHERE $condition LIMIT 1";
        $result = $this->conn->query($query) or $this->errorhandler($this->conn->error);
        $data = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return count($data) > 0 ? $data[0] : null;
    }

    /**
     * findAll
     *
     * Gets an all records from a database table
     * 
     * @param string $tabelename - target table name
     * @param string $fields - optional param for specifying fields to return.if not passed, all fields will be returned
     * @return array - the response array of objects
     */
    public function findAll($tabelename, $fields = "*")
    {
        $query = "SELECT $fields FROM $tabelename";
        $result = $this->conn->query($query) or $this->errorhandler($this->conn->error);
        $data = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * findAllWhere
     *
     * Gets an array of record from a database table with a given condition
     * @param string $tabelename - target table name
     * @param mixed $condition - search condition
     * @param string $fields - optional param for specifying fields to return.if not passed, all fields will be returned
     * @return array - the response array of objects
     */
    public function findAllWhere($tabelename, $condition = 1, $fields = "*")
    {
        $query = "SELECT $fields FROM $tabelename WHERE $condition";
        $result = $this->conn->query($query) or $this->errorhandler($this->conn->error);
        $data = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * create
     *
     * insert data to a table
     * @param string $tabelename - target table name
     * @param string $fields - Fields to insert into e.g "name, age ..."
     * @param string $values - Values to be inserted into field e.g "'John', '20' ..."
     * @return boolean - true or false
     */
    public function create($tabelename, $fields, $values)
    {
        $query = "INSERT INTO $tabelename ($fields) VALUES($values)";
        $result = $this->conn->query($query) or $this->errorhandler($this->conn->error);
        return $result;
    }

    /**
     * Update
     *
     * @param string $tabelename
     * @param string $query
     * @param string $condition
     * @return void
     */
    public function update($tabelename, $query, $condition)
    {
        $sql = "UPDATE $tabelename SET " . $query . " " . $condition;
        $result = $this->conn->query($sql) or $this->errorhandler($this->conn->error);
        return $result;
    }

    /**
     * Delete
     *
     * @param string $tabelename
     * @param string $condition
     * @return void
     */
    public function destroy($tabelename, $condition)
    {
        $check = $this->findOne($tabelename, $condition);
        if ($check == null) return $check;
        $query = "DELETE FROM $tabelename WHERE $condition";
        $result = $this->conn->query($query) or $this->errorhandler($this->conn->error);
        return $result;
    }

    public function getCount($tabelename, $condition)
    {
        $query = "SELECT * FROM $tabelename WHERE $condition";
        $result = $this->conn->query($query) or $this->errorhandler($this->conn->error);
        return $result->num_rows;
    }

    /**
     * Paginate
     *
     * @param string $tabelename
     * @param integer $pageno
     * @param integer $limit
     * @param string $fields
     * @param integer $condition
     * @return void
     */
    public function paginate($tabelename, $condition = 1, $pageno, $limit, $fields = "*")
    {
        $total = $this->getCount($tabelename, $condition);
        $offset = ($pageno - 1) * $limit;
        $result = $this->exec_query("SELECT $fields FROM $tabelename WHERE $condition LIMIT $limit OFFSET $offset");

        $res = array();
        $res['total'] = $total;
        $res['currentpage'] = $pageno;
        $res['totalpages'] = ceil($total / $limit);
        $res["data"] = $result;
        return $res;
    }

    public function lastId()
    {
        return $this->conn->insert_id;
    }

    /**
     * Execute custom query
     *
     * @param string $query
     * @return void
     */
    public function exec_query($query)
    {
        $result = $this->conn->query($query) or $this->errorhandler($this->conn->error);
        $type = explode(" ", $query)[0];
        if ($type == "SELECT" || $type == "select") {
            $data = array();
            if ($result) {

                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            return $data;
        }
        return $result;
    }

    public function __destruct()
    {
        $this->conn = null;
    }
}
