<?php

namespace Owi\utils;

class JSON
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __toString()
    {
        $json = json_encode($this->data);
        if ($json === false) {
            return json_encode(["error" => "JSON encoding error: " . json_last_error_msg()]);
        }
        return $json;
    }
}
