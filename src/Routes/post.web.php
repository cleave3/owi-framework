<?php

use Owi\router\Route;
use App\Controllers\PostController;

// WEB ROUTES
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/create', [PostController::class, 'create']);
Route::post('/posts/store', [PostController::class, 'store']);
Route::get('/posts/{id}', [PostController::class, 'show']);