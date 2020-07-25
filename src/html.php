<?php

class Table
{
    /**
     * Array of items
     *
     * @var array
     */
    private $items = array();
    /**
     * Count
     *
     * @var integer
     */
    private $count = 0;
    /**
     * Title
     *
     * @var string
     */
    public $title = "";
    /**
     * number of rows
     *
     * @var integer
     */
    public $numRows = 0;

    /**
     * message function to echo the title and number of rows
     *
     * @return void
     */
    public function message()
    {
        echo "<p>Table '{$this->title}' has {$this->numRows} rows.</p>";
    }

    /**
     * Add a value to the items array
     *
     * @param [any] $value
     * @return void
     */
    public function add($value)
    {
        $this->items[$this->count++] = $value;
    }

    /**
     * Display the array of items
     *
     * @return [array] -  this items array
     */
    public function showitems()
    {
        return $this->items;
    }

    /**
     * divide function
     *
     * @param [Integer] $dividend
     * @param [Integer] $divisor
     * @return [interger | Exception] - result of the division
     */
    function divide($dividend, $divisor)
    {
        if ($divisor == 0) {
            throw new Exception("Division by zero", 1);
        }
        return $dividend / $divisor;
    }

    /**
     * method to call functions within this class
     *
     * @return [string] - the result of the methods called
     */
    public function callMethod()
    {
        try {

            $this->divide(5, 0);
        } catch (Exception $ex) {
            $code = $ex->getCode();
            $message = $ex->getMessage();
            $file = $ex->getFile();
            $line = $ex->getLine();
            return "Exception thrown in $file on line $line: [Code $code] $message";
        }
    }
}

$class = new Table();

$class->callMethod();
