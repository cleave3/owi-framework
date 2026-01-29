<?php

namespace Owi\utils;

class Response
{

    public static $res = [];

    /**
     * send returns the response array
     *
     * @param array $data
     * @return Array
     */
    public static function send($data = [])
    {
        foreach ($data as $resp => $key) {
            self::$res[$resp] = $key;
        }
        return self::$res;
    }

    /**
     * json returns response array as json
     *
     * @param array $data
     * @return JSON object
     */
    public static function json($data = [], $code = 200)
    {
        foreach ($data as $resp => $key) {
            self::$res[$resp] = $key;
        }
        header("Content-Type: application/json");
        http_response_code($code);
        exit(json_encode(self::$res));
    }
}
