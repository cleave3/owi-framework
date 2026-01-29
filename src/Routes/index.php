<?php

use Owi\router\Route;

// Automatically load all route files in this directory
$files = glob(__DIR__ . '/*.php');

// API Docs
Route::get('/api/docs', [\Owi\docs\ApiDocsController::class, 'index']);

foreach ($files as $file) {
    if ($file !== __FILE__) {
        require_once $file;
    }
}
