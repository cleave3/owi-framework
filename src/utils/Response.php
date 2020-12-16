<?php

namespace App\utils;

class Response
{

    public $res = [];

    /**
     * sets response status 
     *
     * @param boolean $status
     * @return void
     */
    public function status($status = false)
    {
        $this->res['status'] = $status;
        return $this;
    }

    /**
     * code sets response and server response status code
     * Defaults to server status code
     *
     * @param integer $code -  status code
     * @return void
     */
    public function code($code  = "")
    {
        $this->res['code'] = empty($code) ? http_response_code() :  $code;
        http_response_code($code);
        return $this;
    }

    /**
     * success - returns a success response
     *
     * @param string $message
     * @param array $data - data
     * @return void
     */
    function success($message = 'success', $data = "")
    {
        $this->res['status'] = true;
        $this->res['message'] = $message;
        $this->res['data'] = $data;
        return $this;
    }

    /**
     * badRequest - returns a failure response
     *
     * @param string $message
     * @param array $data - data
     * @return void
     */
    function badRequest($message = 'error')
    {
        $this->res['status'] = false;
        $this->res['message'] = $message;
        return $this;
    }

    /**
     * send returns the response array
     *
     * @param array $data
     * @return Array
     */
    public function send($data = [])
    {
        if (count($data) > 0) {
            try {
                foreach ($data as $resp => $key) {
                    $this->res[$resp] = $key;
                }
            } catch (\Throwable $e) {
                throw $e->getMessage();
            }
        }
        return $this->res;
    }

    /**
     * json returns response array as json
     *
     * @param array $data
     * @return JSON object
     */
    public function json($data = [])
    {
        if (count($data) > 0) {
            try {
                foreach ($data as $resp => $key) {
                    $this->res[$resp] = $key;
                }
            } catch (\Throwable $e) {
                throw $e->getMessage();
            }
        }
        return json_encode($this->res);
    }
}
