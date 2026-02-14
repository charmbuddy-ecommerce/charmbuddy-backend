<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// AuthController
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ProductsController
Route::get('/products_index', [ProductController::class, 'index']);
Route::post('/products_store', [ProductController::class, 'store']);

// CategoryController
Route::get('/categories_index', [CategoryController::class, 'index']);
Route::post('/categories_store', [CategoryController::class, 'store']);
