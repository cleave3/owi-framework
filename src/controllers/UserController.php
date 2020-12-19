<?php

namespace App\controllers;

use App\controllers\BaseController;

class UserController extends BaseController
{

    public function index()
    {
        return $this->response->json(["status" => true, "data" => ["name" => "cleave", "age" => 27]]);
    }

    public function add()
    {
        return $this->response->code(200)->json($_SERVER);
    }

    public function edit()
    {
    }

    public function delete()
    {
        return "here";
    }
}
