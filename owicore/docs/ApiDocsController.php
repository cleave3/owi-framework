<?php

namespace Owi\docs;

use Owi\router\Route;

class ApiDocsController
{
    public function index()
    {
        $routes = Route::getRoutes();
        $filteredRoutes = [];

        foreach ($routes as $method => $paths) {
            // Filter the inner array of paths
            $filtered = array_filter($paths, function ($path) {
                // Must start with /api and NOT be exactly /api/docs
                return str_starts_with($path, '/api') && $path !== '/api/docs';
            }, ARRAY_FILTER_USE_KEY);

            // Only add the method to our results if it still has routes left
            if (!empty($filtered)) {
                $filteredRoutes[$method] = $filtered;
            }
        }
        
        // Extract data for the view
        extract(['routes' => $filteredRoutes]);
        
        // Load view directly from the core docs directory
        require __DIR__ . '/views/api_docs.php';
    }
}
