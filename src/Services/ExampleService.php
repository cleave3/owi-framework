<?php

namespace App\Services;

class ExampleService
{
    /**
     * Perform a business logic operation.
     * 
     * @param string $input
     * @return string
     */
    public function processData($input)
    {
        // Add your business logic here
        return "Processed: " . strtoupper($input);
    }
}
