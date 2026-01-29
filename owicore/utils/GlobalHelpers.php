<?php

if (!function_exists('view')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $path
     * @param  array   $data
     * @return string
     */
    function view($path, $data = [])
    {
        // Convert dot notation to slashes if user uses dots (e.g. posts.index)
        $path = str_replace('.', '/', $path);
        
        // Base View Path
        $basePath = realpath(__DIR__ . '/../../src/Views');
        
        // Full Path
        $fullPath = $basePath . '/' . $path . '.php';

        if (!file_exists($fullPath)) {
            // Try index if path is a directory
            $fullPath = $basePath . '/' . $path . '/index.php';
        }

        if (!file_exists($fullPath)) {
            throw new \Exception("View not found: " . $path);
        }

        // Extract data to variables
        extract($data);

        // Start buffering
        ob_start();

        // Include the view file
        require $fullPath;

        // Return the buffer
        return ob_get_clean();
    }
}
