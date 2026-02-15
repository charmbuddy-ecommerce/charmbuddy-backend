<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ShippingController;
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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
});

Route::get('/categories', [ProductController::class, 'categories']);

Route::prefix('shipping')->group(function () {
    Route::get('/provinces', [ShippingController::class, 'provinces']);
    Route::get('/cities', [ShippingController::class, 'cities']);
    Route::post('/cost', [ShippingController::class, 'cost']);
});

Route::get('/faqs', [FaqController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    });

    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'show']);
        Route::post('/', [CartController::class, 'add']);
        Route::put('/{itemId}', [CartController::class, 'update']);
        Route::delete('/{itemId}', [CartController::class, 'destroy']);
    });

    Route::post('/checkout', [CheckoutController::class, 'store']);
    Route::post('/orders/{orderId}/payment-proof', [CheckoutController::class, 'uploadPaymentProof']);
    Route::get('/orders', [CheckoutController::class, 'index']);
    Route::get('/orders/{orderId}', [CheckoutController::class, 'show']);
    Route::get('/orders/{orderId}/status', [OrderStatusController::class, 'status']);

    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::put('/orders/{orderId}/approve', [OrderStatusController::class, 'approve']);
        Route::put('/orders/{orderId}/reject', [OrderStatusController::class, 'reject']);
        Route::put('/orders/{orderId}/ship', [OrderStatusController::class, 'ship']);
    });
});
