<?php

use Owi\router\Route;
use App\Controllers\PostController;

// API ROUTES
Route::get('/api/posts', [PostController::class, 'apiIndex']);
Route::get('/api/posts/{id}', [PostController::class, 'apiShow']);