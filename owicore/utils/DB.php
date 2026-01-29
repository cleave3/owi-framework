<?php

namespace Owi\utils;

use Owi\database\Database;

class DB
{
    private static $instance = null;

    private static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public static function table($table)
    {
        return self::getInstance()->table($table);
    }

    public static function query($sql, $params = [])
    {
        return self::getInstance()->query($sql, $params);
    }

    public static function startTransaction()
    {
        return self::getInstance()->startTransaction();
    }

    public static function commit()
    {
        return self::getInstance()->commitTransaction();
    }

    public static function rollback()
    {
        return self::getInstance()->rollbackTransaction();
    }
}
