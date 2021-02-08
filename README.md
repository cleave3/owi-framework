## OWI FRAMEWORK

A basic setup for PHP projects. Suitable for MVC and API Based project

### Features

- OOP
- Autoloading
- API and view Routing
- Namespaces
- Response spec
- Environmental variables
- PDO
- Mail Service
- Base controller with full CRUD operation

```php
    - create() : inserts data to table
    - findOne() : returns a single object
    - findAll() : returns an array
    - update() : modifies records
    - destroy() : deletes records
    - lastId() : returns last insertid
    - getCount() : returns count
    - paginate() : returns paginated data
    - exec_query() :  Executes custom query
```

### Getting started

- Download or clone repo
- On terminal, run

```sh
    composer install
```

- Start application

```
    php -S localhost:8080
```

### Basic Usage

- creating a service

On the terminal run

```sh
php makeservice.php
```

```sh
Enter service name e.g TestService:
```

- creating a contoller

On the terminal run

```sh
php makecontoller.php
```

```sh
Enter controller name e.g TestController:
```

### USing Base Methods

```php
<?php

namespace App\controllers;

class UserController extends Controller
{

	public function index()
	{
        $data = $this->findAll([
			"tablename" => "inventory",
			"condition" => "client_id = :clientid",
			"bindparam" => [":clientid" => $clientid],
			"fields" => "*,(SELECT SUM(qty_in) - SUM(qty_out) FROM inventory_item WHERE inventory.id = inventory_item.inventory_id) as quantity",
		]);
		return $data;
	}

	public function add()
	{
        $this->create([
			"tablename" => "inventory_item",
			"fields" => "`inventory_id`, `qty_in`, `qty_out`",
			"values" => ":itemid,:in,:out",
			"bindparam" => [":itemid" => $itemid, ":in" => $in, ":out" => $out]
		]);
	}

	public function edit()
	{
        $this->update([
			"tablename" => "inventory",
			"fields" => "name = :name, unit_cost = :cost, unit_measure = :measure, low_stock = :lowstock, reorder = :reorder, description = :description",
			"condition" => "id = :id",
			"bindparam" => [":id" => $itemid, ":name" => $name, ":cost" => $unit_cost, ":measure" => $unit_measure, ":lowstock" => $low_stock, ":reorder" => $reorder, ":description" => $description]
		]);
	}

	public function delete()
	{

        $this->destroy([
            "tablename" => "inventory",
            "condition" => "id = :id",
            "bindparam" => [":id" => $id]
        ]);
	}
}

```
